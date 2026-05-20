@extends('business.layout')

@section('title', 'Business Dashboard')
@section('nav-overview', 'bg-[#f5f3ef] text-gray-900')

@section('content')

    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Business Dashboard</h1>
        <p class="text-[14px] text-gray-600">
            @if($business)
                {{ $business->business_name }} — Seller Control Panel
            @else
                Welcome, {{ auth()->user()->name }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white border border-[#e8e5e0] p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-[14px] font-semibold text-gray-900">Sales Overview (Last 30 Days)</h2>
                <span class="text-[12px] text-gray-500">Revenue in ₱</span>
            </div>
            <div class="h-[300px] relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-[#e8e5e0] p-6">
            <h2 class="text-[14px] font-semibold text-gray-900 mb-4">Quick Stats</h2>
            <div class="grid grid-cols-1 gap-4">
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Products</p>
                    <p class="text-[24px] font-light text-gray-900">{{ $totalProducts }}</p>
                </div>
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Active Products</p>
                    <p class="text-[24px] font-light text-gray-900">{{ $activeProducts }}</p>
                </div>
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending Orders</p>
                    <p class="text-[24px] font-light text-gray-900">{{ $incomingRetail + $bulkRequests }}</p>
                </div>
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Earnings</p>
                    <p class="text-[24px] font-light text-gray-900">₱{{ number_format($totalEarnings, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <div class="bg-white border border-[#e8e5e0] p-6">
            <h2 class="text-[14px] font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                    <div class="rounded border border-[#e8e5e0] p-4">
                        <p class="text-[13px] font-semibold text-gray-600">Order #{{ $order->id }}</p>
                        <p class="text-[14px] text-gray-900">{{ ucfirst($order->type) }} • {{ $order->status }}</p>
                        <p class="text-[12px] text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="rounded border border-[#e8e5e0] p-4 text-[13px] text-gray-500">
                        No recent orders yet.
                    </div>
                @endforelse
            </div>
        </div>
        <div class="space-y-6">
            <a href="{{ route('business.profile') }}" class="block rounded-xl border border-[#e8e5e0] bg-white p-6 hover:bg-[#f5f3ef] transition-colors">
                <h3 class="text-[15px] font-semibold text-gray-900 mb-2">Business Profile</h3>
                <p class="text-[14px] text-gray-600">Manage name, contact, address, and logo.</p>
            </a>
            <a href="{{ route('business.products') }}" class="block rounded-xl border border-[#e8e5e0] bg-white p-6 hover:bg-[#f5f3ef] transition-colors">
                <h3 class="text-[15px] font-semibold text-gray-900 mb-2">Product Management</h3>
                <p class="text-[14px] text-gray-600">Add, edit, and delete products with images and pricing.</p>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <a href="{{ route('business.orders') }}" class="block rounded-xl border border-[#e8e5e0] bg-white p-6 hover:bg-[#f5f3ef] transition-colors">
            <h3 class="text-[15px] font-semibold text-gray-900 mb-2">Orders / Requests</h3>
            <p class="text-[14px] text-gray-600">View and update order status for retail and bulk requests.</p>
        </a>
        <a href="{{ route('business.settings') }}" class="block rounded-xl border border-[#e8e5e0] bg-white p-6 hover:bg-[#f5f3ef] transition-colors">
            <h3 class="text-[15px] font-semibold text-gray-900 mb-2">Settings</h3>
            <p class="text-[14px] text-gray-600">Change your password and account controls.</p>
        </a>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = {!! json_encode($monthlyRevenue->pluck('date')) !!};
        const data = {!! json_encode($monthlyRevenue->pluck('revenue')) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    borderColor: '#111',
                    backgroundColor: 'rgba(17, 17, 17, 0.05)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#111',
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#111',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e8e5e0',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#666',
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#666',
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
