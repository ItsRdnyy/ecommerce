@extends('layouts.app')

@section('title', 'PureFit Apparel - Modern Style. Everyday You.')

@section('content')


    <!-- Hero Section -->
    <section class="relative bg-[#f5f3ef] overflow-hidden">
        <div class="max-w-[1400px] mx-auto">
            <div class="grid lg:grid-cols-2 min-h-[600px] lg:min-h-[700px]">
                <!-- Text Content -->
                <div class="flex flex-col justify-center px-6 lg:px-16 py-16 lg:py-0 order-2 lg:order-1 relative z-10 animate-fade-in">
                    <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-gray-600 mb-5">
                        Modern Style. Everyday You.
                    </p>
                    <h1 class="font-serif-display text-[48px] sm:text-[60px] lg:text-[72px] leading-[1.05] text-gray-900 mb-6">
                        Made for<br>every moment.
                    </h1>
                    <p class="text-gray-600 text-[15px] leading-relaxed mb-8 max-w-md">
                        Timeless essentials. Thoughtful details.<br>
                        Clothing that fits your life.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('products') }}" class="btn-primary">
                            Shop Men
                        </a>
                        <a href="{{ route('products') }}" class="btn-secondary">
                            Shop Women
                        </a>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative order-1 lg:order-2 h-[350px] sm:h-[450px] md:h-[550px] lg:h-auto">
                    <img src="{{ asset('assets/images/hero-fashion.jpg') }}" 
                         alt="Fashion models wearing modern casual clothing" 
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-[#ebe8e3] py-16 lg:py-20">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="text-center mb-12">
                <h2 class="text-[28px] sm:text-[32px] font-semibold text-gray-900 mb-4">Why Choose PureFit</h2>
                <p class="text-[16px] text-gray-600 max-w-2xl mx-auto">Experience the perfect blend of style, comfort, and sustainability</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-[#f5f3ef] rounded-xl flex items-center justify-center mb-6 group-hover:bg-gray-900 transition-colors duration-300">
                        <svg class="w-7 h-7 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold tracking-[0.12em] uppercase text-gray-900 mb-3">Sustainable Materials</h3>
                    <p class="text-[14px] text-gray-600 leading-relaxed">Eco-friendly fabrics that are better for you and the planet, without compromising on style or comfort.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-[#f5f3ef] rounded-xl flex items-center justify-center mb-6 group-hover:bg-gray-900 transition-colors duration-300">
                        <svg class="w-7 h-7 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.974 5.974 0 0 1-2.63-3.547 5.99 5.99 0 0 0-1.925 3.468A3.75 3.75 0 0 0 12 18Z" />
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold tracking-[0.12em] uppercase text-gray-900 mb-3">Timeless Design</h3>
                    <p class="text-[14px] text-gray-600 leading-relaxed">Classic pieces you'll wear today and for years to come. Trends fade, style is eternal.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-[#f5f3ef] rounded-xl flex items-center justify-center mb-6 group-hover:bg-gray-900 transition-colors duration-300">
                        <svg class="w-7 h-7 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold tracking-[0.12em] uppercase text-gray-900 mb-3">Premium Quality</h3>
                    <p class="text-[14px] text-gray-600 leading-relaxed">Built to last with meticulous attention to every detail. Quality you can feel and trust.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-[#f5f3ef] rounded-xl flex items-center justify-center mb-6 group-hover:bg-gray-900 transition-colors duration-300">
                        <svg class="w-7 h-7 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold tracking-[0.12em] uppercase text-gray-900 mb-3">Easy Returns</h3>
                    <p class="text-[14px] text-gray-600 leading-relaxed">Hassle-free returns within 30 days. Shop with confidence knowing we've got you covered.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-16 lg:py-20 bg-white">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="text-center mb-12">
                <h2 class="text-[28px] sm:text-[32px] font-semibold text-gray-900 mb-4">New Arrival Products</h2>
                <p class="text-[16px] text-gray-600 max-w-2xl mx-auto">Discover our curated collection of premium essentials</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                @forelse($featuredProducts as $product)
                    <div class="product-card group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                        <div class="relative overflow-hidden flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-64 bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-400 text-[14px]">No Image</span>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            @php
                                $catName = strtolower($product->category->name ?? '');
                                $isApparelLP = str_contains($catName, 'clothing') || str_contains($catName, 'shirt') || str_contains($catName, 'pants') || str_contains($catName, 'dress') || str_contains($catName, 'apparel');
                                $isShoeLP = str_contains($catName, 'shoe') || str_contains($catName, 'footwear') || str_contains($catName, 'sneaker') || str_contains($catName, 'boot');
                                $showSizesLP = $isApparelLP || $isShoeLP;
                                $variantsStockLP = $product->variants->count() > 0 ? $product->variants->sum('stock') : 0;
                                $totalStock = $showSizesLP && $product->variants->count() > 0 ? $variantsStockLP : $product->stock;
                            @endphp
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex px-3 py-1 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $totalStock == 0 ? 'bg-red-600 text-white' : 'bg-gray-900 text-white' }}">
                                    {{ $totalStock == 0 ? 'Out of Stock' : 'New' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <!-- Category & Gender -->
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[12px] text-gray-600">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $product->gender == 'men' ? 'bg-blue-100 text-blue-800' : ($product->gender == 'women' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($product->gender) }}
                                </span>
                            </div>
                            <h3 class="text-[16px] font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>

                            <!-- Available Sizes -->
                            @php
                                if ($isShoeLP) {
                                    $lpSizes = collect(range(38, 48));
                                    $lpSizeStocks = [];
                                    foreach ($lpSizes as $size) {
                                        $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                        $lpSizeStocks[$size] = $variant ? $variant->stock : 0;
                                    }
                                } elseif ($isApparelLP && $product->variants) {
                                    $lpSizes = $product->variants->map(fn($v) => data_get($v->attributes, 'size'))->filter()->unique()->values();
                                    $lpSizeStocks = [];
                                    foreach ($lpSizes as $size) {
                                        $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                        $lpSizeStocks[$size] = $variant ? $variant->stock : 0;
                                    }
                                } else {
                                    $lpSizes = collect();
                                    $lpSizeStocks = [];
                                }
                            @endphp
                            @if($lpSizes->isNotEmpty())
                            <div class="mb-3">
                                <p class="text-[11px] text-gray-500 mb-1">Size <span class="text-red-500">*</span></p>
                                <div class="flex flex-wrap gap-1" id="sizes-{{ $product->id }}">
                                    @foreach($lpSizes as $size)
                                    @php
                                        $stock = $lpSizeStocks[$size] ?? 0;
                                        $isOutOfStock = $stock <= 0;
                                        $variant = $product->variants->first(fn($v) => data_get($v->attributes, 'size') == $size);
                                        $price = $variant ? $variant->price : null;
                                    @endphp
                                    <button type="button"
                                            onclick="LandingPage.selectSize({{ $product->id }}, '{{ $size }}', this)"
                                            data-stock="{{ $stock }}"
                                            data-price="{{ $price }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}
                                            class="size-btn inline-flex items-center px-2 py-1 text-[11px] font-medium border rounded transition-colors {{ $isOutOfStock ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-100' : 'border-gray-200 text-gray-600 bg-gray-50 hover:border-gray-900' }}">
                                        {{ $size }}
                                    </button>
                                    @endforeach
                                </div>
                                <input type="hidden" id="selected-size-{{ $product->id }}" value="">
                                <p class="text-[10px] text-red-500 mt-1 hidden" id="size-error-{{ $product->id }}">Please select a size.</p>
                            </div>
                            @endif

                            <div class="flex items-center justify-between mb-4 mt-auto">
                                <div class="flex items-center gap-2">
                                    <span id="price-display-{{ $product->id }}" data-base-price="{{ $product->retail_price }}" class="text-[20px] font-bold text-gray-900">₱{{ number_format($product->retail_price, 2) }}</span>
                                    @if($product->discountTiers && $product->discountTiers->count() > 0)
                                        <span class="text-[11px] font-semibold text-green-700 uppercase tracking-wider">Bulk Savings Active</span>
                                    @endif
                                </div>
                                @php
                                    $approvedReviews = $product->reviews->where('status', 'approved');
                                    $reviewsCount = $approvedReviews->count();
                                    $averageRating = $reviewsCount > 0 ? round($approvedReviews->avg('rating'), 1) : null;
                                @endphp
                                @if($reviewsCount > 0)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <span class="text-[12px] font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                        <span class="text-[11px] text-gray-500">({{ $reviewsCount }})</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-400">
                                        <span class="text-[11px] italic">No reviews</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <button onclick="LandingPage.addToCart({{ $product->id }})"
                                        id="add-to-cart-{{ $product->id }}"
                                        class="add-to-cart-btn flex-1 btn-primary text-[10px] py-2.5 {{ $totalStock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $totalStock == 0 ? 'disabled' : '' }}>
                                    <span class="btn-text">{{ $totalStock == 0 ? 'Out of Stock' : 'Add to Cart' }}</span>
                                    <span class="btn-loading hidden">
                                        <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
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
                @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-[14px] text-gray-500">No products available at the moment.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('products') }}" class="btn-secondary">
                    View All Products
                    <svg class="w-4 h-4 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 lg:py-20 bg-[#f5f3ef]">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="text-center mb-12">
                <h2 class="text-[28px] sm:text-[32px] font-semibold text-gray-900 mb-4">Get in Touch</h2>
                <p class="text-[16px] text-gray-600 max-w-2xl mx-auto">We'd love to hear from you. Reach out with any questions or feedback.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <form class="space-y-6">
                        <div>
                            <label for="name" class="block text-[12px] font-semibold text-gray-900 mb-2 tracking-[0.12em] uppercase">Your Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20 transition-all duration-200" placeholder="Enter Your Name">
                        </div>
                        <div>
                            <label for="email" class="block text-[12px] font-semibold text-gray-900 mb-2 tracking-[0.12em] uppercase">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20 transition-all duration-200" placeholder="example@gmail.com">
                        </div>
                        <div>
                            <label for="message" class="block text-[12px] font-semibold text-gray-900 mb-2 tracking-[0.12em] uppercase">Message</label>
                            <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-2 focus:ring-gray-900/20 transition-all duration-200" placeholder="Tell us how we can help..."></textarea>
                        </div>
                        <button type="submit" class="w-full btn-primary">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-[18px] font-semibold text-gray-900">Visit Our Store</h3>
                        </div>
                        <p class="text-[14px] text-gray-600">123 Fashion Street<br>Makati, NY 10001<br>Philippines</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <h3 class="text-[18px] font-semibold text-gray-900">Call Us</h3>
                        </div>
                        <p class="text-[14px] text-gray-600">09223086018<br>Mon-Fri: 9am-6pm PST<br>Sat-Sun: 10am-4pm PST</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-[18px] font-semibold text-gray-900">Email Us</h3>
                        </div>
                        <p class="text-[14px] text-gray-600">support@purefitapparel.com<br>hello@purefitapparel.com<br>We respond within 24 hours</p>
                    </div>

                    <!-- Social Media Links -->
                    <div class="flex justify-center space-x-4 pt-4">
                        <a href="#" class="w-12 h-12 bg-gray-900 text-white rounded-xl flex items-center justify-center hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-900 text-white rounded-xl flex items-center justify-center hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-900 text-white rounded-xl flex items-center justify-center hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <!-- Brand & Socials -->
                <div class="space-y-4">
                    <h3 class="text-[20px] font-bold tracking-[0.1em] uppercase">PureFit</h3>
                    <p class="text-gray-400 text-[13px] leading-relaxed">Modern Style. Everyday You.</p>
                    <div class="flex space-x-3 pt-2">
                        <a href="#" class="w-9 h-9 bg-gray-800 text-gray-300 rounded-lg flex items-center justify-center hover:bg-white hover:text-black transition-colors duration-200">
                            <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-gray-800 text-gray-300 rounded-lg flex items-center justify-center hover:bg-white hover:text-black transition-colors duration-200">
                            <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-gray-800 text-gray-300 rounded-lg flex items-center justify-center hover:bg-white hover:text-black transition-colors duration-200">
                            <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- <!-- Visit Store -->
                <div>
                    <h4 class="text-[12px] font-semibold tracking-[0.15em] uppercase text-gray-400 mb-4">Visit Our Store</h4>
                    <p class="text-gray-300 text-[13px] leading-relaxed">
                        123 Fashion Street<br>
                        Makati, NY 10001<br>
                        Philippines
                    </p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-[12px] font-semibold tracking-[0.15em] uppercase text-gray-400 mb-4">Contact Info</h4>
                    <div class="text-gray-300 text-[13px] space-y-3">
                        <p>
                            <span class="block text-gray-500 text-[10px] uppercase tracking-wider mb-0.5">Phone</span>
                            <span class="font-medium text-gray-200">09223086018</span>
                        </p>
                        <p>
                            <span class="block text-gray-500 text-[10px] uppercase tracking-wider mb-0.5">Email</span>
                            <a href="mailto:support@purefitapparel.com" class="text-gray-200 hover:text-white transition-colors block">support@purefitapparel.com</a>
                            <a href="mailto:hello@purefitapparel.com" class="text-gray-200 hover:text-white transition-colors block">hello@purefitapparel.com</a>
                        </p>
                    </div>
                </div> --}}

                <!-- Quick Links -->
                <div>
                    <h4 class="text-[12px] font-semibold tracking-[0.15em] uppercase text-gray-400 mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-[13px]">
                        <li><a href="{{ route('products') }}" class="text-gray-300 hover:text-white transition-colors">Shop All</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-305 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-[12px]">© 2024 PureFit Apparel. All rights reserved.</p>
                <div class="flex space-x-6 text-[12px] text-gray-500">
                    <span>Mon-Fri: 9am-6pm PST</span>
                    <span>Sat-Sun: 10am-4pm PST</span>
                </div>
            </div>
        </div>
    </footer>

@endsection

@push('scripts')
<script src="{{ asset('js/landingpage.js') }}" defer></script>
@endpush 