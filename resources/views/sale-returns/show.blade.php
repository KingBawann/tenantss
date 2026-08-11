<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('sale-returns.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Return Details <span class="text-slate-400 text-lg font-normal ml-2">#{{ str_pad($saleReturn->id, 8, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Main Column: Returned Items --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Returned Items</h3>
                            <span class="text-sm text-slate-500 font-medium">{{ $saleReturn->items->count() }} item(s)</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Product</th>
                                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Unit Price</th>
                                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Returned Qty</th>
                                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Refund Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($saleReturn->items as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-900">{{ $item->product->name ?? 'Unknown Product' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                                            ${{ number_format($item->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50 border-t border-slate-200">
                                        <td colspan="3" class="px-6 py-4 text-right font-bold text-slate-900 uppercase tracking-wider text-sm">Total Refund</td>
                                        <td class="px-6 py-4 text-right font-black text-xl text-slate-900">${{ number_format($saleReturn->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Sidebar: Return Details --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Summary</h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date & Time</p>
                                <p class="text-sm font-medium text-slate-900">{{ $saleReturn->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Original Sale</p>
                                <a href="{{ route('sales.show', $saleReturn->sale_id) }}" class="text-sm font-bold text-blue-600 hover:underline">
                                    View Sale #{{ str_pad($saleReturn->sale_id, 8, '0', STR_PAD_LEFT) }}
                                </a>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Staff Member</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs border border-slate-300">
                                        {{ strtoupper(substr($saleReturn->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">{{ $saleReturn->user->name ?? 'System' }}</p>
                                </div>
                            </div>

                            @if($saleReturn->reason)
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Reason</p>
                                <div class="p-3 bg-slate-50 rounded border border-slate-100">
                                    <p class="text-sm font-medium text-slate-700 italic">"{{ $saleReturn->reason }}"</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
