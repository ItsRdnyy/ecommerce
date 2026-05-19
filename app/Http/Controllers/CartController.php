<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\DiscountEngine;
use App\Services\ShippingCalculator;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.category');
        $cart->recalculate();

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'size' => 'nullable|string|max:10',
        ]);

        $product = Product::with('category')->findOrFail($request->product_id);
        $quantity = (int) $request->quantity;
        $size = $request->size;

        // Determine if size is required based on category
        $categoryName = strtolower($product->category->name ?? '');
        $isApparel = str_contains($categoryName, 'clothing') || str_contains($categoryName, 'shirt') || str_contains($categoryName, 'pants') || str_contains($categoryName, 'dress') || str_contains($categoryName, 'apparel');
        $isShoe = str_contains($categoryName, 'shoe') || str_contains($categoryName, 'footwear') || str_contains($categoryName, 'sneaker') || str_contains($categoryName, 'boot');

        if (($isApparel || $isShoe) && empty($size)) {
            return $this->jsonOrRedirect($request, 'Please select a size.', false, 422);
        }

        // Check if product is in stock
        if ($product->stock < $quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock
                ], 400);
            }
            return back()->with('error', 'Insufficient stock. Available: ' . $product->stock);
        }

        $cart = $this->getOrCreateCart();

        // Check if product with the same size already exists in cart
        $existingQuery = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id);

        if ($size) {
            $existingQuery->where('size', $size);
        } else {
            $existingQuery->whereNull('size');
        }

        $existing = $existingQuery->first();

        if ($existing) {
            $newQuantity = $existing->quantity + $quantity;

            if ($product->stock < $newQuantity) {
                return $this->jsonOrRedirect($request, 'Insufficient stock. Available: ' . $product->stock, false, 400);
            }

            $itemType = $this->resolveCartItemType($product, $newQuantity);
            $existing->update(array_merge(
                $this->cartItemPayload($product, $newQuantity, $itemType),
                ['size' => $size]
            ));
        } else {
            $itemType = $this->resolveCartItemType($product, $quantity);
            CartItem::create(array_merge([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'size' => $size,
            ], $this->cartItemPayload($product, $quantity, $itemType)));
        }

        $cart->recalculate();

        return $this->jsonOrRedirect($request, 'Added to cart successfully', true, 200, [
            'cart_count' => $cart->items->sum('quantity'),
            'cart_total' => number_format($cart->total, 2),
        ]);
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $product = $item->product;
        $quantity = (int) $request->quantity;

        if ($product->stock < $quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock
                ], 400);
            }
            return back()->with('error', 'Insufficient stock. Available: ' . $product->stock);
        }

        $itemType = $this->resolveCartItemType($product, $quantity);
        $item->update($this->cartItemPayload($product, $quantity, $itemType));

        $item->cart->recalculate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => $item->cart->items->sum('quantity'),
                'cart_total' => number_format($item->cart->total, 2),
                'item_total' => number_format($item->quantity * $item->unit_price, 2)
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    private function resolveCartItemType(Product $product, int $quantity): string
    {
        $user = auth()->user();

        if ($user && $user->isBusiness() && $product->is_wholesale_enabled && $product->wholesale_price > 0) {
            if (DiscountEngine::validateMoq($product, $quantity, 'wholesale')) {
                return 'wholesale';
            }
        }

        return 'retail';
    }

    private function cartItemPayload(Product $product, int $quantity, string $type): array
    {
        $calculation = DiscountEngine::calculate($product, $quantity, $type);

        return [
            'quantity' => $quantity,
            'unit_price' => $calculation['unit_price'],
            'discount_amount' => $calculation['discount_amount'],
            'shipping_estimate' => ShippingCalculator::calculateForProduct($product, ($product->weight ?? 0.5) * $quantity),
            'type' => $type,
        ];
    }

    private function jsonOrRedirect(Request $request, string $message, bool $success, int $status = 200, array $data = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge(['success' => $success, 'message' => $message], $data), $status);
        }

        if ($success) {
            return back()->with('success', $message);
        }

        return back()->with('error', $message);
    }

    public function remove(CartItem $item)
    {
        $cart = $item->cart;
        $item->delete();
        $cart->recalculate();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $cart->items->sum('quantity'),
                'cart_total' => number_format($cart->total, 2)
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        $cart->recalculate();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared',
                'cart_count' => 0,
                'cart_total' => '0.00'
            ]);
        }

        return back()->with('success', 'Cart cleared.');
    }

    public function getCount()
    {
        $cart = $this->getOrCreateCart();
        return response()->json([
            'count' => $cart->items->sum('quantity')
        ]);
    }

    private function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id()],
            ['type' => 'retail', 'total' => 0, 'discount_total' => 0, 'shipping_total' => 0]
        );
    }
}
