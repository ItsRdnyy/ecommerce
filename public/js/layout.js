/**
 * Layout JavaScript
 * Handles global layout functionality including cart count, add to cart buttons, and notifications
 */

(function() {
    'use strict';

    let appState = {};

    /**
     * Initialize the layout
     */
    function init() {
        // Get app state from data attributes
        const body = document.body;
        appState = {
            isAuthenticated: body.dataset.isAuthenticated === 'true',
            cartCountUrl: body.dataset.cartCountUrl,
            cartAddUrl: body.dataset.cartAddUrl
        };

        // Update cart count on page load if authenticated
        if (appState.isAuthenticated) {
            updateCartCount();
        }

        // Setup add to cart buttons
        setupAddToCartButtons();

        // Setup mobile menu toggle
        setupMobileMenu();

        // Setup account dropdown toggle
        setupAccountDropdown();
    }

    /**
     * Setup mobile menu toggle
     */
    function setupMobileMenu() {
        const mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    }

    /**
     * Setup account dropdown toggle for click/touch interactions (especially on mobile)
     */
    function setupAccountDropdown() {
        const accountButton = document.querySelector('[data-account-toggle]');
        const accountDropdown = document.querySelector('[data-account-dropdown]');

        if (accountButton && accountDropdown) {
            accountButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = accountDropdown.classList.contains('invisible');
                if (isHidden) {
                    accountDropdown.classList.remove('opacity-0', 'invisible');
                    accountDropdown.classList.add('opacity-100', 'visible');
                } else {
                    accountDropdown.classList.add('opacity-0', 'invisible');
                    accountDropdown.classList.remove('opacity-100', 'visible');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!accountButton.contains(e.target) && !accountDropdown.contains(e.target)) {
                    accountDropdown.classList.add('opacity-0', 'invisible');
                    accountDropdown.classList.remove('opacity-100', 'visible');
                }
            });
        }
    }

    /**
     * Update cart count badge
     */
    async function updateCartCount() {
        try {
            const response = await fetch(appState.cartCountUrl);
            const data = await response.json();

            const cartCountEl = document.getElementById('cart-count');
            if (cartCountEl) {
                if (data.count > 0) {
                    cartCountEl.textContent = data.count;
                    cartCountEl.classList.remove('hidden');
                } else {
                    cartCountEl.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error updating cart count:', error);
        }
    }

    /**
     * Setup add to cart buttons
     */
    function setupAddToCartButtons() {
        const buttons = document.querySelectorAll('.add-to-cart-btn');

        buttons.forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();

                if (this.disabled) return;

                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const quantitySelector = this.dataset.quantitySelector;
                const btnText = this.querySelector('.btn-text');
                const btnLoading = this.querySelector('.btn-loading');

                // Check for size selection on product detail page
                const sizeInput = document.getElementById('detail-selected-size');
                const sizeError = document.getElementById('detail-size-error');
                if (sizeInput !== null && sizeInput.value === '') {
                    if (sizeError) sizeError.classList.remove('hidden');
                    showNotification('Please select a size', 'error');
                    return;
                }

                // Get quantity if selector exists
                let quantity = 1;
                if (quantitySelector) {
                    const qtyInput = document.getElementById(quantitySelector);
                    if (qtyInput) {
                        quantity = parseInt(qtyInput.value) || 1;
                    }
                }

                // Show loading state
                if (btnText) btnText.classList.add('hidden');
                if (btnLoading) btnLoading.classList.remove('hidden');
                this.disabled = true;

                try {
                    const payload = {
                        product_id: productId,
                        quantity: quantity
                    };

                    // Include size if selected
                    if (sizeInput && sizeInput.value) {
                        payload.size = sizeInput.value;
                    }

                    const response = await fetch(appState.cartAddUrl, {
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

                    if (data.success) {
                        // Update cart count
                        const cartCountEl = document.getElementById('cart-count');
                        if (cartCountEl) {
                            cartCountEl.textContent = data.cart_count;
                            cartCountEl.classList.remove('hidden');
                        }

                        // Show success message
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    showNotification('Error adding item to cart', 'error');
                } finally {
                    // Reset button state
                    if (btnText) btnText.classList.remove('hidden');
                    if (btnLoading) btnLoading.classList.add('hidden');
                    this.disabled = false;
                }
            });
        });
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        const isSuccess = type === 'success';
        const bgColor = isSuccess ? 'bg-gray-900' : 'bg-red-50 border border-red-200';
        const textColor = isSuccess ? 'text-white' : 'text-red-800';
        const icon = isSuccess 
            ? `<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
            : `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

        notification.className = `fixed top-5 right-5 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl z-50 transform transition-all duration-300 translate-y-[-100%] opacity-0 ${bgColor} ${textColor}`;
        
        notification.innerHTML = `
            ${icon}
            <span class="text-[14px] font-medium tracking-wide">${message}</span>
        `;
        
        document.body.appendChild(notification);

        // Animate in
        requestAnimationFrame(() => {
            notification.classList.remove('translate-y-[-100%]', 'opacity-0');
        });

        setTimeout(() => {
            // Animate out
            notification.classList.add('translate-y-[-100%]', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 3500);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose functions globally for external use
    window.Layout = {
        updateCartCount,
        showNotification
    };

})();
