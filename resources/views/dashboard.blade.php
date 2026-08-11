<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-900 leading-tight">Business Analytics</h2>
    </x-slot>

    {{-- Include Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- KPI Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Today's Revenue --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Today's Revenue</p>
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-black text-slate-900">${{ number_format($todayRevenue, 2) }}</p>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        @if($revenueGrowth > 0)
                            <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                +{{ number_format($revenueGrowth, 1) }}%
                            </span>
                        @elseif($revenueGrowth < 0)
                            <span class="inline-flex items-center text-[10px] font-bold text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                {{ number_format($revenueGrowth, 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">
                                0.0%
                            </span>
                        @endif
                        <span class="text-[11px] font-medium text-slate-400">vs yesterday (${{ number_format($yesterdayRevenue, 2) }})</span>
                    </div>
                </div>

                {{-- Total Revenue (All Time) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Total Revenue (All Time)</p>
                        <p class="text-3xl font-black text-slate-900">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-slate-400">{{ number_format($totalSalesCount) }} total sales processed</span>
                    </div>
                </div>

                {{-- Active Products --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Active Products</p>
                        <p class="text-3xl font-black text-slate-900">{{ number_format($totalProducts) }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-amber-500">{{ $lowStockProducts->count() }} items low on stock</span>
                    </div>
                </div>

                {{-- Average Order Value --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Avg Order Value</p>
                        <p class="text-3xl font-black text-slate-900">${{ $totalSalesCount > 0 ? number_format($totalRevenue / $totalSalesCount, 2) : '0.00' }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-slate-400">Based on lifetime sales</span>
                    </div>
                </div>

            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- 30-Day Revenue Trend (Line Chart) --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 lg:col-span-2 overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Revenue Trend (30 Days)</h3>
                    </div>
                    <div class="p-5 flex-1 relative min-h-[300px]">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Sales by Category (Doughnut Chart) --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Revenue by Category</h3>
                    </div>
                    <div class="p-5 flex-1 relative min-h-[300px] flex items-center justify-center">
                        @if($categorySales->count() > 0)
                            <canvas id="categoryChart"></canvas>
                        @else
                            <div class="text-center text-slate-400">
                                <p class="text-sm">No sales data yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Lists Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                {{-- Top 5 Best Sellers (by volume) --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Top 5 Best Sellers</h3>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($topSelling as $item)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $item->product->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-400">{{ $item->product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-slate-900">{{ number_format($item->total_qty) }}</span>
                                <span class="block text-[10px] text-slate-400 uppercase tracking-wider">Units</span>
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-400 text-sm">No data available</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Top 5 Most Profitable --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Top 5 Most Profitable</h3>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($topProfitable as $item)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $item->product->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-400">Margin: ${{ number_format(($item->product->price ?? 0) - ($item->product->cost_price ?? 0), 2) }}/ea</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-emerald-600">${{ number_format($item->total_profit, 2) }}</span>
                                <span class="block text-[10px] text-slate-400 uppercase tracking-wider">Profit</span>
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-400 text-sm">No data available</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Low Stock Alerts --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Low Stock Alerts</h3>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">View All</a>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($lowStockProducts as $product)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex justify-between items-center">
                            <div class="flex-1 pr-4">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $product->barcode ?? 'No Barcode' }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($product->stock_quantity === 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Out of Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                        {{ $product->stock_quantity }} Left
                                    </span>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-400 text-sm">Inventory is healthy!</li>
                        @endforelse
                    </ul>
                    {{-- Expiry Alerts --}}
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Expiry Alerts</h3>
                        <a href="{{ route('reports.inventory') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">View All</a>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($expiringProducts as $product)
                        <li class="p-4 hover:bg-slate-50 transition-colors flex justify-between items-center">
                            <div class="flex-1 pr-4">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $product->name }}</p>
                                <p class="text-[11px] text-slate-400">Exp: {{ \Carbon\Carbon::parse($product->earliest_expiry_date)->format('M d, Y') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($product->expiry_status === 'expired')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                                        Soon
                                    </span>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-400 text-sm">No expiry warnings!</li>
                        @endforelse
                    </ul>
                </div>

            </div>

            </div>

        </div>
    </div>

    {{-- Chart Initialization --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup default styling for Chart.js to match Impeccable standard
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b'; // slate-500
            
            // 1. Revenue Line Chart
            const revCtx = document.getElementById('revenueChart');
            if (revCtx) {
                new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartDates) !!},
                        datasets: [{
                            label: 'Revenue ($)',
                            data: {!! json_encode($chartRevenue) !!},
                            borderColor: '#2563eb', // blue-600
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2563eb',
                            pointHoverBackgroundColor: '#2563eb',
                            pointHoverBorderColor: '#fff',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.3 // Smooth curves
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a', // slate-900
                                titleFont: { size: 13 },
                                bodyFont: { size: 14, weight: 'bold' },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9', // slate-100
                                    drawBorder: false,
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    },
                                    font: { size: 11 }
                                }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: {
                                    font: { size: 10 },
                                    maxTicksLimit: 10 // Prevent crowding
                                }
                            }
                        }
                    }
                });
            }

            // 2. Category Sales Doughnut Chart
            const catCtx = document.getElementById('categoryChart');
            if (catCtx) {
                const catLabels = {!! json_encode($categorySales->pluck('name')) !!};
                const catData = {!! json_encode($categorySales->pluck('category_total')) !!};
                
                if (catLabels.length > 0) {
                    new Chart(catCtx, {
                        type: 'doughnut',
                        data: {
                            labels: catLabels,
                            datasets: [{
                                data: catData,
                                backgroundColor: [
                                    '#2563eb', // blue-600
                                    '#0f172a', // slate-900
                                    '#10b981', // emerald-500
                                    '#f59e0b', // amber-500
                                    '#8b5cf6', // violet-500
                                    '#ef4444', // red-500
                                    '#64748b'  // slate-500
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%', // Thin doughnut
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        font: { size: 11, weight: '600' }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            return ' $' + context.parsed.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
