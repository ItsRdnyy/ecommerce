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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white border border-[#e8e5e0] p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-[14px] font-semibold text-gray-900">Store Income Comparison</h2>
                <span class="text-[12px] text-gray-500">Total Revenue in ₱</span>
            </div>
            <div class="h-[350px] relative">
                <canvas id="storeIncomeChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-[#e8e5e0] p-6">
            <h2 class="text-[14px] font-semibold text-gray-900 mb-4">System Overview</h2>
            <div class="space-y-4">
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-[24px] font-light text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="rounded border border-[#e8e5e0] p-4">
                    <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending Users</p>
                    <p class="text-[24px] font-light text-gray-900">{{ \App\Models\User::where('status', 'pending')->count() }}</p>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('storeIncomeChart').getContext('2d');
        const labels = {!! json_encode($storeIncomes->pluck('name')) !!};
        const data = {!! json_encode($storeIncomes->pluck('income')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Store Income',
                    data: data,
                    backgroundColor: 'rgba(17, 17, 17, 0.8)',
                    hoverBackgroundColor: '#111',
                    borderRadius: 4,
                    borderSkipped: false,
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
                            color: '#666'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
