<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Sale Details <span class="text-slate-500 text-lg font-normal ml-2">#{{ str_pad($sale->id, 8, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sale-returns.create', ['sale_id' => $sale->id]) }}">
                    <x-secondary-button class="flex items-center gap-2 border-amber-200 text-amber-700 hover:bg-amber-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Refund / Return
                    </x-secondary-button>
                </a>
                <a href="{{ route('sales.receipt', $sale) }}" target="_blank">
                    <x-secondary-button class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Receipt
                    </x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Main Column: Line Items --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-white flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Line Items</h3>
                            <span class="text-sm text-slate-500 font-medium">{{ $sale->items->count() }} item(s)</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm border-collapse">
                                <thead class="bg-slate-50/80 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs">Product</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Unit Price</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Qty</th>
                                        <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($sale->items as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-900">{{ $item->product->name ?? 'Deleted Product' }}</p>
                                            <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $item->product->barcode ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right text-slate-600 font-medium">${{ number_format($item->price, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-900">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50/80 border-t border-slate-200">
                                        <td colspan="3" class="px-6 py-4 text-right font-bold text-slate-500 uppercase tracking-wider text-sm">Subtotal</td>
                                        <td class="px-6 py-4 text-right font-bold text-slate-500">${{ number_format($sale->total, 2) }}</td>
                                    </tr>
                                    @if($sale->discount_type !== 'none' && $sale->discount_value > 0)
                                    <tr class="bg-slate-50/80 border-t border-slate-100">
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-amber-600 uppercase tracking-wider text-sm">Discount @if($sale->discount_type === 'percentage')({{ floatval($sale->discount_value) }}%)@endif</td>
                                        <td class="px-6 py-3 text-right font-bold text-amber-600">-${{ number_format($sale->total - $sale->discountedTotal, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="bg-slate-50/80 border-t border-slate-200">
                                        <td colspan="3" class="px-6 py-4 text-right font-bold text-slate-900 uppercase tracking-wider text-sm">Grand Total</td>
                                        <td class="px-6 py-4 text-right font-black text-xl text-slate-900">${{ number_format($sale->discountedTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Sidebar: Sale Details --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Summary Card --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Sale Summary</h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Date & Time</p>
                                <p class="text-sm font-medium text-slate-900">{{ $sale->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Payment Status</p>
                                <div class="flex gap-2">
                                    @if($sale->payment_status === 'paid')
                                        <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">Paid</span>
                                    @elseif($sale->payment_status === 'partial')
                                        <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">Partial</span>
                                    @else
                                        <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">Pending</span>
                                    @endif
                                    
                                    <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $sale->payment_method === 'cash' ? 'Cash' : 'Card' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cashier</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs border border-slate-300">
                                        {{ strtoupper(substr($sale->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">{{ $sale->user->name ?? 'System' }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Branch</p>
                                <p class="text-sm font-medium text-slate-900">{{ $sale->branch->name ?? 'Main Branch' }}</p>
                            </div>

                            @if($sale->payment_method === 'cash' && $sale->amount_tendered !== null)
                            <div class="pt-4 border-t border-slate-200 mt-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tendered</span>
                                    <span class="text-sm font-bold text-slate-900">${{ number_format($sale->amount_tendered, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Change</span>
                                    <span class="text-sm font-bold text-slate-900">${{ number_format(max(0, $sale->amount_tendered - $sale->discountedTotal), 2) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Customer Card --}}
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Customer</h3>
                        </div>
                        <div class="p-6">
                            @if($sale->customer)
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm border border-blue-200">
                                            {{ strtoupper(substr($sale->customer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-slate-900">{{ $sale->customer->name }}</p>
                                            <p class="text-xs font-medium text-slate-500">{{ $sale->customer->phone ?? 'No phone' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 text-slate-500">
                                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <p class="text-sm font-medium">Walk-in Customer</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>