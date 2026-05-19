<div class="w-full max-w-[1200px] mx-auto overflow-x-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
    @forelse ($products as $product)
        <div class="bg-white border border-[#e8e5e0] rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Product Image -->
            <div class="aspect-square bg-gray-100 relative">
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
            <div class="p-4">
                <h3 class="font-semibold text-[15px] text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                
                <!-- Category & Gender -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[12px] text-gray-600">{{ $product->category->name ?? '—' }}</span>
                    <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase rounded-full {{ $product->gender == 'men' ? 'bg-blue-100 text-blue-800' : ($product->gender == 'women' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($product->gender) }}
                    </span>
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
                    $sizes = ($showSizes && $product->variants) ? $product->variants->map(fn($v) => $v->attributes['size'] ?? null)->filter()->unique()->values() : collect();
                @endphp
                @if($showSizes && $sizes->isNotEmpty())
                <div class="mb-3">
                    <p class="text-[11px] text-gray-500 mb-1">Available Sizes</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($sizes as $size)
                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium border border-gray-200 rounded text-gray-600 bg-gray-50">
                            {{ $size }}
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
                <div class="flex gap-2">
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
