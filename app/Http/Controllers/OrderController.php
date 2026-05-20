<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with('business.businessProfile', 'items.product')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->buyer_id !== auth()->id() && $order->business_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product', 'items.variant', 'payments', 'invoices', 'shipments.timelines', 'disputes');

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancellation if order is in pending or confirmed status
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $transitioned = \App\Services\OrderStateMachine::transition($order, Order::STATUS_CANCELLED, auth()->id(), 'Cancelled by customer');

        if ($transitioned) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'Failed to cancel the order.');
    }

    public function receive(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_SHIPPED) {
            return back()->with('error', 'This order cannot be marked as received.');
        }

        $transitioned = \App\Services\OrderStateMachine::transition($order, Order::STATUS_DELIVERED, auth()->id(), 'Order received by buyer');

        if ($transitioned) {
            return back()->with('success', 'Thank you! The order has been marked as delivered.');
        }

        return back()->with('error', 'Failed to update order status.');
    }

    public function storeReview(Order $order, Product $product, Request $request)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED])) {
            return back()->with('error', 'You can only review products from delivered or completed orders.');
        }

        // Verify product belongs to this order
        $existsInOrder = $order->items()->where('product_id', $product->id)->exists();
        if (!$existsInOrder) {
            return back()->with('error', 'This product was not purchased in this order.');
        }

        // Prevent duplicate reviews
        $alreadyReviewed = Review::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->exists();
        if ($alreadyReviewed) {
            return back()->with('error', 'You have already reviewed this product for this order.');
        }

        // Validate
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Create review
        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'approved', // Automatically approve reviews for immediate display
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted successfully.');
    }
}
