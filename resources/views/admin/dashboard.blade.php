@extends('admin.layout')

@section('title', 'Dashboard')
@section('nav-overview', 'bg-[#f5f3ef] text-gray-900')

@section('content')

    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Dashboard</h1>
        <p class="text-[14px] text-gray-600">Overview of your platform</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Users</p>
            <p class="text-[28px] font-light text-gray-900">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Businesses</p>
            <p class="text-[28px] font-light text-gray-900">{{ $totalBusinesses }}</p>
        </div>
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Products</p>
            <p class="text-[28px] font-light text-gray-900">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Orders</p>
            <p class="text-[28px] font-light text-gray-900">{{ $retailOrders + $b2bOrders }}</p>
        </div>
    </div>

    <!-- System Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Revenue</p>
            <p class="text-[28px] font-light text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white border border-[#e8e5e0] px-6 py-5">
            <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending Users</p>
            <p class="text-[28px] font-light text-gray-900">{{ \App\Models\User::where('status', 'pending')->count() }}</p>
        </div>
    </div>

@endsection

@push('scripts')
@endpush
