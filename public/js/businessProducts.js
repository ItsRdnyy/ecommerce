function addBulkPricingRow() {
    const container = document.getElementById('bulk-pricing-container');
    const newRow = document.createElement('div');
    newRow.className = 'bulk-pricing-row grid grid-cols-2 gap-3';
    newRow.innerHTML = `
        <input type="number" name="bulk_min_quantity[]" placeholder="Min Qty" min="1" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400">
        <input type="number" name="bulk_max_quantity[]" placeholder="Max Qty" min="1" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400">
    `;
    container.appendChild(newRow);
}

function getCategoryType(selectElement) {
    const selected = selectElement.options[selectElement.selectedIndex];
    if (!selected) return null;
    if (selected.getAttribute('data-is-apparel') === 'true') return 'apparel';
    if (selected.getAttribute('data-is-shoe') === 'true') return 'shoe';
    return null;
}

function toggleSizesField(selectEl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const type = getCategoryType(selectEl);
    const input = container.querySelector('input[name="sizes"]');
    if (type === 'apparel') {
        container.classList.remove('hidden');
        if (input) input.placeholder = 'e.g. S, M, L, XL';
    } else if (type === 'shoe') {
        container.classList.remove('hidden');
        if (input) input.placeholder = 'e.g. 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48';
    } else {
        container.classList.add('hidden');
        if (input) input.value = '';
    }
}

function generateSizeStockInputs(sizesInputId, stockContainerId, stockInputsId) {
    const sizesInput = document.getElementById(sizesInputId);
    const stockContainer = document.getElementById(stockContainerId);
    const stockInputs = document.getElementById(stockInputsId);

    if (!sizesInput || !stockContainer || !stockInputs) return;

    sizesInput.addEventListener('input', function() {
        const sizes = this.value.split(',').map(s => s.trim()).filter(s => s);

        if (sizes.length > 0) {
            stockContainer.classList.remove('hidden');
            stockInputs.innerHTML = '';

            sizes.forEach(size => {
                const div = document.createElement('div');
                div.className = 'grid grid-cols-3 gap-2 items-center';
                div.innerHTML = `
                    <label class="text-[12px] text-gray-600 font-semibold">${size}:</label>
                    <input type="number" name="sizes_stock[${size}]" min="0" value="0" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400" placeholder="Stock">
                    <input type="number" name="sizes_price[${size}]" step="0.01" min="0" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400" placeholder="Price (Optional)">
                `;
                stockInputs.appendChild(div);
            });
        } else {
            stockContainer.classList.add('hidden');
            stockInputs.innerHTML = '';
        }
    });
}

// Init create form category listener
document.addEventListener('DOMContentLoaded', function () {
    const createCategorySelect = document.getElementById('create_category_id');
    if (createCategorySelect) {
        createCategorySelect.addEventListener('change', function () {
            toggleSizesField(this, 'create_sizes_container');
        });
    }

    const editCategorySelect = document.getElementById('edit_category_id');
    if (editCategorySelect) {
        editCategorySelect.addEventListener('change', function () {
            toggleSizesField(this, 'edit_sizes_container');
        });
    }

    // Initialize size stock inputs for create form
    generateSizeStockInputs('create_sizes', 'create_sizes_stock_container', 'create_sizes_stock_inputs');

    // Initialize size stock inputs for edit form
    generateSizeStockInputs('edit_sizes', 'edit_sizes_stock_container', 'edit_sizes_stock_inputs');
});

