<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">Business Analytics</h2>
            
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <label for="range" class="text-sm font-bold text-slate-500 uppercase tracking-wider hidden sm:block">Timeframe:</label>
                <select name="range" id="range" onchange="this.form.submit()" class="text-sm font-bold text-slate-900 bg-white border-slate-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 py-2 pl-3 pr-8 cursor-pointer">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $range === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All Time</option>
                </select>
            </form>
        </div>
    </x-slot>

    {{-- Include Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- KPI Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Period Revenue --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-xl p-5 border border-slate-200 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1">Period Revenue</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white">${{ number_format($currentRevenue, 2) }}</p>
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
                        <span class="text-[11px] font-medium text-slate-500">vs prev period</span>
                    </div>
                </div>

                {{-- Transactions --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-xl p-5 border border-slate-200 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1">Transactions</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalSalesCount) }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-slate-500">Total sales this period</span>
                    </div>
                </div>

                {{-- Average Order Value --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-xl p-5 border border-slate-200 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1">Avg Order Value</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white">${{ number_format($averageTransactionValue, 2) }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-slate-500">Per transaction</span>
                    </div>
                </div>

                {{-- Active Products --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-xl p-5 border border-slate-200 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-1">Active Products</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalProducts) }}</p>
                    </div>
                    <div class="mt-3">
                        <span class="text-[11px] font-medium text-amber-500">{{ $lowStockProducts->count() }} items low on stock</span>
                    </div>
                </div>

            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- 30-Day Revenue Trend (Line Chart) --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 lg:col-span-2 overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Revenue Trend (Last 30 Days)</h3>
                    </div>
                    <div class="p-5 flex-1 relative min-h-[300px]">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Sales by Category (Doughnut Chart) --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Revenue by Category</h3>
                    </div>
                    <div class="p-5 flex-1 relative min-h-[300px] flex items-center justify-center">
                        @if($categorySales->count() > 0)
                            <canvas id="categoryChart"></canvas>
                        @else
                            <div class="text-center text-slate-400">
                                <p class="text-sm">No sales data in period.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Staff & Methods Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Sales by Cashier --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Sales by Cashier</h3>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-4">
                            @forelse($cashierSales as $cs)
                                @php
                                    $maxAmount = $cashierSales->max('total_amount');
                                    $percent = $maxAmount > 0 ? ($cs->total_amount / $maxAmount) * 100 : 0;
                                @endphp
                                <li>
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $cs->user->name ?? 'Unknown' }}</span>
                                        <div class="text-right">
                                            <span class="text-sm font-black text-slate-900 dark:text-white">${{ number_format($cs->total_amount, 2) }}</span>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase ml-1">({{ $cs->count }} Txns)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center text-slate-400 text-sm">No sales in this period.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Sales by Payment Method --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Sales by Payment Method</h3>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-4">
                        @forelse($paymentMethods as $pm)
                            <div class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/5 rounded-lg p-4 flex flex-col justify-center items-center">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">{{ ucfirst($pm->payment_method) }}</span>
                                <span class="text-2xl font-black text-slate-900 dark:text-white">${{ number_format($pm->total_amount, 2) }}</span>
                                <span class="text-xs font-medium text-slate-400 mt-1">{{ $pm->count }} transactions</span>
                            </div>
                        @empty
                            <div class="col-span-2 text-center text-slate-400 text-sm py-6">No data in this period.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Lists Row (Products) --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                {{-- Top 5 Best Sellers --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Top 5 Best Sellers</h3>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($topSelling as $item)
                        <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">{{ $item->product->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-slate-900">{{ number_format($item->total_qty) }}</span>
                                <span class="block text-[10px] text-slate-400 uppercase tracking-wider">Units</span>
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-600 text-sm">No data available</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Top 5 Worst Sellers --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Top 5 Worst Sellers</h3>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($worstSelling as $item)
                        <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">{{ $item->product->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-red-600">{{ number_format($item->total_qty) }}</span>
                                <span class="block text-[10px] text-slate-400 uppercase tracking-wider">Units</span>
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-slate-400 text-sm">No data available</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Top 5 Most Profitable --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Most Profitable</h3>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($topProfitable as $item)
                        <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">{{ $item->product->name ?? 'Unknown' }}</p>
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

                {{-- Alerts (Low Stock / Expiring) --}}
                <div class="flex flex-col gap-6">
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden flex-1">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Low Stock</h3>
                            <a href="{{ route('products.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">All</a>
                        </div>
                        <ul class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse($lowStockProducts as $product)
                            <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex justify-between items-center">
                                <div class="flex-1 pr-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $product->name }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($product->stock_quantity === 0)
                                        <span class="text-[10px] font-bold text-red-600">OUT</span>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-600">{{ $product->stock_quantity }} LEFT</span>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="p-6 text-center text-slate-400 text-sm">Inventory is healthy!</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden flex-1">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Expiring Soon</h3>
                        </div>
                        <ul class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse($expiringProducts as $product)
                            <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex justify-between items-center">
                                <div class="flex-1 pr-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $product->name }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($product->expiry_status === 'expired')
                                        <span class="text-[10px] font-bold text-red-600">EXPIRED</span>
                                    @else
                                        <span class="text-[10px] font-bold text-orange-500">SOON</span>
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
            // Setup default styling for Chart.js
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8'; // slate-400 (brighter on dark)
            
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
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.08)',
                            borderWidth: 2,
                            pointBackgroundColor: '#0f172a',
                            pointBorderColor: '#3b82f6',
                            pointHoverBackgroundColor: '#3b82f6',
                            pointHoverBorderColor: '#0f172a',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
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
                                grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                                ticks: { callback: function(value) { return '$' + value; }, font: { size: 11 } }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { size: 10 }, maxTicksLimit: 10 }
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
                                    '#2563eb', '#0f172a', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#64748b'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle', font: { size: 11, weight: '600' } }
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) { return ' $' + context.parsed.toFixed(2); }
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
