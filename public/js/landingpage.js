/**
 * Landing Page JavaScript
 * Handles size selection and add to cart functionality
 */

(function() {
    'use strict';

    /**
     * Select a size chip — highlights selected, stores value
     */
    function selectSize(productId, size, btn) {
        // Deselect all chips for this product
        const container = document.getElementById(`sizes-${productId}`);
        if (container) {
            container.querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
                b.classList.add('border-gray-200', 'text-gray-600', 'bg-gray-50');
            });
        }
        // Highlight selected chip
        btn.classList.remove('border-gray-200', 'text-gray-600', 'bg-gray-50');
        btn.classList.add('bg-gray-900', 'text-white', 'border-gray-900');
        // Store value
        const hidden = document.getElementById(`selected-size-${productId}`);
        if (hidden) hidden.value = size;
        // Hide error if shown
        const err = document.getElementById(`size-error-${productId}`);
        if (err) err.classList.add('hidden');
    }

    /**
     * Add product to cart
     */
    async function addToCart(productId) {
        const button = document.getElementById(`add-to-cart-${productId}`);

        if (!button) {
            showNotification('Product not found', 'error');
            return;
        }

        // Validate size if required
        const sizeInput = document.getElementById(`selected-size-${productId}`);
        const sizeError = document.getElementById(`size-error-${productId}`);
        if (sizeInput !== null && sizeInput.value === '') {
            if (sizeError) sizeError.classList.remove('hidden');
            showNotification('Please select a size first', 'error');
            return;
        }

        const selectedSize = sizeInput ? sizeInput.value : null;
        const originalContent = button.innerHTML;

        button.innerHTML = '<span class="btn-loading"><svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
        button.disabled = true;

        try {
            const payload = { product_id: productId, quantity: 1 };
            if (selectedSize) payload.size = selectedSize;

            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                updateCartCount(data.cart_count);
                showNotification('Added to cart', 'success');
            } else {
                showNotification(data.message || 'Failed to add', 'error');
            }
        } catch (error) {
            showNotification('Something went wrong', 'error');
        } finally {
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    /**
     * Update cart count in header
     */
    function updateCartCount(count) {
        const cartCount = document.getElementById('cart-count');
        if (!cartCount) return;

        cartCount.textContent = count;
        count > 0
            ? cartCount.classList.remove('hidden')
            : cartCount.classList.add('hidden');
    }

    /**
     * Show notification toast
     */
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className =
            `fixed top-5 right-5 px-5 py-3 rounded-lg shadow-xl z-50 text-white text-[14px]
            ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 2500);
    }

    // Expose functions globally for onclick handlers
    window.LandingPage = {
        selectSize,
        addToCart
    };

})();