function openEditModal(productId) {
    console.log('Opening edit modal for product:', productId);
    
    // Fetch product data via AJAX
    fetch(`/business/products/${productId}/edit`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Product data:', data);
            document.getElementById('editProductId').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_status').value = data.status;
            document.getElementById('edit_gender').value = data.gender;
            document.getElementById('edit_retail_price').value = data.retail_price;
            document.getElementById('edit_wholesale_price').value = data.wholesale_price || '';
            document.getElementById('edit_stock').value = data.stock;

            // Set category and toggle sizes field
            const editCategorySelect = document.getElementById('edit_category_id');
            if (editCategorySelect) {
                editCategorySelect.value = data.category_id;
                toggleSizesField(editCategorySelect, 'edit_sizes_container');
            }

            // Populate sizes if apparel
            const editSizes = document.getElementById('edit_sizes');
            if (editSizes) {
                editSizes.value = data.sizes_string || '';
                // Trigger input event to generate stock inputs
                editSizes.dispatchEvent(new Event('input'));

                // Populate stock and price values from variants
                if (data.variants && data.variants.length > 0) {
                    setTimeout(() => {
                        data.variants.forEach(variant => {
                            const size = variant.attributes?.size;
                            if (size) {
                                const stockInput = document.querySelector(`input[name="sizes_stock[${size}]"]`);
                                if (stockInput) {
                                    stockInput.value = variant.stock || 0;
                                }
                                const priceInput = document.querySelector(`input[name="sizes_price[${size}]"]`);
                                if (priceInput) {
                                    priceInput.value = variant.price || '';
                                }
                            }
                        });
                    }, 100);
                }
            }

            document.getElementById('editForm').action = `/business/products/${data.id}`;
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product data: ' + error.message);
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function archiveProduct(productId, button) {
    if (!confirm('Are you sure you want to archive this product?')) return;

    const token = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = token ? token.content : '';

    fetch(`/business/products/${productId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the card from the grid
            const card = button.closest('.bg-white');
            card.style.transition = 'opacity 0.3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
        } else {
            alert('Error archiving product');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error archiving product');
    });
}

// Function to update grid layout - uniform across all categories
function updateGridLayout(categoryId) {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    // Keep uniform layout for all categories
    productsGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
}

// Category filter AJAX
document.getElementById('categoryFilter').addEventListener('change', function() {
    const categoryId = this.value;
    const genderId = document.getElementById('genderFilter').value;
    const clearBtn = document.getElementById('clearFilter');

    // Show/hide clear button
    if (categoryId || genderId) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

    // Update grid layout immediately
    updateGridLayout(categoryId);

    // Show loading state
    const currentGrid = document.getElementById('productsGrid');
    if (currentGrid) {
        currentGrid.style.opacity = '0.5';
    }

    // Build query parameters
    const params = new URLSearchParams();
    if (categoryId) params.append('category', categoryId);
    if (genderId) params.append('gender', genderId);

    // Fetch filtered products
    fetch(`/business/products/filter?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        // Create a temporary div to parse the response
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        // Extract the products grid from the response
        const newProductsGrid = tempDiv.querySelector('#productsGrid');

        if (newProductsGrid && currentGrid) {
            currentGrid.innerHTML = newProductsGrid.innerHTML;
            // Reapply the grid layout after content replacement
            updateGridLayout(categoryId);
            currentGrid.style.opacity = '1';
        } else {
            console.error('Could not find grid elements');
            if (currentGrid) currentGrid.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error filtering products:', error);
        if (currentGrid) currentGrid.style.opacity = '1';
        alert('Error filtering products. Please try again.');
    });
});

// Gender filter AJAX
document.getElementById('genderFilter').addEventListener('change', function() {
    const genderId = this.value;
    const categoryId = document.getElementById('categoryFilter').value;
    const clearBtn = document.getElementById('clearFilter');

    // Show/hide clear button
    if (categoryId || genderId) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

    // Update grid layout immediately
    updateGridLayout(categoryId);

    // Show loading state
    const currentGrid = document.getElementById('productsGrid');
    if (currentGrid) {
        currentGrid.style.opacity = '0.5';
    }

    // Build query parameters
    const params = new URLSearchParams();
    if (categoryId) params.append('category', categoryId);
    if (genderId) params.append('gender', genderId);

    // Fetch filtered products
    fetch(`/business/products/filter?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        // Create a temporary div to parse the response
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        // Extract the products grid from the response
        const newProductsGrid = tempDiv.querySelector('#productsGrid');

        if (newProductsGrid && currentGrid) {
            currentGrid.innerHTML = newProductsGrid.innerHTML;
            // Reapply the grid layout after content replacement
            updateGridLayout(categoryId);
            currentGrid.style.opacity = '1';
        } else {
            console.error('Could not find grid elements');
            if (currentGrid) currentGrid.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error filtering products:', error);
        if (currentGrid) currentGrid.style.opacity = '1';
        alert('Error filtering products. Please try again.');
    });
});

// Clear filter
document.getElementById('clearFilter').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('categoryFilter').value = '';
    document.getElementById('genderFilter').value = '';
    this.classList.add('hidden');

    // Reset grid layout to normal
    updateGridLayout('');

    // Show loading state
    const currentGrid = document.getElementById('productsGrid');
    if (currentGrid) {
        currentGrid.style.opacity = '0.5';
    }

    // Fetch all products
    fetch(`/business/products/filter`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        // Create a temporary div to parse the response
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        // Extract the products grid from the response
        const newProductsGrid = tempDiv.querySelector('#productsGrid');

        if (newProductsGrid && currentGrid) {
            currentGrid.innerHTML = newProductsGrid.innerHTML;
            // Reapply the grid layout after content replacement (normal layout)
            updateGridLayout('');
            currentGrid.style.opacity = '1';
        } else {
            console.error('Could not find grid elements');
            if (currentGrid) currentGrid.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error clearing filter:', error);
        if (currentGrid) currentGrid.style.opacity = '1';
        alert('Error clearing filter. Please try again.');
    });
});