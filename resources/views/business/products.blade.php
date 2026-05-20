@extends('business.layout')

@section('title', 'Products')
@section('nav-products', 'bg-[#f5f3ef] text-gray-900')

@section('content')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Products</h1>
            <p class="text-[14px] text-gray-600">Manage your product inventory.</p>
        </div>
        <div>
            <a href="{{ route('business.products.archived') }}" class="inline-flex items-center gap-2 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-700 hover:text-black border border-[#e8e5e0] hover:border-black px-4 py-2.5 bg-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                View Archived Products
            </a>
        </div>
    </div>

    <!-- Add Product Form -->
    <div class="bg-white border border-[#e8e5e0] mb-10">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Add New Product</h2>
        </div>
        <form method="POST" action="{{ route('business.products.store') }}" class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5" enctype="multipart/form-data">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Product Name</label>
                <input type="text" name="name" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Product Image</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
            </div>
            <div>
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Category</label>
                <select name="category_id" id="create_category_id" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    <option value="" disabled selected>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                                data-is-apparel="{{ str_contains(strtolower($category->name), 'clothing') || str_contains(strtolower($category->name), 'shirt') || str_contains(strtolower($category->name), 'pants') || str_contains(strtolower($category->name), 'dress') || str_contains(strtolower($category->name), 'apparel') ? 'true' : 'false' }}"
                                data-is-shoe="{{ str_contains(strtolower($category->name), 'shoe') || str_contains(strtolower($category->name), 'footwear') || str_contains(strtolower($category->name), 'sneaker') || str_contains(strtolower($category->name), 'boot') ? 'true' : 'false' }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Status</label>
                <select name="status" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="flagged">Flagged</option>
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Gender</label>
                <select name="gender" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    <option value="men">Men</option>
                    <option value="women">Women</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Retail Price (₱)</label>
                <input type="number" name="retail_price" step="0.01" min="0" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Bulk Pricing Tiers (Volume Discounts)</label>
                <div id="bulk-pricing-container" class="space-y-3">
                    <div class="bulk-pricing-row grid grid-cols-3 gap-3">
                        <input type="number" name="bulk_min_quantity[]" placeholder="Min Qty (e.g. 5)" min="1" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400">
                        <input type="number" name="bulk_max_quantity[]" placeholder="Max Qty (e.g. 9)" min="1" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400">
                        <input type="number" name="bulk_discount_percent[]" placeholder="Discount %" min="0" max="100" step="0.01" class="border border-[#e8e5e0] rounded px-3 py-2 text-[14px] focus:outline-none focus:border-gray-400">
                    </div>
                </div>
                <button type="button" onclick="addBulkPricingRow()" class="mt-3 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-800 hover:text-black transition-colors underline underline-offset-4">
                    + Add Bulk Tier
                </button>
            </div>
            <div>
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Stock</label>
                <input type="number" name="stock" id="create_stock" min="0" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
            </div>
            <div id="create_sizes_container" class="hidden">
                <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Available Sizes (Comma Separated)</label>
                <input type="text" id="create_sizes" name="sizes" placeholder="e.g. S, M, L, XL" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                <p class="text-[10px] text-gray-500 mt-1">This will automatically create variants for each size.</p>
                <div id="create_sizes_stock_container" class="mt-3 hidden">
                    <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Stock and Price per Size</label>
                    <div id="create_sizes_stock_inputs" class="space-y-2"></div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-6 py-2.5 bg-[#111] text-white text-[12px] font-semibold tracking-[0.1em] uppercase rounded hover:bg-gray-800 transition-colors">Add Product</button>
            </div>
        </form>
    </div>

    <!-- Products List -->
    <div class="bg-white border border-[#e8e5e0]">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Your Products</h2>
                <div class="flex items-center gap-3">
                    <label class="text-[11px] text-gray-500 font-medium">Filter:</label>
                    <select id="categoryFilter" class="bg-white border border-[#e8e5e0] text-[12px] py-2 px-3 rounded focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    data-is-clothing="{{ str_contains(strtolower($category->name), 'clothing') || str_contains(strtolower($category->name), 'shirt') || str_contains(strtolower($category->name), 'pants') || str_contains(strtolower($category->name), 'dress') ? 'true' : 'false' }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <select id="genderFilter" class="bg-white border border-[#e8e5e0] text-[12px] py-2 px-3 rounded focus:outline-none focus:border-black focus:ring-1 focus:ring-black">
                        <option value="">All Genders</option>
                        <option value="men" {{ request('gender') == 'men' ? 'selected' : '' }}>Men</option>
                        <option value="women" {{ request('gender') == 'women' ? 'selected' : '' }}>Women</option>
                        <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>

                    <button id="clearFilter" class="text-[11px] text-gray-600 hover:text-black underline transition-colors {{ request('category') || request('gender') ? '' : 'hidden' }}">Clear</button>
                </div>
            </div>
        </div>
        <div class="p-6 overflow-x-hidden">
            <div class="w-full max-w-[1200px] mx-auto overflow-x-hidden">
                <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($products as $product)
                <div class="bg-white border border-[#e8e5e0] rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
                    <!-- Product Image -->
                    <div class="aspect-square bg-gray-100 relative flex-shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-[14px]">No Image</div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex px-2 py-1 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : ($product->status == 'flagged' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $product->status }}
                            </span>
                        </div>
                        
                        <!-- Stock Badge -->
                        @if($product->stock == 0)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex px-2 py-1 text-[10px] font-semibold tracking-wider uppercase rounded-full bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-semibold text-[15px] text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                        
                        <!-- Category & Gender -->
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[12px] text-gray-600">{{ $product->category->name ?? '—' }}</span>
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $product->gender == 'men' ? 'bg-blue-100 text-blue-800' : ($product->gender == 'women' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($product->gender) }}
                            </span>
                        </div>

                        <!-- Reviews Summary -->
                        @php
                            $approvedReviews = $product->reviews->where('status', 'approved');
                            $reviewsCount = $approvedReviews->count();
                            $averageRating = $reviewsCount > 0 ? round($approvedReviews->avg('rating'), 1) : null;
                        @endphp
                        <div class="flex items-center gap-1.5 mb-3">
                            @if($reviewsCount > 0)
                                <div class="flex text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-[11px] font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                <span class="text-[11px] text-gray-500">({{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }})</span>
                            @else
                                <span class="text-[11px] text-gray-400 italic">No reviews yet</span>
                            @endif
                        </div>

                        <!-- Prices -->
                        <div class="flex items-center gap-3 mb-3">
                            <div>
                                <p class="text-[11px] text-gray-500">Retail</p>
                                <p class="text-[14px] font-semibold text-gray-900">₱{{ number_format($product->retail_price, 2) }}</p>
                            </div>
                            @if($product->wholesale_price)
                            <div>
                                <p class="text-[11px] text-gray-500">Wholesale</p>
                                <p class="text-[14px] font-semibold text-gray-700">₱{{ number_format($product->wholesale_price, 2) }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Stock -->
                        <div class="mb-3">
                            <p class="text-[11px] text-gray-500">Stock</p>
                            <p class="text-[14px] font-medium {{ $product->stock == 0 ? 'text-red-600' : 'text-gray-600' }}">{{ $product->stock }} units</p>
                        </div>
                        
                        <!-- Sizes (Apparel & Shoes) -->
                        @php
                            $categoryName = strtolower($product->category->name ?? '');
                            $isApparel = str_contains($categoryName, 'clothing') || str_contains($categoryName, 'shirt') || str_contains($categoryName, 'pants') || str_contains($categoryName, 'dress') || str_contains($categoryName, 'apparel');
                            $isShoe = str_contains($categoryName, 'shoe') || str_contains($categoryName, 'footwear') || str_contains($categoryName, 'sneaker') || str_contains($categoryName, 'boot');
                            $showSizes = $isApparel || $isShoe;
                            if ($isShoe) {
                                $sizes = collect(range(38, 48));
                            } elseif ($isApparel && $product->variants) {
                                $sizes = $product->variants->map(fn($v) => $v->attributes['size'] ?? null)->filter()->unique()->values();
                            } else {
                                $sizes = collect();
                            }
                        @endphp
                        @if($showSizes && $sizes->isNotEmpty())
                        <div class="mb-3">
                            <p class="text-[11px] text-gray-500 mb-1">Available Sizes</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($sizes as $size)
                                @php
                                    $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                    $variantPrice = $variant ? $variant->price : null;
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium border border-gray-200 rounded text-gray-600 bg-gray-50">
                                    {{ $size }}
                                    @if($variantPrice)
                                        <span class="ml-1 text-green-600 font-semibold">(₱{{ number_format($variantPrice, 2) }})</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Discount Tiers -->
                        @if(($product->discountTiers ?? collect())->count() > 0)
                        <div class="mb-4">
                            <p class="text-[11px] text-gray-500 mb-1">Bulk Discounts</p>
                            <div class="space-y-1">
                                @foreach($product->discountTiers->take(2) as $tier)
                                <div class="text-[11px] text-gray-600">
                                    {{ $tier->min_quantity }}{{ $tier->max_quantity ? '-'.$tier->max_quantity : '+' }} pcs
                                    <span class="text-green-600 font-medium">{{ $tier->discount_percent }}% off</span>
                                </div>
                                @endforeach
                                @if($product->discountTiers->count() > 2)
                                <p class="text-[10px] text-gray-400">+{{ $product->discountTiers->count() - 2 }} more tiers</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <!-- Actions -->
                        <div class="flex gap-2 mt-auto">
                            <button onclick="openEditModal({{ $product->id }})" class="flex-1 bg-blue-600 text-white text-[11px] font-semibold tracking-[0.1em] uppercase py-2 px-3 rounded hover:bg-blue-700 transition-colors">
                                Update
                            </button>
                            <button onclick="archiveProduct({{ $product->id }}, this)" class="flex-1 border border-red-600 text-red-600 text-[11px] font-semibold tracking-[0.1em] uppercase py-2 px-3 rounded hover:bg-red-50 transition-colors">
                                Archive
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-[14px] text-gray-500">No products found. Add your first product above.</p>
                </div>
            @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-[16px] font-semibold tracking-[0.1em] uppercase text-gray-700">Update Product</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editProductId" name="product_id">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Product Name</label>
                        <input type="text" id="edit_name" name="name" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Description</label>
                        <textarea id="edit_description" name="description" rows="3" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400"></textarea>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Category</label>
                        <select id="edit_category_id" name="category_id" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-is-apparel="{{ str_contains(strtolower($category->name), 'clothing') || str_contains(strtolower($category->name), 'shirt') || str_contains(strtolower($category->name), 'pants') || str_contains(strtolower($category->name), 'dress') || str_contains(strtolower($category->name), 'apparel') ? 'true' : 'false' }}"
                                        data-is-shoe="{{ str_contains(strtolower($category->name), 'shoe') || str_contains(strtolower($category->name), 'footwear') || str_contains(strtolower($category->name), 'sneaker') || str_contains(strtolower($category->name), 'boot') ? 'true' : 'false' }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Status</label>
                        <select id="edit_status" name="status" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="flagged">Flagged</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Gender</label>
                        <select id="edit_gender" name="gender" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Retail Price (₱)</label>
                        <input type="number" id="edit_retail_price" name="retail_price" step="0.01" min="0" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Stock</label>
                        <input type="number" id="edit_stock" name="stock" min="0" required class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                    </div>
                    <div id="edit_sizes_container" class="hidden">
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Available Sizes (Comma Separated)</label>
                        <input type="text" id="edit_sizes" name="sizes" placeholder="e.g. S, M, L, XL" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                        <p class="text-[10px] text-gray-500 mt-1">This will automatically create variants for each size.</p>
                        <div id="edit_sizes_stock_container" class="mt-3 hidden">
                            <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Stock and Price per Size</label>
                            <div id="edit_sizes_stock_inputs" class="space-y-2"></div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Bulk Pricing Tiers (Volume Discounts)</label>
                        <div id="edit-bulk-pricing-container" class="space-y-3">
                            <!-- Dynamic rows will be inserted here via JS -->
                        </div>
                        <button type="button" onclick="addEditBulkPricingRow()" class="mt-3 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-800 hover:text-black transition-colors underline underline-offset-4">
                            + Add Bulk Tier
                        </button>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Product Image</label>
                        <input type="file" id="edit_image" name="image" accept="image/*" class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                        <p class="text-[11px] text-gray-500 mt-1">Leave empty to keep current image</p>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="px-6 py-2.5 bg-[#111] text-white text-[12px] font-semibold tracking-[0.1em] uppercase rounded hover:bg-gray-800 transition-colors">Update Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/businessProducts.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/businessProducts.css') }}">
@endpush
