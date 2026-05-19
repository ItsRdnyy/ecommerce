@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $isAuthenticated = auth()->check();
    $categoryName = strtolower($product->category->name ?? '');
    $isApparel = str_contains($categoryName, 'clothing') || str_contains($categoryName, 'shirt') || str_contains($categoryName, 'pants') || str_contains($categoryName, 'dress') || str_contains($categoryName, 'apparel');
    $isShoe = str_contains($categoryName, 'shoe') || str_contains($categoryName, 'footwear') || str_contains($categoryName, 'sneaker') || str_contains($categoryName, 'boot');
    $showSizes = $isApparel || $isShoe;
    $totalStock = $showSizes ? $product->variants->sum('stock') : $product->stock;

    $isPants = str_contains($categoryName, 'pants') || str_contains($categoryName, 'bottom') || str_contains($categoryName, 'trouser') || str_contains($categoryName, 'jeans');
    $sizeChartImage = $isPants ? 'assets/images/PantsSizeChart.png' : 'assets/images/SizeChart.png';
    $sizeChartTitle = $isPants ? 'PureFit Pants Size Chart' : 'PureFit Apparel Size Chart';
@endphp

    <!-- Product Detail Page -->
    <div class="bg-[#f5f3ef] min-h-screen py-12">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center gap-2 text-[13px] text-gray-600">
                    <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                    <li>/</li>
                    <li><a href="{{ route('products') }}" class="hover:text-gray-900">Products</a></li>
                    <li>/</li>
                    <li class="text-gray-900">{{ $product->name }}</li>
                </ol>
            </nav>

            <!-- Product Detail Container -->
            <div class="bg-white border border-[#e8e5e0]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 p-8 lg:p-12">
                    <!-- Product Image -->
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="flex flex-col">
                        <!-- Category -->
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
                        <h1 class="font-serif-display text-[36px] text-gray-900 mb-4">
                            {{ $product->name }}
                        </h1>

                        <!-- Rating -->
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </div>
                            <span class="text-[14px] text-gray-600">4.8 (12 reviews)</span>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <span class="text-[32px] font-light text-gray-900">
                                ₱{{ number_format($product->retail_price, 2) }}
                            </span>
                            @if($product->is_wholesale_enabled && $product->wholesale_price > 0)
                                <div class="mt-2">
                                    <span class="text-[14px] text-gray-500 line-through">₱{{ number_format($product->wholesale_price, 2) }}</span>
                                    <span class="text-[12px] text-gray-600 ml-2">Wholesale from {{ $product->moq ?? 1 }} pcs</span>
                                </div>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-6" id="stock-status-container">
                            @if($totalStock == 0)
                                <span class="inline-flex items-center gap-2 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Out of Stock
                                </span>
                            @elseif($totalStock <= 10)
                                <span class="inline-flex items-center gap-2 text-orange-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Only {{ $totalStock }} left in stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 text-green-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    In Stock ({{ $totalStock }} available)
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <h2 class="text-[16px] font-semibold text-gray-900 mb-3">Description</h2>
                            <p class="text-[15px] text-gray-600 leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Available Sizes -->
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
                            $requiresSize = $sizes->isNotEmpty();
                        @endphp
                        @if($requiresSize)
                        <div class="mb-8" id="sizes-section">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-[16px] font-semibold text-gray-900">
                                    Select Size <span class="text-red-500">*</span>
                                </h2>
                                <button type="button" onclick="openSizeChartModal()" class="text-[12px] text-gray-500 hover:text-gray-900 underline flex items-center gap-1 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9h1m-1 3h1m-1 3h1m-1 3h1m3-12H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-3" />
                                    </svg>
                                    Size Chart
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2" id="detail-sizes-container">
                                @foreach($sizes as $size)
                                @php
                                    $stock = $sizeStocks[$size] ?? 0;
                                    $isOutOfStock = $stock <= 0;
                                @endphp
                                <button type="button"
                                    onclick="selectDetailSize('{{ $size }}', this)"
                                    data-stock="{{ $stock }}"
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    class="detail-size-btn px-4 py-2 text-[13px] font-medium border rounded transition-colors {{ $isOutOfStock ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-100' : 'border-gray-300 text-gray-700 hover:border-gray-900' }}">
                                    {{ $size }}
                                    
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="detail-selected-size" value="">
                            <p class="text-[12px] text-red-500 mt-2 hidden" id="detail-size-error">Please select a size before proceeding.</p>
                        </div>
                        @endif

                        <!-- Quantity and Add to Cart -->
                        @auth
                            <div class="mt-auto">
                                <div class="flex items-center gap-4 mb-4">
                                    <label class="text-[14px] font-medium text-gray-900">Quantity:</label>
                                    <div class="flex items-center gap-2">
                                        <button onclick="document.getElementById('quantity').value = Math.max(1, parseInt(document.getElementById('quantity').value) - 1)"
                                                class="w-10 h-10 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 12h-15" />
                                            </svg>
                                        </button>
                                        <input type="number"
                                               id="quantity"
                                               value="1"
                                               min="1"
                                               max="{{ $totalStock }}"
                                               class="w-20 px-3 py-2 text-center border border-[#e8e5e0] bg-[#f5f3ef] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900">
                                        <button onclick="document.getElementById('quantity').value = Math.min(document.getElementById('quantity').max, parseInt(document.getElementById('quantity').value) + 1)"
                                                class="w-10 h-10 flex items-center justify-center border border-[#e8e5e0] bg-[#f5f3ef] text-gray-700 hover:bg-gray-900 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <button class="add-to-cart-btn w-full btn-primary py-3 {{ $totalStock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-quantity-selector="quantity"
                                        {{ $totalStock == 0 ? 'disabled' : '' }}>
                                    <span class="btn-text">{{ $totalStock == 0 ? 'Out of Stock' : 'Add to Cart' }}</span>
                                    <span class="btn-loading hidden">
                                        <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        @endauth

                        @guest
                            <div class="mt-auto">
                                <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" 
                                   class="block w-full btn-primary py-3 text-center">
                                    Log In to Purchase
                                </a>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Size Chart Modal -->
    <div id="size-chart-modal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 transition-opacity duration-300">
        <!-- Modal Container -->
        <div class="relative w-full max-w-5xl scale-95 opacity-0 transition-all duration-300"
             id="size-chart-content">
            <!-- Close Button -->
            <button onclick="closeSizeChartModal()"
                    class="absolute -top-4 -right-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Modal Card -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-2xl border border-gray-100">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 bg-gradient-to-r from-gray-50 to-white">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $sizeChartTitle }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Find your perfect fit before ordering</p>
                    </div>
                </div>
                <!-- Image -->
                <div class="bg-gray-50 p-4 md:p-6 flex items-center justify-center">
                    <img src="{{ asset($sizeChartImage) }}"
                         alt="Size Chart"
                         class="w-full max-h-[80vh] object-contain rounded-2xl border border-gray-200 shadow-sm hover:scale-[1.01] transition-transform duration-300">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function selectDetailSize(size, btn) {
    // Deselect all
    document.querySelectorAll('.detail-size-btn').forEach(b => {
        b.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
        b.classList.add('border-gray-300', 'text-gray-700');
    });
    // Highlight selected
    btn.classList.remove('border-gray-300', 'text-gray-700');
    btn.classList.add('bg-gray-900', 'text-white', 'border-gray-900');
    // Store value
    document.getElementById('detail-selected-size').value = size;
    // Hide error
    const err = document.getElementById('detail-size-error');
    if (err) err.classList.add('hidden');
    
    // Update stock display and max quantity
    const stock = parseInt(btn.dataset.stock) || 0;
    const stockContainer = document.getElementById('stock-status-container');
    if (stockContainer) {
        if (stock === 0) {
            stockContainer.innerHTML = `<span class="inline-flex items-center gap-2 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Out of Stock
                                </span>`;
        } else if (stock <= 10) {
            stockContainer.innerHTML = `<span class="inline-flex items-center gap-2 text-orange-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Only ${stock} left in stock
                                </span>`;
        } else {
            stockContainer.innerHTML = `<span class="inline-flex items-center gap-2 text-green-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    In Stock (${stock} available)
                                </span>`;
        }
    }
    
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.max = stock;
        if (parseInt(quantityInput.value) > stock) {
            quantityInput.value = stock > 0 ? stock : 1;
        }
    }
    
    // Update Add to Cart button state
    const addBtn = document.querySelector('.add-to-cart-btn');
    if (addBtn) {
        if (stock === 0) {
            addBtn.disabled = true;
            addBtn.classList.add('opacity-50', 'cursor-not-allowed');
            addBtn.querySelector('.btn-text').textContent = 'Out of Stock';
        } else {
            addBtn.disabled = false;
            addBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            addBtn.querySelector('.btn-text').textContent = 'Add to Cart';
        }
    }
}

function openSizeChartModal() {
    const modal = document.getElementById('size-chart-modal');
    const content = document.getElementById('size-chart-content');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeSizeChartModal() {
    const modal = document.getElementById('size-chart-modal');
    const content = document.getElementById('size-chart-content');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

document.addEventListener('DOMContentLoaded', () => {
    const firstBtn = document.querySelector('.detail-size-btn:not([disabled])');
    if (firstBtn) {
        firstBtn.click();
    }
});
</script>
@endpush
