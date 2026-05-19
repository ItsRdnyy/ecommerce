@extends('layouts.app')

@section('title', 'Order #'.$order->id)

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-10">
    <div class="max-w-[800px] mx-auto px-6 lg:px-10">
        <a href="{{ route('orders.index') }}" class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-600 hover:text-black mb-6 inline-block">&larr; Back to Orders</a>
        <h1 class="font-serif-display text-[32px] text-gray-900 mb-2">Order #{{ $order->id }}</h1>
        <p class="text-[12px] text-gray-600 mb-8">Placed on {{ $order->created_at->format('F d, Y') }}</p>

        <div class="bg-white border border-[#e8e5e0] p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <span class="inline-block text-[10px] font-semibold tracking-[0.1em] uppercase px-2 py-1 border
                    {{ $order->status === 'delivered' || $order->status === 'completed' ? 'border-green-600 text-green-700' : ($order->status === 'cancelled' || $order->status === 'refunded' ? 'border-red-600 text-red-700' : 'border-gray-400 text-gray-600') }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
                <span class="text-[11px] text-gray-500">{{ ucfirst($order->type) }}</span>
            </div>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-start gap-4 py-4 border-b border-[#e8e5e0] last:border-b-0">
                    <div class="w-16 h-16 bg-gray-100 shrink-0">
                        @if($item->product->image)
                            <img src="{{ asset('storage/'.$item->product->image) }}" class="w-full h-full object-cover" alt="">
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-[14px] font-medium text-gray-900">{{ $item->product->name }}</p>
                        @if($item->variant_name)
                            <p class="text-[11px] text-gray-500">{{ $item->variant_name }}</p>
                        @endif
                        <p class="text-[11px] text-gray-500">Qty: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[13px] font-semibold text-gray-900">₱{{ number_format($item->price * $item->quantity, 2) }}</p>
                        @if($item->discount_amount > 0)
                            <p class="text-[11px] text-green-700 font-medium">Saved ₱{{ number_format($item->discount_amount, 2) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-[#e8e5e0]">
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium text-gray-900">₱{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_total > 0)
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-green-700">Discounts</span>
                    <span class="text-green-700 font-medium">-₱{{ number_format($order->discount_total, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-medium text-gray-900">₱{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-gray-600">Platform Fee</span>
                    <span class="font-medium text-gray-900">₱{{ number_format($order->platform_fee, 2) }}</span>
                </div>
                <div class="flex justify-between text-[18px] font-semibold text-gray-900 pt-3 border-t border-[#e8e5e0]">
                    <span>Total</span>
                    <span class="text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($order->shipments->count())
        <div class="bg-white border border-[#e8e5e0] p-6 mb-6">
            <h3 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-4">Shipment</h3>
            @foreach($order->shipments as $shipment)
                <p class="text-[13px] mb-2"><span class="text-gray-500">Courier:</span> {{ $shipment->courier ?? 'N/A' }}</p>
                <p class="text-[13px] mb-2"><span class="text-gray-500">Tracking:</span> {{ $shipment->tracking_number ?? 'N/A' }}</p>
                @if($shipment->timelines->count())
                <div class="mt-4 space-y-3">
                    @foreach($shipment->timelines as $tl)
                    <div class="flex gap-4 text-[12px]">
                        <span class="text-gray-500 shrink-0 w-24">{{ $tl->timestamp->format('M d, H:i') }}</span>
                        <span class="font-medium">{{ ucfirst($tl->status) }}</span>
                        <span class="text-gray-600">{{ $tl->location }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
        @endif

        @if($order->payments->count())
        <div class="bg-white border border-[#e8e5e0] p-6">
            <h3 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-4">Payments</h3>
            <div class="space-y-3">
                @foreach($order->payments as $payment)
                <div class="flex justify-between text-[13px]">
                    <span class="text-gray-600">{{ ucfirst($payment->type) }} via {{ ucfirst($payment->method) }}</span>
                    <span class="font-medium text-gray-900">₱{{ number_format(abs($payment->amount), 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
