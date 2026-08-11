<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                Product Details
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- MAIN COLUMN: DETAILS --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- PRODUCT IDENTITY CARD --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col md:flex-row">
                        <div class="w-full md:w-1/3 bg-slate-100 flex items-center justify-center p-8 border-r border-slate-200">
                            <svg class="w-24 h-24 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="p-6 md:p-8 flex-1 flex flex-col justify-center">
                            <span class="inline-block px-3 py-1 rounded bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-3 w-fit border border-blue-100">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2">{{ $product->name }}</h1>
                            <p class="text-sm font-mono text-slate-500 mb-6">BARCODE: {{ $product->barcode ?? 'N/A' }}</p>
                            
                            <div class="flex gap-4">
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-slate-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Edit Product
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- INVENTORY ACTIONS TABLE --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Stock Movement History</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-white border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs">Date</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs">Action Type</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Qty Change</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs">User</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($inventoryActions as $action)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-slate-500 text-xs font-mono">
                                            {{ $action->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider
                                                {{ $action->action_type == 'purchase' || $action->action_type == 'sale_return' || $action->action_type == 'manual_add' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200' }} border">
                                                {{ str_replace('_', ' ', $action->action_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black {{ $action->action_type == 'sale' || $action->action_type == 'loss' || $action->action_type == 'manual_subtract' ? 'text-amber-600' : 'text-emerald-600' }}">
                                            {{ $action->action_type == 'sale' || $action->action_type == 'loss' || $action->action_type == 'manual_subtract' ? '-' : '+' }}{{ $action->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-900 font-medium">
                                            {{ $action->user->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 text-xs truncate max-w-[200px]" title="{{ $action->notes }}">
                                            {{ $action->notes ?: '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                            <p class="text-sm font-bold text-slate-500">No stock movements recorded yet.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($inventoryActions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                            {{ $inventoryActions->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- SIDEBAR: METRICS --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- CURRENT STOCK METRIC --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l {{ $product->stock_quantity <= 5 ? 'from-red-50 to-transparent' : 'from-emerald-50 to-transparent' }}"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Current Stock</p>
                            <h2 class="text-4xl font-black {{ $product->stock_quantity <= 5 ? 'text-red-600' : 'text-slate-900' }}">{{ $product->stock_quantity }}</h2>
                            @if($product->stock_quantity <= 5)
                                <p class="text-xs font-bold text-red-500 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Low Stock Warning
                                </p>
                            @else
                                <p class="text-xs font-bold text-emerald-500 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    In Stock
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- PRICING CARD --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pricing details</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-end pb-4 border-b border-slate-100">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Retail Price</p>
                                    <p class="text-2xl font-black text-slate-900">${{ number_format($product->price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Cost Price</p>
                                    <p class="text-lg font-bold text-slate-500">${{ number_format($product->cost_price, 2) }}</p>
                                </div>
                            </div>
                            
                            @php
                                $margin = $product->price - $product->cost_price;
                                $marginPercent = $product->price > 0 ? ($margin / $product->price) * 100 : 0;
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">Profit Margin</span>
                                <span class="text-sm font-black {{ $margin > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                    ${{ number_format($margin, 2) }} ({{ number_format($marginPercent, 1) }}%)
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- SALES VELOCITY CARD --}}
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Sales Velocity</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Last 7 Days</p>
                                        <p class="text-xl font-black text-slate-900">{{ $soldThisWeek }} <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">units</span></p>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        @php $weekWidth = min(100, $soldThisWeek * 5); @endphp
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $weekWidth }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Last 30 Days</p>
                                        <p class="text-xl font-black text-slate-900">{{ $soldThisMonth }} <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">units</span></p>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        @php $monthWidth = min(100, $soldThisMonth * 2); @endphp
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $monthWidth }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>