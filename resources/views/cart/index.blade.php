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
                            @if($item->product->variants->isNotEmpty() && $item->product->variants->pluck('attributes.size')->filter()->isNotEmpty())
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[13px] font-medium text-gray-700">Size:</span>
                                <select class="size-select bg-[#f5f3ef] hover:bg-[#e8e5e0] border border-[#e8e5e0] rounded-lg px-3 py-1.5 text-[13px] font-medium text-gray-800 focus:outline-none transition-colors cursor-pointer" 
                                        data-item-id="{{ $item->id }}">
                                    @foreach($item->product->variants->pluck('attributes.size')->filter()->unique() as $sz)
                                        <option value="{{ $sz }}" {{ $item->size === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @elseif($item->size)
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

                            <!-- Price & Savings -->
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-dashed border-[#e8e5e0]">
                                <div class="space-y-1">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-[18px] font-semibold text-gray-900 item-total" data-item-id="{{ $item->id }}">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                                        <span class="text-[13px] text-gray-400 line-through item-original-total {{ $item->discount_amount > 0 ? '' : 'hidden' }}" data-item-id="{{ $item->id }}">
                                            ₱{{ number_format(($item->unit_price * $item->quantity) + $item->discount_amount, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-[12px] font-medium text-green-700 item-savings {{ $item->discount_amount > 0 ? '' : 'hidden' }}" data-item-id="{{ $item->id }}">
                                        Saved ₱{{ number_format($item->discount_amount, 2) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-[12px] text-gray-500">
                                        <span class="item-unit-price font-medium text-gray-900" data-item-id="{{ $item->id }}">₱{{ number_format($item->unit_price, 2) }}</span> each
                                    </div>
                                    <div class="text-[11px] text-gray-400 line-through item-original-unit-price {{ $item->discount_amount > 0 ? '' : 'hidden' }}" data-item-id="{{ $item->id }}">
                                        ₱{{ number_format($item->unit_price + ($item->discount_amount / $item->quantity), 2) }} each
                                    </div>
                                </div>
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
                            <span class="font-medium text-gray-900" id="summary-subtotal">₱{{ number_format($cart->total + $cart->discount_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[14px] {{ $cart->discount_total > 0 ? '' : 'hidden' }}" id="summary-discount-row">
                            <span class="text-green-700">Discounts</span>
                            <span class="font-medium text-green-700" id="summary-discount">-₱{{ number_format($cart->discount_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[14px]">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-gray-900" id="summary-shipping">₱{{ number_format($cart->shipping_total, 2) }}</span>
                        </div>
                        <div class="border-t border-[#e8e5e0] pt-4">
                            <div class="flex justify-between text-[20px] font-semibold text-gray-900">
                                <span>Total</span>
                                <span id="summary-total">₱{{ number_format($cart->total + $cart->shipping_total, 2) }}</span>
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
        document.querySelectorAll('.qty-decrease').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt(input.value);
                if (currentValue > 1) updateQuantity(itemId, currentValue - 1);
            });
        });

        document.querySelectorAll('.qty-increase').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt(input.value);
                if (currentValue < 99) updateQuantity(itemId, currentValue + 1);
            });
        });

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

        document.querySelectorAll('.size-select').forEach(select => {
            select.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const newSize = this.value;
                updateCartItemSize(itemId, newSize);
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                showConfirmModal(
                    'Remove Item', 
                    'Are you sure you want to remove this item from your cart?', 
                    () => removeItem(itemId)
                );
            });
        });

        const clearCartBtn = document.getElementById('clear-cart');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', function() {
                showConfirmModal(
                    'Clear Cart', 
                    'Are you sure you want to remove all items from your shopping cart? This action cannot be undone.', 
                    () => clearCart()
                );
            });
        }
    }

    function showConfirmModal(title, message, onConfirm) {
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300';
        
        const modal = document.createElement('div');
        modal.className = 'bg-white rounded-2xl shadow-2xl w-[90%] max-w-[400px] overflow-hidden transform scale-95 opacity-0 transition-all duration-300';
        
        modal.innerHTML = `
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-[18px] font-semibold text-gray-900 mb-2">${title}</h3>
                <p class="text-[14px] text-gray-600">${message}</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                <button id="modal-cancel" class="px-5 py-2.5 text-[13px] font-medium text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                <button id="modal-confirm" class="px-5 py-2.5 text-[13px] font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Confirm</button>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Animate in
        requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            modal.classList.remove('scale-95', 'opacity-0');
        });
        
        function closeModal() {
            overlay.classList.add('opacity-0');
            modal.classList.add('scale-95', 'opacity-0');
            setTimeout(() => overlay.remove(), 300);
        }
        
        modal.querySelector('#modal-cancel').addEventListener('click', closeModal);
        modal.querySelector('#modal-confirm').addEventListener('click', () => {
            onConfirm();
            closeModal();
        });
    }

    function updateSummary(summary) {
        if (!summary) return;
        const subtotalEl = document.getElementById('summary-subtotal');
        if (subtotalEl) subtotalEl.textContent = '₱' + summary.subtotal;
        
        const discountRow = document.getElementById('summary-discount-row');
        const discountEl = document.getElementById('summary-discount');
        if (discountRow && discountEl) {
            if (summary.has_discounts) {
                discountEl.textContent = '-₱' + summary.discount_total;
                discountRow.classList.remove('hidden');
            } else {
                discountRow.classList.add('hidden');
            }
        }
        
        const shippingEl = document.getElementById('summary-shipping');
        if (shippingEl) shippingEl.textContent = '₱' + summary.shipping_total;
        
        const totalEl = document.getElementById('summary-total');
        if (totalEl) totalEl.textContent = '₱' + summary.total;
    }

    async function updateCartItemSize(itemId, size) {
        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ size: size })
            });
            const data = await response.json();
            if (data.success) {
                if (data.merged) {
                    location.reload();
                } else {
                    updateCartCountDisplay(data.cart_count);
                    
                    // Update net total and net unit price
                    const itemTotal = document.querySelector(`.item-total[data-item-id="${itemId}"]`);
                    if (itemTotal) itemTotal.textContent = '₱' + data.item_total;
                    
                    const itemUnit = document.querySelector(`.item-unit-price[data-item-id="${itemId}"]`);
                    if (itemUnit && data.item_unit_price) itemUnit.textContent = '₱' + data.item_unit_price;
                    
                    // Update original prices and savings
                    const origTotal = document.querySelector(`.item-original-total[data-item-id="${itemId}"]`);
                    const savings = document.querySelector(`.item-savings[data-item-id="${itemId}"]`);
                    const origUnit = document.querySelector(`.item-original-unit-price[data-item-id="${itemId}"]`);
                    
                    if (data.has_discount) {
                        if (origTotal) {
                            origTotal.textContent = '₱' + data.original_total;
                            origTotal.classList.remove('hidden');
                        }
                        if (savings) {
                            savings.textContent = 'Saved ₱' + data.discount_amount;
                            savings.classList.remove('hidden');
                        }
                        if (origUnit) {
                            origUnit.textContent = '₱' + data.original_unit_price + ' each';
                            origUnit.classList.remove('hidden');
                        }
                    } else {
                        if (origTotal) origTotal.classList.add('hidden');
                        if (savings) savings.classList.add('hidden');
                        if (origUnit) origUnit.classList.add('hidden');
                    }
                    
                    updateSummary(data.summary);
                    window.Layout.showNotification(data.message, 'success');
                }
            } else {
                window.Layout.showNotification(data.message, 'error');
                // Revert size drop down to previous setting by reloading
                location.reload();
            }
        } catch (error) {
            console.error('Error updating size:', error);
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
                updateCartCountDisplay(data.cart_count);
                
                // Update net total and net unit price
                const itemTotal = document.querySelector(`.item-total[data-item-id="${itemId}"]`);
                if (itemTotal) itemTotal.textContent = '₱' + data.item_total;
                
                const itemUnit = document.querySelector(`.item-unit-price[data-item-id="${itemId}"]`);
                if (itemUnit && data.item_unit_price) itemUnit.textContent = '₱' + data.item_unit_price;
                
                // Update original prices and savings
                const origTotal = document.querySelector(`.item-original-total[data-item-id="${itemId}"]`);
                const savings = document.querySelector(`.item-savings[data-item-id="${itemId}"]`);
                const origUnit = document.querySelector(`.item-original-unit-price[data-item-id="${itemId}"]`);
                
                if (data.has_discount) {
                    if (origTotal) {
                        origTotal.textContent = '₱' + data.original_total;
                        origTotal.classList.remove('hidden');
                    }
                    if (savings) {
                        savings.textContent = 'Saved ₱' + data.discount_amount;
                        savings.classList.remove('hidden');
                    }
                    if (origUnit) {
                        origUnit.textContent = '₱' + data.original_unit_price + ' each';
                        origUnit.classList.remove('hidden');
                    }
                } else {
                    if (origTotal) origTotal.classList.add('hidden');
                    if (savings) savings.classList.add('hidden');
                    if (origUnit) origUnit.classList.add('hidden');
                }
                
                updateSummary(data.summary);
            } else {
                window.Layout.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
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
                updateCartCountDisplay(data.cart_count);
                const item = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                if (item) item.remove();
                if (data.cart_count === 0) location.reload();
                else updateSummary(data.summary);
            }
        } catch (error) {
            console.error('Error removing item:', error);
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
                window.Layout.showNotification(data.message, 'success');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            window.Layout.showNotification('Error clearing cart', 'error');
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
