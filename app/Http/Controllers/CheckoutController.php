<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartItem;
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
        if (session()->has('buy_now')) {
            $buyNowData = session('buy_now');
            
            $product = Product::with(['category', 'variants', 'business.businessProfile'])
                ->findOrFail($buyNowData['product_id']);
                
            $quantity = $buyNowData['quantity'];
            $size = $buyNowData['size'];
            
            // Build a temporary Cart & CartItem
            $cart = new Cart([
                'user_id' => auth()->id(),
                'type' => 'retail',
                'total' => 0,
                'discount_total' => 0,
                'shipping_total' => 0
            ]);
            
            $itemType = $product->is_wholesale_enabled && $product->wholesale_price > 0 && DiscountEngine::validateMoq($product, $quantity, 'wholesale') ? 'wholesale' : 'retail';
            $calc = DiscountEngine::calculate($product, $quantity, $itemType, $size);
            $shippingEstimate = ShippingCalculator::calculateForProduct($product, ($product->weight ?? 0.5) * $quantity);
            
            $item = new CartItem([
                'product_id' => $product->id,
                'size' => $size,
                'quantity' => $quantity,
                'unit_price' => $calc['unit_price'],
                'discount_amount' => $calc['discount_amount'],
                'shipping_estimate' => $shippingEstimate,
                'type' => $itemType
            ]);
            
            // Link them in-memory
            $item->setRelation('product', $product);
            
            if ($size) {
                $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                if ($variant) {
                    $item->setRelation('variant', $variant);
                }
            }
            
            $cart->setRelation('items', collect([$item]));
            $cart->recalculate();
            
            return view('checkout.index', compact('cart'));
        }

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
            'shipping_address.contact' => 'required|string',
            'billing_address' => 'nullable|array',
            'payment_method' => 'required|in:stripe,paypal,bank_transfer,cod',
            'notes' => 'nullable|string',
        ]);

        $isBuyNow = session()->has('buy_now');
        
        if ($isBuyNow) {
            $buyNowData = session('buy_now');
            $product = Product::with(['variants', 'business.businessProfile'])->findOrFail($buyNowData['product_id']);
            $quantity = $buyNowData['quantity'];
            $size = $buyNowData['size'];
            
            $itemType = $product->is_wholesale_enabled && $product->wholesale_price > 0 && DiscountEngine::validateMoq($product, $quantity, 'wholesale') ? 'wholesale' : 'retail';
            $calc = DiscountEngine::calculate($product, $quantity, $itemType, $size);
            $shippingEstimate = ShippingCalculator::calculateForProduct($product, ($product->weight ?? 0.5) * $quantity);
            
            $item = new CartItem([
                'product_id' => $product->id,
                'size' => $size,
                'quantity' => $quantity,
                'unit_price' => $calc['unit_price'],
                'discount_amount' => $calc['discount_amount'],
                'shipping_estimate' => $shippingEstimate,
                'type' => $itemType
            ]);
            $item->setRelation('product', $product);
            $items = collect([$item]);
        } else {
            $cart = Cart::where('user_id', auth()->id())
                ->with('items.product.variants')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return back()->with('error', 'Cart is empty.');
            }
            $items = $cart->items;
        }

        DB::beginTransaction();
        try {
            $grouped = $items->groupBy(fn($item) => $item->product->business_id);
            $orders = [];

            foreach ($grouped as $businessId => $groupedItems) {
                $subtotal = 0;
                $discountTotal = 0;
                $shippingTotal = 0;

                // Validate stock for all items before creating order
                foreach ($groupedItems as $item) {
                    $itemProduct = $item->product;
                    $itemSize = $item->size;

                    if ($itemSize) {
                        $variant = $itemProduct->variants->first(fn($v) => data_get($v->attributes, 'size') == $itemSize);

                        if (!$variant || $variant->stock < $item->quantity) {
                            $available = $variant ? $variant->stock : 0;
                            throw new \Exception("Insufficient stock for {$itemProduct->name} (size {$itemSize}). Available: {$available}");
                        }
                    } else {
                        if ($itemProduct->stock < $item->quantity) {
                            throw new \Exception("Insufficient stock for {$itemProduct->name}. Available: {$itemProduct->stock}");
                        }
                    }
                }

                foreach ($groupedItems as $item) {
                    $itemProduct = $item->product;
                    $calc = DiscountEngine::calculate($itemProduct, $item->quantity, $item->type, $item->size);
                    $shipping = ShippingCalculator::calculateForProduct(
                        $itemProduct,
                        ($itemProduct->weight ?? 0.5) * $item->quantity
                    );

                    $subtotal += $calc['base_price'] * $item->quantity;
                    $discountTotal += $calc['discount_amount'];
                    $shippingTotal += $shipping;
                }

                $commissionRate = (float) \App\Models\Setting::get('commission_rate', 10);
                $platformFee = (float) \App\Models\Setting::get('platform_fee', 2.50);
                $total = ($subtotal - $discountTotal) + $shippingTotal + $platformFee;
                $commission = ($subtotal - $discountTotal) * ($commissionRate / 100);

                $orderType = $groupedItems->contains(fn($item) => $item->type === 'wholesale') ? 'b2b' : 'retail';

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

                foreach ($groupedItems as $item) {
                    $itemProduct = $item->product;
                    $itemSize = $item->size;
                    $calc = DiscountEngine::calculate($itemProduct, $item->quantity, $item->type, $itemSize);

                    // Find variant if size is selected
                    $variant = null;
                    if ($itemSize) {
                        $variant = $itemProduct->variants->first(fn($v) => data_get($v->attributes, 'size') == $itemSize);
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemProduct->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'variant_name' => $variant ? $variant->name : null,
                        'quantity' => $item->quantity,
                        'price' => $calc['unit_price'],
                        'original_price' => $calc['base_price'],
                        'discount_amount' => $calc['discount_amount'],
                        'shipping_fee' => ShippingCalculator::calculateForProduct(
                            $itemProduct,
                            ($itemProduct->weight ?? 0.5) * $item->quantity
                        ),
                    ]);

                    // Deduct stock from variant or product
                    if ($variant) {
                        $variant->stock -= $item->quantity;
                        $variant->save();
                    } else {
                        $itemProduct->stock -= $item->quantity;
                        $itemProduct->save();
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

            if ($isBuyNow) {
                // Clear the buy now session
                session()->forget('buy_now');
            } else {
                // Clear normal cart
                $cart->items()->delete();
                $cart->recalculate();
            }

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
