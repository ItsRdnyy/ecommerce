@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-10">
    <div class="max-w-[1000px] mx-auto px-6 lg:px-10">
        <h1 class="font-serif-display text-[32px] text-gray-900 mb-8">My Orders</h1>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-[13px] px-4 py-3 font-medium">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 text-[13px] px-4 py-3 font-medium">
            {{ session('error') }}
        </div>
        @endif

        @if($orders->count())
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white border border-[#e8e5e0] p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                    <div>
                        <p class="text-[11px] text-gray-500">Order #{{ $order->id }}</p>
                        <p class="text-[13px] font-medium text-gray-900">{{ $order->business->businessProfile?->business_name ?? 'Vendor' }}</p>
                    </div>
                    <div class="text-left sm:text-right mt-2 sm:mt-0">
                        <span class="inline-block text-[10px] font-semibold tracking-[0.1em] uppercase px-2 py-1 border
                            {{ $order->status === 'delivered' || $order->status === 'completed' ? 'border-green-600 text-green-700' : ($order->status === 'cancelled' || $order->status === 'refunded' ? 'border-red-600 text-red-700' : 'border-gray-400 text-gray-600') }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                        <p class="text-[11px] text-gray-500 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-[#e8e5e0]">
                    <div class="text-[12px] text-gray-600">
                        {{ $order->items->count() }} item(s) &middot; {{ ucfirst($order->type) }}
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[14px] font-semibold">₱{{ number_format($order->total, 2) }}</span>
                        @if(in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_CONFIRMED]))
                        <button type="button" onclick="confirmCancellation('{{ route('orders.cancel', $order) }}')" class="text-[11px] font-semibold tracking-[0.12em] uppercase text-red-600 hover:text-red-700 underline">Cancel</button>
                        @endif
                        <a href="{{ route('orders.show', $order) }}" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-800 hover:text-black underline">View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-10">{{ $orders->links() }}</div>
        @else
        <div class="bg-white border border-[#e8e5e0] p-16 text-center">
            <p class="text-[13px] text-gray-600">No orders yet.</p>
        </div>
        @endif
    </div>
</section>
@endsection

<!-- Cancellation Confirmation Modal -->
<div id="cancel-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
    <div class="bg-white border border-[#e8e5e0] max-w-md w-full p-8 shadow-2xl transform scale-95 transition-all duration-300">
        <h3 class="font-serif-display text-[24px] text-gray-900 mb-2">Cancel Order</h3>
        <p class="text-[13px] text-gray-600 mb-6">Are you sure you want to cancel this order? This action cannot be undone and any reserved items will be released back to stock.</p>
        
        <div class="flex items-center justify-end gap-4">
            <button id="cancel-modal-close" type="button" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500 hover:text-black py-2 px-4 transition-colors">
                No, Keep Order
            </button>
            <form id="cancel-modal-form" method="POST" action="">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[11px] font-semibold tracking-[0.12em] uppercase py-3 px-6 transition-colors">
                    Yes, Cancel Order
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmCancellation(actionUrl) {
        const modal = document.getElementById('cancel-modal');
        const form = document.getElementById('cancel-modal-form');
        
        form.action = actionUrl;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('cancel-modal');
        const closeBtn = document.getElementById('cancel-modal-close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            });
        }
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBtn.click();
                }
            });
        }
    });
</script>
@endpush
