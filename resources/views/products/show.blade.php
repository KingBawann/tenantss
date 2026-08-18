<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-white leading-tight">
                Product Details
            </h2>
        </div>
    </x-slot>

    <div x-data="{ showAdjustmentModal: false }" class="py-10 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- MAIN COLUMN: DETAILS --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- PRODUCT IDENTITY CARD --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden flex flex-col md:flex-row">
                        <div class="w-full md:w-1/3 bg-slate-800/50 flex items-center justify-center p-8 border-r border-white/5">
                            <svg class="w-24 h-24 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="p-6 md:p-8 flex-1 flex flex-col justify-center">
                            <span class="inline-block px-3 py-1 rounded bg-blue-900/50 text-blue-400 text-xs font-bold uppercase tracking-wider mb-3 w-fit border border-blue-800/50">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            <h1 class="text-3xl font-black text-white tracking-tight leading-none mb-2">{{ $product->name }}</h1>
                            <p class="text-sm font-mono text-slate-400 mb-6">BARCODE: {{ $product->barcode ?? 'N/A' }}</p>
                            
                            <div class="flex gap-4">
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 border border-white/10 rounded-lg text-sm font-bold text-slate-200 hover:bg-slate-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Edit Product
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- INVENTORY ACTIONS TABLE --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden">
                        <div class="px-6 py-5 border-b border-white/5 bg-slate-800/50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Stock Movement History</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-800/50 border-b border-white/5">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Date</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Action Type</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs text-right">Qty Change</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">User</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($inventoryActions as $action)
                                    <tr class="hover:bg-slate-800 transition-colors">
                                        <td class="px-6 py-4 text-slate-400 text-xs font-mono">
                                            {{ $action->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider border
                                                {{ $action->action_type == 'purchase' || $action->action_type == 'sale_return' || $action->action_type == 'manual_add' ? 'bg-emerald-900/50 text-emerald-400 border-emerald-800/50' : 'bg-amber-900/50 text-amber-400 border-amber-800/50' }}">
                                                {{ str_replace('_', ' ', $action->action_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black {{ $action->action_type == 'sale' || $action->action_type == 'loss' || $action->action_type == 'manual_subtract' ? 'text-red-400' : 'text-emerald-400' }}">
                                            {{ $action->action_type == 'sale' || $action->action_type == 'loss' || $action->action_type == 'manual_subtract' ? '-' : '+' }}{{ $action->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-300 font-medium">
                                            {{ $action->user->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-400 text-xs truncate max-w-[200px]" title="{{ $action->notes }}">
                                            {{ $action->notes ?: '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                            <p class="text-sm font-bold text-slate-400">No stock movements recorded yet.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($inventoryActions->hasPages())
                        <div class="px-6 py-4 border-t border-white/5 bg-slate-800/50">
                            {{ $inventoryActions->links() }}
                        </div>
                        @endif
                    </div>

                    {{-- MANUAL STOCK ADJUSTMENTS TABLE --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden">
                        <div class="px-6 py-5 border-b border-white/5 bg-slate-800/50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Manual Stock Adjustments</h3>
                            <button @click="showAdjustmentModal = true" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                New Adjustment
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-800/50 border-b border-white/5">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Date</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Reason</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs text-right">Qty Change</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">User</th>
                                        <th class="px-6 py-3 font-bold text-slate-400 uppercase tracking-wider text-xs">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($stockAdjustments ?? [] as $adjustment)
                                    <tr class="hover:bg-slate-800 transition-colors">
                                        <td class="px-6 py-4 text-slate-400 text-xs font-mono">
                                            {{ $adjustment->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider border bg-slate-800 border-white/10 text-slate-300">
                                                {{ ucfirst($adjustment->reason) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black {{ $adjustment->quantity_change > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-300 font-medium">
                                            {{ $adjustment->user->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-400 text-xs truncate max-w-[200px]" title="{{ $adjustment->note }}">
                                            {{ $adjustment->note ?: '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                            <p class="text-sm font-bold text-slate-400">No manual adjustments recorded yet.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(isset($stockAdjustments) && $stockAdjustments->hasPages())
                        <div class="px-6 py-4 border-t border-white/5 bg-slate-800/50">
                            {{ $stockAdjustments->links() }}
                        </div>
                        @endif
                    </div>

                </div>

                {{-- SIDEBAR: METRICS --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- CURRENT STOCK METRIC --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l {{ $product->stock_quantity <= 5 ? 'from-red-900/20 to-transparent' : 'from-emerald-900/20 to-transparent' }}"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Current Stock</p>
                            <h2 class="text-4xl font-black {{ $product->stock_quantity <= 5 ? 'text-red-500' : 'text-white' }}">{{ $product->stock_quantity }}</h2>
                            @if($product->stock_quantity <= 5)
                                <p class="text-xs font-bold text-red-500 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Low Stock Warning
                                </p>
                            @else
                                <p class="text-xs font-bold text-emerald-400 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    In Stock
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- PRICING CARD --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden">
                        <div class="px-6 py-5 border-b border-white/5 bg-slate-800/50">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Pricing details</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-end pb-4 border-b border-white/5">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Retail Price</p>
                                    <p class="text-2xl font-black text-white">${{ number_format($product->price, 2) }}</p>
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
                                <span class="text-sm font-bold text-slate-300">Profit Margin</span>
                                <span class="text-sm font-black {{ $margin > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    ${{ number_format($margin, 2) }} ({{ number_format($marginPercent, 1) }}%)
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- SALES VELOCITY CARD --}}
                    <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden">
                        <div class="px-6 py-5 border-b border-white/5 bg-slate-800/50">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Sales Velocity</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Last 7 Days</p>
                                        <p class="text-xl font-black text-white">{{ $soldThisWeek }} <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">units</span></p>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
                                        @php $weekWidth = min(100, $soldThisWeek * 5); @endphp
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $weekWidth }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Last 30 Days</p>
                                        <p class="text-xl font-black text-white">{{ $soldThisMonth }} <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">units</span></p>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-2">
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

        {{-- NEW ADJUSTMENT MODAL --}}
        <div x-show="showAdjustmentModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
            <div x-show="showAdjustmentModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" 
                 @click="showAdjustmentModal = false"></div>
                 
            <div x-show="showAdjustmentModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-slate-900 border border-white/10 rounded-xl shadow-2xl w-full max-w-lg p-6 mx-4">
                 
                 <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">New Stock Adjustment</h3>
                    <button @click="showAdjustmentModal = false" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                 </div>

                 <form action="{{ route('stock-adjustments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div>
                        <label for="quantity_change" class="block text-sm font-medium text-slate-300 mb-1">Quantity Change (Use - for reductions)</label>
                        <input type="number" name="quantity_change" id="quantity_change" required placeholder="e.g. 5 or -3" 
                               class="w-full bg-slate-800 border-white/10 text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-blue-500/20 rounded-lg shadow-sm">
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-slate-300 mb-1">Reason</label>
                        <select name="reason" id="reason" required class="w-full bg-slate-800 border-white/10 text-slate-100 focus:border-blue-500 focus:ring-blue-500/20 rounded-lg shadow-sm">
                            <option value="">Select a reason...</option>
                            <option value="received">Received</option>
                            <option value="correction">Correction</option>
                            <option value="damaged">Damaged</option>
                            <option value="theft">Theft</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium text-slate-300 mb-1">Notes (Optional)</label>
                        <textarea name="note" id="note" rows="3" class="w-full bg-slate-800 border-white/10 text-slate-100 focus:border-blue-500 focus:ring-blue-500/20 rounded-lg shadow-sm"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-white/5">
                        <button type="button" @click="showAdjustmentModal = false" class="px-4 py-2 bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 rounded-lg text-sm font-bold shadow-sm transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">
                            Save Adjustment
                        </button>
                    </div>
                 </form>
            </div>
        </div>
    </div>
</x-app-layout>