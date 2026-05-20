@extends('admin.layout')
@section('title', 'Analytics Dashboard')
@section('nav-analytics', 'bg-[#f5f3ef] text-gray-900')
@section('content')
<div class="mb-8"><h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Analytics Dashboard</h1><p class="text-[14px] text-gray-600">Platform-wide metrics and performance.</p></div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Top Business</p><p class="text-[16px] font-medium text-gray-900">{{ $topBusiness ? $topBusiness->name : '—' }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Top Product</p><p class="text-[16px] font-medium text-gray-900">{{ $topProduct ? $topProduct->name : '—' }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Conversion Rate</p><p class="text-[16px] font-medium text-gray-900">{{ $conversionRate }}%</p></div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
    <div class="bg-white border border-[#e8e5e0]">
        <div class="px-6 py-4 border-b border-[#e8e5e0]"><h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Order Growth</h2></div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between"><span class="text-[14px] text-gray-600">Retail Orders</span><span class="text-[14px] font-medium text-gray-900">{{ $retailGrowth }}</span></div>
            <div class="flex justify-between"><span class="text-[14px] text-gray-600">B2B Orders</span><span class="text-[14px] font-medium text-gray-900">{{ $b2bGrowth }}</span></div>
        </div>
    </div>
    <div class="bg-white border border-[#e8e5e0]">
        <div class="px-6 py-4 border-b border-[#e8e5e0]"><h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Stock Performance</h2></div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between"><span class="text-[14px] text-gray-600">Products Listed</span><span class="text-[14px] font-medium text-gray-900">{{ \App\Models\Product::count() }}</span></div>
            <div class="flex justify-between"><span class="text-[14px] text-gray-600">Out of Stock</span><span class="text-[14px] font-medium text-gray-900">{{ \App\Models\Product::where('stock', 0)->count() }}</span></div>
        </div>
    </div>
</div>
@endsection
