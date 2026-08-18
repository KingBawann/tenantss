<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white leading-tight">
                {{ __('Inventory Report') }}
            </h2>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('reports.inventory') }}" class="flex items-center gap-2">
                    <label for="threshold" class="text-xs font-bold text-slate-400 uppercase">Low Stock Alert:</label>
                    <input type="number" name="threshold" id="threshold" value="{{ request('threshold', 10) }}" min="1" class="bg-slate-800 text-slate-100 border-white/10 rounded text-sm w-20 py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 px-3 py-1 rounded text-sm font-bold shadow-sm transition-colors">Apply</button>
                </form>
                <a href="{{ route('reports.inventory', ['export' => 'csv'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm gap-2">
                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- KPI Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 p-6 flex flex-col justify-between">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Stock Items</div>
                    <div class="text-3xl font-black text-white">{{ number_format($totalItems) }}</div>
                </div>
                <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 p-6 flex flex-col justify-between">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Cost Value</div>
                    <div class="text-3xl font-black text-white">${{ number_format($totalCostValue, 2) }}</div>
                </div>
                <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 p-6 flex flex-col justify-between">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Retail Value</div>
                    <div class="text-3xl font-black text-white">${{ number_format($totalRetailValue, 2) }}</div>
                </div>
                <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 p-6 flex flex-col justify-between">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Potential Profit</div>
                    <div class="text-3xl font-black text-emerald-400">${{ number_format($totalRetailValue - $totalCostValue, 2) }}</div>
                </div>
            </div>

            {{-- Alerts / Actionable Items --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                {{-- Low & Out of Stock --}}
                <div class="bg-slate-900 rounded-lg shadow-sm border border-amber-900/50 overflow-hidden flex flex-col">
                    <div class="bg-amber-900/20 px-6 py-4 border-b border-amber-900/50 flex justify-between items-center">
                        <h3 class="font-bold text-amber-400 flex items-center gap-2">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Stock Warnings
                        </h3>
                        <span class="text-xs font-bold text-amber-300 bg-amber-900/50 px-2 py-1 rounded-full border border-amber-800/50">{{ $outOfStockProducts->count() + $lowStockProducts->count() }} Items</span>
                    </div>
                    <div class="p-0 overflow-y-auto max-h-60">
                        @if($outOfStockProducts->isEmpty() && $lowStockProducts->isEmpty())
                            <div class="p-6 text-center text-sm text-slate-400 font-medium">All items are sufficiently stocked.</div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <tbody class="text-sm divide-y divide-white/5">
                                    @foreach($outOfStockProducts as $product)
                                        <tr class="hover:bg-slate-800">
                                            <td class="px-6 py-3 font-medium text-white">{{ $product->name }}</td>
                                            <td class="px-6 py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-900/50 text-red-400 border border-red-800/50">Out of Stock</span></td>
                                        </tr>
                                    @endforeach
                                    @foreach($lowStockProducts as $product)
                                        <tr class="hover:bg-slate-800">
                                            <td class="px-6 py-3 font-medium text-white">{{ $product->name }}</td>
                                            <td class="px-6 py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-900/50 text-amber-400 border border-amber-800/50">Only {{ $product->stock_quantity }} Left</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                {{-- Dead Stock --}}
                <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 overflow-hidden flex flex-col">
                    <div class="bg-slate-800/50 px-6 py-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Dead Stock <span class="text-xs font-normal text-slate-400 ml-1">(0 sales in 30 days)</span>
                        </h3>
                        <span class="text-xs font-bold text-slate-300 bg-slate-800 px-2 py-1 border border-white/10 rounded-full">{{ $deadStockProducts->count() }} Items</span>
                    </div>
                    <div class="p-0 overflow-y-auto max-h-60">
                        @if($deadStockProducts->isEmpty())
                            <div class="p-6 text-center text-sm text-slate-400 font-medium">No dead stock detected. All products are moving.</div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <tbody class="text-sm divide-y divide-white/5">
                                    @foreach($deadStockProducts as $product)
                                        <tr class="hover:bg-slate-800">
                                            <td class="px-6 py-3 font-medium text-white">
                                                {{ $product->name }}
                                                <div class="text-xs text-slate-400 font-mono mt-0.5">Value: ${{ number_format($product->stock_quantity * $product->cost_price, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-slate-300 border border-white/10">{{ $product->stock_quantity }} in stock</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Full Inventory Table --}}
            <div class="bg-slate-900 rounded-lg shadow-sm border border-white/5 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h3 class="font-bold text-white">Complete Inventory Valuation</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/5">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Category</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Stock Qty</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Unit Cost</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Total Cost</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Total Retail</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-white/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-900 divide-y divide-white/5">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-bold text-white">{{ $product->name }}</div>
                                                <div class="text-xs text-slate-400 font-mono">{{ $product->barcode ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-300">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold {{ $product->stock_quantity <= 0 ? 'text-red-400' : ($product->stock_quantity <= $lowStockThreshold ? 'text-amber-400' : 'text-white') }}">
                                        {{ $product->stock_quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-400">
                                        ${{ number_format($product->cost_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-slate-300">
                                        ${{ number_format($product->stock_quantity * $product->cost_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-emerald-400">
                                        ${{ number_format($product->stock_quantity * $product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($product->stock_quantity == 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-900/50 text-red-400 border border-red-800/50">Out of Stock</span>
                                        @elseif($product->stock_quantity <= $lowStockThreshold)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-900/50 text-amber-400 border border-amber-800/50">Low Stock</span>
                                        @elseif($product->sale_items_count == 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-slate-300 border border-white/10">Dead Stock</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-900/50 text-emerald-400 border border-emerald-800/50">Healthy</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm font-medium">No products found in the inventory.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
