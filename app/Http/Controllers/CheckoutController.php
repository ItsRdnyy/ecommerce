<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\DiscountEngine;
use App\Services\ShippingCalculator;
use App\Services\InventoryManager;
use App\Services\OrderStateMachine;
use App\Services\PaymentProcessor;
use App\Services\NotificationService;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product.business.businessProfile', 'items.variant')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cart->recalculate();

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address.name' => 'required|string',
            'shipping_address.line1' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postal' => 'required|string',
            'shipping_address.country' => 'required|string',
            'billing_address' => 'nullable|array',
            'payment_method' => 'required|in:stripe,paypal,bank_transfer,cod',
            'notes' => 'nullable|string',
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product.variants')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Cart is empty.');
        }

        DB::beginTransaction();
        try {
            $grouped = $cart->items->groupBy(fn($item) => $item->product->business_id);
            $orders = [];

            foreach ($grouped as $businessId => $items) {
                $subtotal = 0;
                $discountTotal = 0;
                $shippingTotal = 0;

                // Validate stock for all items before creating order
                foreach ($items as $item) {
                    $product = $item->product;
                    $size = $item->size;

                    if ($size) {
                        $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);

                        if (!$variant || $variant->stock < $item->quantity) {
                            $available = $variant ? $variant->stock : 0;
                            throw new \Exception("Insufficient stock for {$product->name} (size {$size}). Available: {$available}");
                        }
                    } else {
                        if ($product->stock < $item->quantity) {
                            throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock}");
                        }
                    }
                }

                foreach ($items as $item) {
                    $product = $item->product;
                    $calc = DiscountEngine::calculate($product, $item->quantity, $item->type);
                    $shipping = ShippingCalculator::calculateForProduct(
                        $product,
                        ($product->weight ?? 0.5) * $item->quantity
                    );

                    $subtotal += $calc['base_price'] * $item->quantity;
                    $discountTotal += $calc['discount_amount'];
                    $shippingTotal += $shipping;
                }

                $commissionRate = (float) \App\Models\Setting::get('commission_rate', 10);
                $platformFee = (float) \App\Models\Setting::get('platform_fee', 2.50);
                $total = ($subtotal - $discountTotal) + $shippingTotal + $platformFee;
                $commission = ($subtotal - $discountTotal) * ($commissionRate / 100);

                $orderType = $cart->items->contains(fn($item) => $item->type === 'wholesale') ? 'b2b' : 'retail';

                $order = Order::create([
                    'buyer_id' => auth()->id(),
                    'business_id' => $businessId,
                    'type' => $orderType,
                    'status' => Order::STATUS_PENDING,
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'shipping_fee' => $shippingTotal,
                    'platform_fee' => $platformFee,
                    'commission' => $commission,
                    'total' => $total,
                    'shipping_address' => $validated['shipping_address'],
                    'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
                    'notes' => $validated['notes'] ?? null,
                    'estimated_delivery_date' => ShippingCalculator::estimateDeliveryDate(),
                ]);

                foreach ($items as $item) {
                    $product = $item->product;
                    $size = $item->size;
                    $calc = DiscountEngine::calculate($product, $item->quantity, $item->type);

                    // Find variant if size is selected
                    $variant = null;
                    if ($size) {
                        $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'variant_name' => $variant ? $variant->name : null,
                        'quantity' => $item->quantity,
                        'price' => $calc['unit_price'],
                        'original_price' => $calc['base_price'],
                        'discount_amount' => $calc['discount_amount'],
                        'shipping_fee' => ShippingCalculator::calculateForProduct(
                            $product,
                            ($product->weight ?? 0.5) * $item->quantity
                        ),
                    ]);

                    // Deduct stock from variant or product
                    if ($variant) {
                        $variant->stock -= $item->quantity;
                        $variant->save();
                    } else {
                        $product->stock -= $item->quantity;
                        $product->save();
                    }
                }

                Shipment::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'destination_address' => $validated['shipping_address'],
                    'estimated_delivery_date' => $order->estimated_delivery_date,
                ]);

                $orders[] = $order;

                if (in_array($validated['payment_method'], ['cod', 'bank_transfer'])) {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => auth()->id(),
                        'type' => 'full',
                        'amount' => 0,
                        'balance_due' => $total,
                        'method' => $validated['payment_method'],
                        'status' => 'pending',
                    ]);
                } else {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => auth()->id(),
                        'type' => 'full',
                        'amount' => $total,
                        'balance_due' => 0,
                        'method' => $validated['payment_method'],
                        'status' => 'pending',
                    ]);
                }
            }

            $cart->items()->delete();
            $cart->recalculate();

            DB::commit();

            $firstOrder = $orders[0];
            return redirect()->route('orders.show', $firstOrder)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function pay(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:stripe,paypal,bank_transfer',
        ]);

        $amount = (float) $request->amount;
        $method = $request->method;

        PaymentProcessor::process($order, $method, $amount);

        NotificationService::notifyPaymentConfirmation(auth()->user(), $order->id, $amount);

        return back()->with('success', 'Payment processed successfully.');
    }
}
