<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

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
}
