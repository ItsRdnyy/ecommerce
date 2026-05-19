@extends('layouts.app')

@section('title', 'Shopping Cart - PureFit Apparel')

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-16 lg:py-20">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
        <div class="text-center mb-12">
            <h1 class="text-[32px] sm:text-[40px] font-semibold text-gray-900 mb-4">Shopping Cart</h1>
            <p class="text-[16px] text-gray-600">Review your items before checkout</p>
        </div>

        @if($cart && $cart->items->count())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                <div class="cart-item bg-white border border-[#e8e5e0] rounded-2xl p-6" data-item-id="{{ $item->id }}">
                    <div class="flex gap-6">
                        <!-- Product Image -->
                        <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                            @if($item->product->image)
                                <img src="{{ asset('storage/'.$item->product->image) }}" 
                                     class="w-full h-full object-cover" 
                                     alt="{{ $item->product->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400 text-[12px]">No Image</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1">
                            <h3 class="text-[16px] font-semibold text-gray-900 mb-2">{{ $item->product->name }}</h3>
                            <p class="text-[14px] text-gray-600 mb-1">{{ $item->product->category->name ?? 'Uncategorized' }}</p>
                            @if($item->size)
                            <p class="text-[13px] text-gray-700 mb-1"><span class="font-medium">Size:</span> {{ $item->size }}</p>
                            @endif
                            <p class="text-[12px] uppercase tracking-[0.12em] text-gray-500 mb-3">{{ $item->type === 'wholesale' ? 'Wholesale purchase' : 'Retail purchase' }}</p>
                            
                            <!-- Quantity Selector -->
                            <div class="flex items-center gap-4 mb-3">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button class="qty-decrease px-3 py-2 text-gray-600 hover:text-black hover:bg-gray-100 transition-colors" data-item-id="{{ $item->id }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           name="quantity" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           max="99"
                                           class="quantity-input w-12 bg-transparent text-center text-[14px] font-medium text-gray-900 focus:outline-none"
                                           data-item-id="{{ $item->id }}">
                                    <button class="qty-increase px-3 py-2 text-gray-600 hover:text-black hover:bg-gray-100 transition-colors" data-item-id="{{ $item->id }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                <button class="remove-item text-[12px] text-red-600 hover:text-red-800 font-medium transition-colors" data-item-id="{{ $item->id }}">
                                    Remove
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="flex items-center justify-between">
                                <p class="text-[18px] font-semibold text-gray-900 item-total">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                                <p class="text-[12px] text-gray-500">₱{{ number_format($item->unit_price, 2) }} each</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-[#e8e5e0] rounded-2xl p-6 sticky top-8">
                    <h2 class="text-[18px] font-semibold text-gray-900 mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-[14px]">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">₱{{ number_format($cart->total + $cart->discount_total, 2) }}</span>
                        </div>
                        @if($cart->discount_total > 0)
                        <div class="flex justify-between text-[14px]">
                            <span class="text-green-700">Discounts</span>
                            <span class="font-medium text-green-700">-₱{{ number_format($cart->discount_total, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-[14px]">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-gray-900">₱{{ number_format($cart->shipping_total, 2) }}</span>
                        </div>
                        <div class="border-t border-[#e8e5e0] pt-4">
                            <div class="flex justify-between text-[20px] font-semibold text-gray-900">
                                <span>Total</span>
                                <span>₱{{ number_format($cart->total + $cart->shipping_total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('checkout.index') }}" 
                           class="block w-full bg-gray-900 text-white text-center text-[12px] font-semibold tracking-[0.12em] uppercase py-4 hover:bg-gray-800 transition-colors rounded-lg">
                            Proceed to Checkout
                        </a>
                        <button id="clear-cart" class="w-full border border-gray-300 text-gray-900 text-[12px] font-semibold tracking-[0.12em] uppercase py-4 hover:bg-gray-100 transition-colors rounded-lg">
                            Clear Cart
                        </button>
                        <a href="{{ route('products') }}" 
                           class="block w-full text-center text-[12px] text-gray-600 hover:text-black transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white border border-[#e8e5e0] rounded-2xl p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <h2 class="text-[24px] font-semibold text-gray-900 mb-3">Your cart is empty</h2>
            <p class="text-[14px] text-gray-600 mb-8">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products') }}" 
               class="inline-block bg-gray-900 text-white text-[12px] font-semibold tracking-[0.12em] uppercase py-4 px-8 hover:bg-gray-800 transition-colors rounded-lg">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Cart Page JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupCartPage();
    });

    function setupCartPage() {
        // Quantity decrease buttons
        document.querySelectorAll('.qty-decrease').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt(input.value);
                
                if (currentValue > 1) {
                    updateQuantity(itemId, currentValue - 1);
                }
            });
        });

        // Quantity increase buttons
        document.querySelectorAll('.qty-increase').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt(input.value);
                
                if (currentValue < 99) {
                    updateQuantity(itemId, currentValue + 1);
                }
            });
        });

        // Quantity input change
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                let value = parseInt(this.value);
                
                if (value < 1) value = 1;
                if (value > 99) value = 99;
                
                this.value = value;
                updateQuantity(itemId, value);
            });
        });

        // Remove item buttons
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                if (confirm('Are you sure you want to remove this item?')) {
                    removeItem(itemId);
                }
            });
        });

        // Clear cart button
        const clearCartBtn = document.getElementById('clear-cart');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear your cart?')) {
                    clearCart();
                }
            });
        }
    }

    async function updateQuantity(itemId, quantity) {
        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity: quantity })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update cart count
                updateCartCountDisplay(data.cart_count);
                
                // Update item total
                const itemTotal = document.querySelector(`.cart-item[data-item-id="${itemId}"] .item-total`);
                if (itemTotal) {
                    itemTotal.textContent = '₱' + data.item_total;
                }
                
                // Update cart total
                location.reload(); // Reload to update totals
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            showNotification('Error updating quantity', 'error');
        }
    }

    async function removeItem(itemId) {
        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update cart count
                updateCartCountDisplay(data.cart_count);
                
                // Remove item from DOM
                const item = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                if (item) {
                    item.remove();
                }
                
                // Reload if cart is empty
                if (data.cart_count === 0) {
                    location.reload();
                } else {
                    location.reload(); // Reload to update totals
                }
                
                showNotification(data.message, 'success');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showNotification('Error removing item', 'error');
        }
    }

    async function clearCart() {
        try {
            const response = await fetch('/cart/clear', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateCartCountDisplay(0);
                location.reload();
                showNotification(data.message, 'success');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            showNotification('Error clearing cart', 'error');
        }
    }

    function updateCartCountDisplay(count) {
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            if (count > 0) {
                cartCountEl.textContent = count;
                cartCountEl.classList.remove('hidden');
            } else {
                cartCountEl.classList.add('hidden');
            }
        }
    }
</script>
@endsection
