@extends('layouts.app')

@section('title', 'Products')

@section('content')
@php
    $isAuthenticated = auth()->check();
    $isBusinessUser = auth()->check() && auth()->user()->isBusiness();
@endphp

    <!-- Products Page Container with Data Attributes -->
    <div id="products-page"
         data-is-authenticated="{{ $isAuthenticated ? 'true' : 'false' }}"
         data-is-business-user="{{ $isBusinessUser ? 'true' : 'false' }}"
         data-login-url="{{ route('login', ['redirect' => request()->fullUrl()]) }}"
         data-products-url="{{ route('products') }}"
         data-cart-add-url="{{ route('cart.add') }}"
         data-checkout-url="{{ route('checkout.index') }}">

    <!-- Hero Section -->
    <section class="bg-[#f5f3ef] py-16">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="text-center">
                <h1 class="font-serif-display text-[48px] sm:text-[60px] text-gray-900 mb-4">Products</h1>
                <p class="text-gray-600 text-[15px] max-w-2xl mx-auto">
                    Browse our collection of premium clothing and accessories.
                </p>
            </div>
        </div>
    </section>

    <!-- Products Grid with Search & Filters -->
    <section class="bg-white">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <!-- Search and Filter Bar - Top of Products -->
            <div class="py-8 border-b border-[#e8e5e0]">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search Bar -->
                    <div class="flex-1 w-full md:w-auto">
                        <div class="relative">
                            <input type="text" 
                                   id="search-input"
                                   value="{{ request('search') }}"
                                   placeholder="Search products..." 
                                   class="w-full px-4 py-3 pl-12 border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-900 transition-colors">
                            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="flex items-center gap-3">
                        <select id="category-filter"
                                class="px-4 py-3 border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors cursor-pointer">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select id="gender-filter"
                                class="px-4 py-3 border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors cursor-pointer">
                            <option value="">All Genders</option>
                            <option value="men" {{ request('gender') == 'men' ? 'selected' : '' }}>Men</option>
                            <option value="women" {{ request('gender') == 'women' ? 'selected' : '' }}>Women</option>
                            <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                        </select>
                        <button id="clear-filters" class="px-4 py-3 text-[14px] font-medium text-gray-700 hover:text-gray-900 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Container -->
            <div class="py-16" id="products-container">
                @if($products->count() > 0)
                    @foreach($productsByShop as $businessId => $shopProducts)
                    @php
                        $firstProduct = $shopProducts->first();
                        $shopName = $firstProduct->business && $firstProduct->business->businessProfile 
                            ? $firstProduct->business->businessProfile->business_name 
                            : ($firstProduct->business ? $firstProduct->business->name : 'Unknown Shop');
                    @endphp
                    
                    <!-- Shop Header -->
                    <div class="mb-12">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-[24px] font-serif-display text-gray-900">{{ $shopName }}</h2>
                                <p class="text-gray-600 text-[14px]">{{ $shopProducts->count() }} product{{ $shopProducts->count() > 1 ? 's' : '' }}</p>
                            </div>
                        </div>

                        <!-- Products Grid for this Shop -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($shopProducts as $product)
                                @php
                                    $categoryName = strtolower($product->category->name ?? '');
                                    $isApparel = str_contains($categoryName, 'clothing') || str_contains($categoryName, 'shirt') || str_contains($categoryName, 'pants') || str_contains($categoryName, 'dress') || str_contains($categoryName, 'apparel');
                                    $isShoe = str_contains($categoryName, 'shoe') || str_contains($categoryName, 'footwear') || str_contains($categoryName, 'sneaker') || str_contains($categoryName, 'boot');
                                    $showSizes = $isApparel || $isShoe;
                                    $totalStock = $showSizes ? $product->variants->sum('stock') : $product->stock;
                                @endphp
                                <div class="bg-white border border-[#e8e5e0] group hover:shadow-xl transition-all duration-300">
                                    <!-- Product Image -->
                                    <div class="aspect-[3/4] overflow-hidden bg-gray-100 relative">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Stock Badge -->
                                        @if($totalStock == 0)
                                            <div class="absolute top-3 left-3 px-3 py-1 bg-red-600 text-white text-[11px] font-semibold uppercase tracking-wider rounded-full">
                                                Out of Stock
                                            </div>
                                        @elseif($totalStock <= 10)
                                            <div class="absolute top-3 left-3 px-3 py-1 bg-orange-500 text-white text-[11px] font-semibold uppercase tracking-wider rounded-full">
                                                Low Stock
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="p-5">
                                        <!-- Category & Gender -->
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-[11px] px-2 py-1 bg-[#f5f3ef] text-gray-700 rounded-full">
                                                {{ $product->category->name ?? 'General' }}
                                            </span>
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $product->gender == 'men' ? 'bg-blue-100 text-blue-800' : ($product->gender == 'women' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($product->gender) }}
                                            </span>
                                        </div>

                                        <!-- Product Name -->
                                        <h3 class="text-[15px] font-medium text-gray-900 mb-2 line-clamp-2">
                                            {{ $product->name }}
                                        </h3>

                                        <!-- Description -->
                                        <p class="text-[13px] text-gray-600 mb-4 line-clamp-2">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>

                                        <!-- Price and Stock -->
                                        <div class="flex items-center justify-between mb-2">
                                            <span id="price-display-{{ $product->id }}" data-base-price="{{ $product->retail_price }}" class="text-[18px] font-light text-gray-900">
                                                ₱{{ number_format($product->retail_price, 2) }}
                                            </span>
                                            <span id="stock-display-{{ $product->id }}" class="text-[12px] {{ $totalStock > 0 ? 'text-green-700' : 'text-red-600' }}">
                                                {{ $totalStock > 0 ? $totalStock . ' in stock' : 'Out of stock' }}
                                            </span>
                                        </div>
                                        @if($product->is_wholesale_enabled && $product->wholesale_price > 0)
                                            <p class="text-[12px] text-gray-500 mb-4">
                                                Wholesale from {{ $product->moq ?? 1 }} pcs — ₱{{ number_format($product->wholesale_price, 2) }} each
                                            </p>
                                        @endif

                                        <!-- Sizes (Apparel & Shoes) -->
                                        @php
                                            if ($isShoe) {
                                                $sizes = collect(range(38, 48));
                                                $sizeStocks = [];
                                                foreach ($sizes as $size) {
                                                    $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                                    $sizeStocks[$size] = $variant ? $variant->stock : 0;
                                                }
                                            } elseif ($isApparel && $product->variants) {
                                                $sizes = $product->variants->map(fn($v) => data_get($v->attributes, 'size'))->filter()->unique()->values();
                                                $sizeStocks = [];
                                                foreach ($sizes as $size) {
                                                    $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                                    $sizeStocks[$size] = $variant ? $variant->stock : 0;
                                                }
                                            } else {
                                                $sizes = collect();
                                                $sizeStocks = [];
                                            }
                                        @endphp
                                        @if($showSizes && $sizes->isNotEmpty())
                                        <div class="mb-4">
                                            <p class="text-[11px] text-gray-500 mb-1">Size <span class="text-red-500">*</span></p>
                                            <div class="flex flex-wrap gap-1.5" id="sizes-{{ $product->id }}">
                                                @foreach($sizes as $size)
                                                @php
                                                    $stock = $sizeStocks[$size] ?? 0;
                                                    $isOutOfStock = $stock <= 0;
                                                    $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                                    $price = $variant ? $variant->price : null;
                                                @endphp
                                                <button type="button"
                                                    onclick="window.ProductsPage.selectSize({{ $product->id }}, '{{ $size }}', this)"
                                                    data-stock="{{ $stock }}"
                                                    data-price="{{ $price }}"
                                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                                    class="size-btn px-3 py-1.5 text-[12px] font-medium border rounded transition-colors {{ $isOutOfStock ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-100' : 'border-gray-300 text-gray-700 hover:border-gray-900' }}">
                                                    {{ $size }}
                                                </button>
                                                @endforeach
                                            </div>
                                            <input type="hidden" id="selected-size-{{ $product->id }}" value="">
                                            <p class="text-[10px] text-red-500 mt-1 hidden" id="size-error-{{ $product->id }}">Please select a size.</p>
                                        </div>
                                        @endif

                                        <!-- Quantity Selector and Actions -->
                                        <div class="space-y-3">
                                            <!-- Quantity Selector -->
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="window.ProductsPage.decrementQuantity({{ $product->id }})"
                                                        class="w-8 h-8 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 12h-15" />
                                                    </svg>
                                                </button>
                                                <input type="number"
                                                       id="quantity-{{ $product->id }}"
                                                       value="1"
                                                       min="1"
                                                       max="{{ $totalStock }}"
                                                       onchange="window.ProductsPage.onQuantityChange({{ $product->id }})"
                                                       class="w-16 px-3 py-2 text-center border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900">
                                                <button onclick="window.ProductsPage.incrementQuantity({{ $product->id }}, {{ $totalStock }})"
                                                        class="w-8 h-8 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex gap-2">
                                                @auth
                                                    @if($totalStock > 0)
                                                        <button onclick="window.ProductsPage.addToCart({{ $product->id }})"
                                                                id="add-to-cart-{{ $product->id }}"
                                                                class="flex-1 py-2.5 px-4 bg-gray-900 text-white text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1.75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1.75 0Z" />
                                                            </svg>
                                                            Add to Cart
                                                        </button>
                                                        <button onclick="window.ProductsPage.buyNow({{ $product->id }})"
                                                                class="flex-1 py-2.5 px-4 border-2 border-gray-900 text-gray-900 text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-900 hover:text-white transition-colors">
                                                            Buy Now
                                                        </button>
                                                    @else
                                                        <button disabled
                                                                class="flex-1 py-2.5 px-4 bg-gray-300 text-gray-500 text-[12px] font-semibold uppercase tracking-wider cursor-not-allowed">
                                                            Out of Stock
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}"
                                                       class="flex-1 py-2.5 px-4 bg-gray-900 text-white text-[12px] font-semibold uppercase tracking-wider hover:bg-gray-800 transition-colors text-center">
                                                        Login to Buy
                                                    </a>
                                                @endauth
                                                <a href="{{ route('products.show', $product) }}"
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
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-20">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <h3 class="text-[20px] font-medium text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600 text-[14px] mb-6">Try adjusting your search or filter criteria.</p>
                </div>
            @endif
            </div>
        </div>
    </section>

    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endpush

