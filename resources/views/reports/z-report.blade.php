<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-white leading-tight">End of Day Summary (Z-Report)</h2>
            <div class="text-sm text-slate-400 font-medium bg-slate-900 px-4 py-2 rounded-lg shadow-sm border border-white/5">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Overview Cards (if admin, show grand total) --}}
            @if(Auth::user()->hasAdminAccess())
                @php
                    $grandTotalCash = collect($reports)->sum('cash');
                    $grandTotalCard = collect($reports)->sum('card');
                    $grandTotal = collect($reports)->sum('total');
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-white/5 flex flex-col items-start hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Total Cash
                        </p>
                        <p class="text-4xl font-black text-white mt-2">${{ number_format($grandTotalCash, 2) }}</p>
                    </div>
                    <div class="bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-white/5 flex flex-col items-start hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Total Card
                        </p>
                        <p class="text-4xl font-black text-white mt-2">${{ number_format($grandTotalCard, 2) }}</p>
                    </div>
                    <div class="bg-slate-900 overflow-hidden shadow-lg sm:rounded-xl p-6 border border-white/5 flex flex-col items-start">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Store Grand Total</p>
                        <p class="text-4xl font-black text-white mt-2">${{ number_format($grandTotal, 2) }}</p>
                    </div>
                </div>
            @endif

            {{-- Main Data Table --}}
            <div class="bg-slate-900 shadow-sm sm:rounded-2xl border border-white/5 overflow-hidden">
                <div class="px-6 py-5 border-b border-white/5 bg-slate-900">
                    <h3 class="text-lg font-bold text-white">Shift Breakdown by Cashier</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/5">
                        <thead class="bg-slate-800/50 border-b border-white/5">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-wider w-1/3">Cashier Name</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-black text-slate-400 uppercase tracking-wider">Cash Register</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-black text-slate-400 uppercase tracking-wider">Card Terminal</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-black text-white uppercase tracking-wider">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-900 divide-y divide-white/5">
                            @forelse($reports as $userId => $data)
                                <tr class="hover:bg-slate-800 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-slate-800 border border-white/5 rounded-full flex items-center justify-center text-slate-300 font-bold uppercase">
                                                {{ substr($data['user']->name ?? '?', 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-white">{{ $data['user']->name ?? 'Unknown' }}</div>
                                                <div class="text-xs text-slate-400">{{ $data['user']->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="text-lg font-semibold text-emerald-400">${{ number_format($data['cash'], 2) }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="text-lg font-semibold text-blue-400">${{ number_format($data['card'], 2) }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="text-xl font-black text-white">${{ number_format($data['total'], 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-sm font-semibold text-slate-500">No sales recorded today.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="window.print()" class="bg-slate-800 border border-white/10 text-slate-200 px-6 py-2.5 rounded-lg shadow-sm hover:bg-slate-700 hover:shadow font-semibold transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Z-Report
                </button>
            </div>

        </div>
    </div>
</x-app-layout>
