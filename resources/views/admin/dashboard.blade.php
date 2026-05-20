@extends('admin.layout')

@section('title', 'Dashboard')
@section('nav-overview', 'bg-[#f5f3ef] text-gray-900')

@section('content')

    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Dashboard</h1>
        <p class="text-[14px] text-gray-600">Overview of your platform</p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-[13px] rounded">
            {{ session('success') }}
        </div>
    @endif

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

    <!-- Revenue & Commission Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

        <!-- Revenue Cards -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-[#e8e5e0] px-6 py-5">
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Revenue</p>
                <p class="text-[28px] font-light text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="bg-white border border-[#e8e5e0] px-6 py-5">
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending Users</p>
                <p class="text-[28px] font-light text-gray-900">{{ \App\Models\User::where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white border border-[#e8e5e0] px-6 py-5">
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Current Commission Rate</p>
                <p class="text-[28px] font-light text-gray-900">{{ $commissionRate }}%</p>
            </div>
            <div class="bg-white border border-[#e8e5e0] px-6 py-5">
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Current Platform Fee</p>
                <p class="text-[28px] font-light text-gray-900">₱{{ number_format($platformFee, 2) }}</p>
            </div>
        </div>

        <!-- Commission Settings Form -->
        <div class="bg-white border border-[#e8e5e0]">
            <div class="px-6 py-4 border-b border-[#e8e5e0]">
                <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Commission Settings</h2>
            </div>
            <form method="POST" action="{{ route('admin.revenue.update') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Commission Rate (%)</label>
                    <input type="number" name="commission_rate" value="{{ $commissionRate }}" step="0.01" min="0" max="100"
                        class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-1.5">Platform Fee per Transaction (₱)</label>
                    <input type="number" name="platform_fee" value="{{ $platformFee }}" step="0.01" min="0"
                        class="w-full border border-[#e8e5e0] rounded px-4 py-2.5 text-[14px] focus:outline-none focus:border-gray-400">
                </div>
                <button type="submit"
                    class="w-full px-5 py-2.5 bg-[#111] text-white text-[12px] font-semibold tracking-[0.1em] uppercase rounded hover:bg-gray-800 transition-colors">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

@endsection
