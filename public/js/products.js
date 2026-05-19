/**
 * Products Page JavaScript
 * Handles search, filtering, cart operations, and product interactions
 */

(function() {
    'use strict';

    let searchTimeout;
    let isLoading = false;
    let searchInput, categoryFilter, genderFilter, clearFiltersBtn, productsGrid;
    let appState = {};
    let allProducts = [];

    /**
     * Initialize the products page
     */
    function init() {
        console.log('Initializing products page...');

        // Get app state from data attributes
        const productsContainer = document.getElementById('products-page');
        if (!productsContainer) {
            console.error('Products container not found');
            return;
        }

        appState = {
            isAuthenticated: productsContainer.dataset.isAuthenticated === 'true',
            isBusinessUser: productsContainer.dataset.isBusinessUser === 'true',
            loginUrl: productsContainer.dataset.loginUrl,
            productsUrl: productsContainer.dataset.productsUrl,
            cartAddUrl: productsContainer.dataset.cartAddUrl,
            checkoutUrl: productsContainer.dataset.checkoutUrl
        };

        console.log('App state:', appState);

        // Get DOM elements
        searchInput = document.getElementById('search-input');
        categoryFilter = document.getElementById('category-filter');
        genderFilter = document.getElementById('gender-filter');
        clearFiltersBtn = document.getElementById('clear-filters');
        productsGrid = document.getElementById('products-container');

        console.log('DOM elements:', {
            searchInput: !!searchInput,
            categoryFilter: !!categoryFilter,
            genderFilter: !!genderFilter,
            clearFiltersBtn: !!clearFiltersBtn,
            productsGrid: !!productsGrid
        });

        if (!searchInput || !categoryFilter || !genderFilter || !clearFiltersBtn || !productsGrid) {
            console.error('Required DOM elements not found');
            return;
        }

        // Add smooth transition styles to prevent layout shifts
        productsGrid.style.transition = 'opacity 0.3s ease-in-out';
        
        // Add CSS for smooth animations if not already present
        if (!document.getElementById('products-page-styles')) {
            const style = document.createElement('style');
            style.id = 'products-page-styles';
            style.textContent = `
                /* Shop sections maintain their position during filtering */
                [data-business-id] {
                    will-change: opacity;
                    transition: opacity 0.3s ease-in-out;
                }

                /* Prevent layout shift when products change */
                .grid {
                    will-change: contents;
                }

                /* Smooth product card appearance */
                .grid > div {
                    animation: fadeIn 0.3s ease-in-out;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }

                /* Maintain fixed heights during loading to prevent jumps */
                [style*="min-height"] {
                    transition: min-height 0.3s ease-in-out;
                }
            `;
            document.head.appendChild(style);
        }

        console.log('Filter elements initialized');

        // Apply initial filter if URL has parameters
        const urlParams = new URLSearchParams(window.location.search);
        console.log('URL params:', Object.fromEntries(urlParams));
        
        if (urlParams.has('search') || urlParams.has('category') || urlParams.has('gender')) {
            searchInput.value = urlParams.get('search') || '';
            categoryFilter.value = urlParams.get('category') || '';
            genderFilter.value = urlParams.get('gender') || '';
            console.log('Applying initial filter...');
            filterProducts();
        }

        // Live Search with auto-refresh
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            // Auto-refresh after user stops typing for 300ms
            searchTimeout = setTimeout(() => {
                filterProducts();
            }, 300);
        });

        // Category Filter - Auto-refresh on change
        categoryFilter.addEventListener('change', filterProducts);

        // Gender Filter - Auto-refresh on change
        genderFilter.addEventListener('change', filterProducts);

        // Clear Filters
        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = '';
            genderFilter.value = '';
            filterProducts();
        });
    }

    /**
     * Filter products via AJAX - Real-time server-side filtering
     */
    async function filterProducts() {
        const search = searchInput.value.trim();
        const category = categoryFilter.value;
        const gender = genderFilter.value;

        console.log('Filtering - Search:', search, 'Category:', category, 'Gender:', gender);

        // Update URL without page reload
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (category) params.append('category', category);
        if (gender) params.append('gender', gender);
        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newUrl);

        // Set loading state with smooth fade transition
        isLoading = true;
        productsGrid.style.transition = 'opacity 0.3s ease-in-out';
        productsGrid.style.opacity = '0.6';

        try {
            // Fetch filtered products from server
            const queryParams = new URLSearchParams();
            if (search) queryParams.append('search', search);
            if (category) queryParams.append('category', category);
            if (gender) queryParams.append('gender', gender);

            const response = await fetch(`${appState.productsUrl}?${queryParams.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch products');
            }

            const data = await response.json();
            console.log('Fetched products:', data.products.length);

            // Group products by business - maintain order
            const productsByShop = {};
            const shopOrder = [];
            
            data.products.forEach(product => {
                if (!productsByShop[product.business_id]) {
                    productsByShop[product.business_id] = {
                        business_name: product.business_name,
                        products: []
                    };
                    shopOrder.push(product.business_id);
                }
                productsByShop[product.business_id].products.push(product);
            });

            // Render products maintaining section organization
            renderProducts(productsByShop, shopOrder);
            
            // Smooth transition back to full opacity
            setTimeout(() => {
                productsGrid.style.opacity = '1';
            }, 50);
            
            isLoading = false;
        } catch (error) {
            console.error('Error filtering products:', error);
            showNotification('Error loading products', 'error');
            isLoading = false;
            productsGrid.style.opacity = '1';
        }
    }

    /**
     * Render products grid - maintains shop sections and prevents layout shifts
     */
    function renderProducts(productsByShop, shopOrder = []) {
        if (Object.keys(productsByShop).length === 0) {
            // No products found
            productsGrid.innerHTML = emptyState();
            return;
        }

        let html = '';

        // Use provided shop order or fallback to object keys
        const orderedShops = shopOrder.length > 0 ? shopOrder : Object.keys(productsByShop);

        orderedShops.forEach(businessId => {
            const shopData = productsByShop[businessId];
            if (!shopData) return;

            const productCount = shopData.products.length;
            html += `
                <div class="mb-12" data-business-id="${businessId}" style="min-height: 100px;">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[24px] font-serif-display text-gray-900">${shopData.business_name}</h2>
                            <p class="text-gray-600 text-[14px]" data-product-count>${productCount} product${productCount > 1 ? 's' : ''}</p>
                        </div>
                    </div>

                    <!-- Products Grid for this Shop -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        ${shopData.products.map(product => createProductCard(product)).join('')}
                    </div>
                </div>
            `;
        });

        // Clear and set new content (prevents duplication)
        productsGrid.innerHTML = '';
        productsGrid.innerHTML = html;
    }

    /**
     * Create product card HTML
     */
    function createProductCard(product) {
        const image = product.image
            ? `<img src="/storage/${product.image}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">`
            : `<div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>`;

        const badge =
            product.stock === 0
                ? `<div class="absolute top-3 left-3 px-3 py-1 bg-red-600 text-white text-[11px] font-semibold uppercase tracking-wider rounded-full">Out of Stock</div>`
                : product.stock <= 10
                    ? `<div class="absolute top-3 left-3 px-3 py-1 bg-orange-500 text-white text-[11px] font-semibold uppercase tracking-wider rounded-full">Low Stock</div>`
                    : '';

        const genderBadge = product.gender
            ? `<span class="inline-flex px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase rounded-full ${product.gender === 'men' ? 'bg-blue-100 text-blue-800' : (product.gender === 'women' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800')}">
                ${product.gender.charAt(0).toUpperCase() + product.gender.slice(1)}
            </span>`
            : '';

        const wholesaleNote = product.is_wholesale_enabled && product.wholesale_price > 0
            ? `<p class="text-[12px] text-gray-500 mb-4">Wholesale from ${product.moq || 1} pcs — ₱${parseFloat(product.wholesale_price).toFixed(2)} each</p>`
            : '';

        // Build size chips if product has sizes
        const catName = (product.category_name || '').toLowerCase();
        const isShoe = ['shoe','footwear','sneaker','boot'].some(k => catName.includes(k));
        const isApparel = ['clothing','shirt','pants','dress','apparel'].some(k => catName.includes(k));
        let sizesHtml = '';
        if (isShoe || isApparel) {
            const sizeList = isShoe
                ? [38,39,40,41,42,43,44,45,46,47,48]
                : (product.sizes || []);
            if (sizeList.length > 0) {
                const chips = sizeList.map(s =>
                    `<button type="button"
                        onclick="window.ProductsPage.selectSize(${product.id}, '${s}', this)"
                        class="size-btn px-3 py-1.5 text-[12px] font-medium border border-gray-300 rounded text-gray-700 hover:border-gray-900 transition-colors">
                        ${s}
                    </button>`
                ).join('');
                sizesHtml = `
                    <div class="mb-4">
                        <p class="text-[11px] text-gray-500 mb-1">Size <span class="text-red-500">*</span></p>
                        <div class="flex flex-wrap gap-1.5" id="sizes-${product.id}">${chips}</div>
                        <input type="hidden" id="selected-size-${product.id}" value="">
                        <p class="text-[10px] text-red-500 mt-1 hidden" id="size-error-${product.id}">Please select a size.</p>
                    </div>`;
            }
        }

        const buttons = product.stock > 0
            ? appState.isAuthenticated
                ? `
                    <button onclick="window.ProductsPage.addToCart(${product.id})"
                        id="add-to-cart-${product.id}"
                        class="flex-1 py-2.5 px-4 bg-gray-900 text-white text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        Add to Cart
                    </button>
                    <button onclick="window.ProductsPage.buyNow(${product.id})"
                        class="flex-1 py-2.5 px-4 border-2 border-gray-900 text-gray-900 text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-900 hover:text-white transition-colors">
                        Buy Now
                    </button>
                `
                : `<a href="${window.location.origin}/login?redirect=${encodeURIComponent(window.location.href)}"
                        class="flex-1 py-2.5 px-4 bg-gray-900 text-white text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-800 transition-colors text-center">
                        Login to Buy
                    </a>`
            : `
                <button disabled
                    class="flex-1 py-2.5 px-4 bg-gray-300 text-gray-500 text-[12px] font-semibold uppercase tracking-wider cursor-not-allowed">
                    Out of Stock
                </button>
            `;

        return `
            <div class="bg-white border border-[#e8e5e0] group hover:shadow-xl transition-all duration-300">
                <div class="aspect-[3/4] overflow-hidden bg-gray-100 relative">
                    ${image}
                    ${badge}
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[11px] px-2 py-1 bg-[#f5f3ef] text-gray-700 rounded-full">
                            ${product.category_name || 'General'}
                        </span>
                        ${genderBadge}
                    </div>
                    <h3 class="text-[15px] font-medium text-gray-900 mb-2 line-clamp-2">
                        ${product.name}
                    </h3>
                    <p class="text-[13px] text-gray-600 mb-4 line-clamp-2">
                        ${product.description || ''}
                    </p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[18px] font-light text-gray-900">
                            ₱${parseFloat(product.retail_price).toFixed(2)}
                        </span>
                        <span class="text-[12px] ${product.stock > 0 ? 'text-green-700' : 'text-red-600'}">
                            ${product.stock > 0 ? product.stock + ' in stock' : 'Out of stock'}
                        </span>
                    </div>
                    ${wholesaleNote}
                    ${sizesHtml}
                    <div class="space-y-3">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="window.ProductsPage.decrementQuantity(${product.id})"
                                class="w-8 h-8 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 12h-15" />
                                </svg>
                            </button>
                            <input type="number"
                                id="quantity-${product.id}"
                                value="1"
                                min="1"
                                max="${product.stock}"
                                class="w-16 px-3 py-2 text-center border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900">
                            <button onclick="window.ProductsPage.incrementQuantity(${product.id}, ${product.stock})"
                                class="w-8 h-8 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            ${buttons}
                            <a href="/products/${product.id}"
                               class="w-10 h-10 p-3 border border-gray-300 rounded-lg hover:border-gray-900 transition-colors duration-200 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Empty state HTML
     */
    function emptyState() {
        return `
            <div class="col-span-full text-center py-20">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <h3 class="text-[20px] font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 text-[14px] mb-6">Try adjusting your search or filter criteria.</p>
                <button onclick="clearFiltersBtn.click()" class="inline-block px-6 py-3 bg-gray-900 text-white text-[13px] font-semibold uppercase tracking-wider hover:bg-gray-800 transition-colors">
                    View All Products
                </button>
            </div>
        `;
    }

    /**
     * Increment quantity
     */
    function incrementQuantity(id, stock) {
        const input = document.getElementById(`quantity-${id}`);
        if (parseInt(input.value) < stock) {
            input.value++;
        }
    }

    /**
     * Decrement quantity
     */
    function decrementQuantity(id) {
        const input = document.getElementById(`quantity-${id}`);
        if (parseInt(input.value) > 1) {
            input.value--;
        }
    }

    /**
     * Select a size chip — highlights selected, stores value
     */
    function selectSize(productId, size, btn) {
        // Deselect all chips for this product
        const container = document.getElementById(`sizes-${productId}`);
        if (container) {
            container.querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
                b.classList.add('border-gray-300', 'text-gray-700');
            });
        }
        // Highlight selected chip
        btn.classList.remove('border-gray-300', 'text-gray-700');
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
        const quantity = document.getElementById(`quantity-${productId}`);
        const button = document.getElementById(`add-to-cart-${productId}`);

        if (!quantity || !button) {
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

        const qty = parseInt(quantity.value);
        const selectedSize = sizeInput ? sizeInput.value : null;
        const originalContent = button.innerHTML;

        button.innerHTML = 'Adding...';
        button.disabled = true;

        try {
            const payload = { product_id: productId, quantity: qty };
            if (selectedSize) payload.size = selectedSize;

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
     * Buy now - add to cart and redirect to checkout
     */
    async function buyNow(productId) {
        const quantity = document.getElementById(`quantity-${productId}`);

        if (!quantity) {
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

        const qty = parseInt(quantity.value);
        const selectedSize = sizeInput ? sizeInput.value : null;

        try {
            const payload = { product_id: productId, quantity: qty };
            if (selectedSize) payload.size = selectedSize;

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

            if (response.ok && data.success) {
                window.location.href = appState.checkoutUrl;
            } else {
                showNotification(data.message || 'Checkout failed', 'error');
            }
        } catch (error) {
            showNotification('Something went wrong', 'error');
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

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(init, 100);
        });
    } else {
        setTimeout(init, 100);
    }

    // Expose functions globally for onclick handlers
    window.ProductsPage = {
        incrementQuantity,
        decrementQuantity,
        addToCart,
        buyNow,
        selectSize
    };

})();
