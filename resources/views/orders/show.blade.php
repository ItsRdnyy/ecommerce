@extends('layouts.app')

@section('title', 'Order #'.$order->id)

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-10">
    <div class="max-w-[800px] mx-auto px-6 lg:px-10">
        <a href="{{ route('orders.index') }}" class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-600 hover:text-black mb-6 inline-block">&larr; Back to Orders</a>
        <h1 class="font-serif-display text-[32px] text-gray-900 mb-2">Order #{{ $order->id }}</h1>
        <p class="text-[12px] text-gray-600 mb-8">Placed on {{ $order->created_at->format('F d, Y') }}</p>

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

                        @php
                            $showReviewButton = in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]);
                            $review = null;
                            if ($showReviewButton) {
                                $review = \App\Models\Review::where('order_id', $order->id)
                                    ->where('product_id', $item->product_id)
                                    ->first();
                            }
                        @endphp

                        @if($showReviewButton)
                            @if($review)
                                <div class="mt-3 flex flex-col gap-1 items-start bg-[#faf9f7] border border-[#e8e5e0] p-3 max-w-md">
                                    <div class="flex items-center gap-2">
                                        <div class="flex text-yellow-500">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="inline-block text-[9px] font-semibold tracking-wider uppercase text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">Reviewed</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-[12px] text-gray-600 mt-1 italic">"{{ $review->comment }}"</p>
                                    @endif
                                </div>
                            @else
                                <div class="mt-2">
                                    <button type="button" 
                                            onclick="openReviewModal('{{ $item->product->id }}', '{{ addslashes($item->product->name) }}', '{{ route('orders.review.store', [$order, $item->product]) }}')"
                                            class="border border-black text-black hover:bg-black hover:text-white text-[10px] font-semibold tracking-[0.1em] uppercase py-1.5 px-3.5 transition-all duration-200">
                                        Write Review
                                    </button>
                                </div>
                            @endif
                        @endif
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

        <div class="bg-white border border-[#e8e5e0] p-6 mb-6">
            <h3 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-4">Checkout Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Shipping Address</h4>
                    @if($order->shipping_address)
                        <p class="text-[13px] font-medium text-gray-900">{{ $order->shipping_address['name'] ?? '—' }}</p>
                        <p class="text-[12px] text-gray-600 mt-1 leading-relaxed">
                            {{ $order->shipping_address['line1'] ?? '' }}<br>
                            {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal'] ?? '' }}
                            @if(!empty($order->shipping_address['contact']))
                                <br>Contact: {{ $order->shipping_address['contact'] }}
                            @elseif(!empty($order->shipping_address['country']))
                                <br>{{ $order->shipping_address['country'] }}
                            @endif
                        </p>
                    @else
                        <p class="text-[12px] text-gray-500">No shipping address provided.</p>
                    @endif
                </div>
                
            </div>
            @if($order->notes)
            <div class="mt-6 pt-6 border-t border-[#e8e5e0]">
                <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Order Notes</h4>
                <p class="text-[13px] text-gray-600 italic bg-[#faf9f7] p-3 border border-[#e8e5e0]">"{{ $order->notes }}"</p>
            </div>
            @endif
        </div>

        @if($order->status === \App\Models\Order::STATUS_SHIPPED)
        <div class="bg-white border border-[#e8e5e0] p-6 mb-6">
            <h3 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-4">Confirm Delivery</h3>
            <p class="text-[13px] text-gray-600 mb-6">Please click below only after you have received your parcel and verified that all items are correct.</p>
            <form method="POST" action="{{ route('orders.receive', $order) }}">
                @csrf
                <button type="submit" class="w-full bg-black hover:bg-black/90 text-white text-[11px] font-semibold tracking-[0.12em] uppercase py-3 transition-colors">
                    Order Received
                </button>
            </form>
        </div>
        @endif

        @if(in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_CONFIRMED]))
        <div class="bg-white border border-[#e8e5e0] p-6 mb-6">
            <h3 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-red-600 mb-4">Danger Zone</h3>
            <p class="text-[13px] text-gray-600 mb-4">If you cancel this order, any reserved inventory will be released immediately and this action cannot be undone.</p>
            <button type="button" onclick="confirmCancellation('{{ route('orders.cancel', $order) }}')" class="w-full border border-red-600 text-red-600 text-[11px] font-semibold tracking-[0.12em] uppercase py-3 hover:bg-red-50 transition-colors">Cancel Order</button>
        </div>
        @endif

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
                    <span class="font-medium text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                </div>
                @endforeach
            </div>
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

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/25 hidden opacity-0 transition-all duration-300">
    <div class="bg-white border border-[#e8e5e0] max-w-[390px] w-full p-6 shadow-sm transform scale-95 transition-all duration-300">
        <div class="flex items-center justify-between mb-4 pb-3">
            <h3 class="text-[12px] font-semibold tracking-[0.15em] uppercase text-gray-900" id="review-modal-title">Write a Review</h3>
            <button id="review-modal-close-icon" type="button" class="text-gray-400 hover:text-black focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="review-modal-form" method="POST" action="">
            @csrf
            
            <!-- Star Rating Select -->
            <div class="mb-5">
                <label class="block text-[10px] font-semibold tracking-[0.12em] uppercase text-gray-400 mb-2">Rating <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-1.5" id="star-rating-container">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" data-rating="{{ $i }}" class="star-rating-btn text-gray-300 focus:outline-none">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="review-rating-input" value="">
                <span class="text-[10px] text-red-500 font-medium hidden mt-1.5 block" id="review-rating-error">Please select a rating.</span>
            </div>
            
            <!-- Comment Textarea -->
            <div class="mb-5">
                <label for="review-comment" class="block text-[10px] font-semibold tracking-[0.12em] uppercase text-gray-400 mb-2">Comment (Optional)</label>
                <textarea id="review-comment" name="comment" rows="4" maxlength="1000" placeholder="Share your thoughts about this product..." class="w-full px-3 py-2 border border-[#e8e5e0] text-[12px] text-gray-900 bg-transparent focus:outline-none focus:border-black resize-none placeholder-gray-400/80"></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-3">
                <button id="review-modal-close" type="button" class="text-[10px] font-semibold tracking-[0.12em] uppercase text-gray-400 hover:text-black py-2 px-3 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-black hover:bg-black/90 text-white text-[10px] font-semibold tracking-[0.12em] uppercase py-2.5 px-4 transition-colors">
                    Submit Review
                </button>
            </div>
        </form>
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

    function openReviewModal(productId, productName, actionUrl) {
        const modal = document.getElementById('review-modal');
        const form = document.getElementById('review-modal-form');
        const title = document.getElementById('review-modal-title');
        
        form.action = actionUrl;
        title.textContent = `Review: ${productName}`;
        
        // Reset form
        form.reset();
        document.getElementById('review-rating-input').value = '';
        const ratingError = document.getElementById('review-rating-error');
        if (ratingError) ratingError.classList.add('hidden');
        resetStars();
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function resetStars() {
        const buttons = document.querySelectorAll('.star-rating-btn');
        buttons.forEach(btn => {
            btn.classList.remove('text-black');
            btn.classList.add('text-gray-300');
        });
    }

    function highlightStars(rating) {
        const buttons = document.querySelectorAll('.star-rating-btn');
        buttons.forEach(btn => {
            const btnRating = parseInt(btn.dataset.rating);
            if (btnRating <= rating) {
                btn.classList.remove('text-gray-300');
                btn.classList.add('text-black');
            } else {
                btn.classList.remove('text-black');
                btn.classList.add('text-gray-300');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Cancellation Modal
        const cancelModal = document.getElementById('cancel-modal');
        const cancelCloseBtn = document.getElementById('cancel-modal-close');
        
        if (cancelCloseBtn && cancelModal) {
            cancelCloseBtn.addEventListener('click', function() {
                cancelModal.classList.add('opacity-0');
                cancelModal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    cancelModal.classList.add('hidden');
                }, 300);
            });
            
            cancelModal.addEventListener('click', function(e) {
                if (e.target === cancelModal) {
                    cancelCloseBtn.click();
                }
            });
        }

        // Review Modal
        const reviewModal = document.getElementById('review-modal');
        const reviewCloseBtn = document.getElementById('review-modal-close');
        const reviewCloseIconBtn = document.getElementById('review-modal-close-icon');
        const starInput = document.getElementById('review-rating-input');
        const ratingError = document.getElementById('review-rating-error');
        const form = document.getElementById('review-modal-form');
        
        function closeReviewModal() {
            if (reviewModal) {
                reviewModal.classList.add('opacity-0');
                reviewModal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    reviewModal.classList.add('hidden');
                    reviewModal.classList.remove('flex');
                }, 300);
            }
        }
        
        if (reviewCloseBtn) {
            reviewCloseBtn.addEventListener('click', closeReviewModal);
        }
        if (reviewCloseIconBtn) {
            reviewCloseIconBtn.addEventListener('click', closeReviewModal);
        }
        
        if (reviewModal) {
            reviewModal.addEventListener('click', function(e) {
                if (e.target === reviewModal) {
                    closeReviewModal();
                }
            });
        }
        
        // Star interactive behavior
        const starBtns = document.querySelectorAll('.star-rating-btn');
        starBtns.forEach(btn => {
            // Click to lock rating
            btn.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                starInput.value = rating;
                highlightStars(rating);
                if (ratingError) {
                    ratingError.classList.add('hidden');
                }
            });
        });
        
        // Form submission validation
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!starInput.value) {
                    e.preventDefault();
                    if (ratingError) {
                        ratingError.classList.remove('hidden');
                    }
                }
            });
        }
    });
</script>
@endpush
