<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.show', $sale) }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                Process Return <span class="text-slate-400 text-lg font-normal ml-2">Sale #{{ str_pad($sale->id, 8, '0', STR_PAD_LEFT) }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error') || $errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') ?? $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('sale-returns.store') }}" method="POST">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Select Items to Return</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Product</th>
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Price</th>
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Purchased</th>
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Previously Returned</th>
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-center">Return Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($sale->items as $index => $item)
                                @php
                                    $previouslyReturned = \App\Models\SaleReturnItem::where('sale_item_id', $item->id)->sum('quantity');
                                    $maxReturnable = $item->quantity - $previouslyReturned;
                                @endphp
                                <tr class="{{ $maxReturnable == 0 ? 'bg-slate-50 opacity-60' : 'hover:bg-slate-50/50 transition-colors' }}">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">{{ $item->product->name ?? 'Unknown' }}</p>
                                        <input type="hidden" name="items[{{ $index }}][sale_item_id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-600">
                                        ${{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-900">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-500">
                                        {{ $previouslyReturned }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($maxReturnable > 0)
                                            <input type="number" name="items[{{ $index }}][return_quantity]" value="0" min="0" max="{{ $maxReturnable }}" class="w-20 text-center border-slate-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold text-slate-900">
                                        @else
                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fully Returned</span>
                                            <input type="hidden" name="items[{{ $index }}][return_quantity]" value="0">
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="p-6">
                        <label for="reason" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">Return Reason</label>
                        <textarea id="reason" name="reason" rows="3" class="block w-full border-slate-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" placeholder="Optional notes about why the items are being returned..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('sales.show', $sale) }}" class="px-6 py-3 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                        <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                        Process Return
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
