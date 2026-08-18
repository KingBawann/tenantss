<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-2xl text-white leading-tight">
                {{ __('Profit & Loss Report') }}
            </h2>
            
            <form method="GET" action="{{ route('reports.profit-loss') }}" class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="start_date" class="text-xs font-bold text-slate-400 uppercase">From</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="text-sm bg-slate-800 text-slate-100 border-white/10 rounded-md shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-2">
                    <label for="end_date" class="text-xs font-bold text-slate-400 uppercase">To</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="text-sm bg-slate-800 text-slate-100 border-white/10 rounded-md shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-bold text-white hover:bg-blue-500 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Filter
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-950 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- NET PROFIT BANNER --}}
            <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 p-8 flex items-center justify-between relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l {{ $grossProfit > 0 ? 'from-emerald-900/50' : 'from-red-900/50' }} to-transparent"></div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Net Gross Profit</p>
                    <div class="flex items-baseline gap-4">
                        <h2 class="text-5xl font-black {{ $grossProfit > 0 ? 'text-emerald-400' : ($grossProfit < 0 ? 'text-red-400' : 'text-white') }}">
                            {{ $grossProfit < 0 ? '-' : '' }}${{ number_format(abs($grossProfit), 2) }}
                        </h2>
                        <span class="px-3 py-1 rounded bg-slate-800 text-slate-300 text-sm font-bold border border-white/5">
                            {{ number_format($marginPercent, 1) }}% Margin
                        </span>
                    </div>
                </div>
            </div>

            {{-- P&L STATEMENT --}}
            <div class="bg-slate-900 rounded-xl shadow-sm border border-white/5 overflow-hidden">
                <div class="px-6 py-5 border-b border-white/5 bg-slate-900">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Statement Details</h3>
                </div>
                
                <div class="p-0">
                    <table class="min-w-full text-left text-sm">
                        <tbody class="divide-y divide-white/5">
                            {{-- REVENUE SECTION --}}
                            <tr class="bg-slate-800/50">
                                <td colspan="2" class="px-6 py-3 font-bold text-white uppercase tracking-wider text-xs border-b border-white/5">Revenue</td>
                            </tr>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 pl-10 font-medium text-slate-300">Gross Sales</td>
                                <td class="px-6 py-4 text-right font-bold text-white">${{ number_format($grossSales, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 pl-10 font-medium text-slate-300">Less: Refunds & Returns</td>
                                <td class="px-6 py-4 text-right font-bold text-red-400">-${{ number_format($totalRefunds, 2) }}</td>
                            </tr>
                            <tr class="bg-slate-800/30 border-t-2 border-white/10">
                                <td class="px-6 py-4 pl-10 font-black text-white">Net Sales</td>
                                <td class="px-6 py-4 text-right font-black text-white">${{ number_format($netSales, 2) }}</td>
                            </tr>

                            {{-- COGS SECTION --}}
                            <tr class="bg-slate-800/50">
                                <td colspan="2" class="px-6 py-3 font-bold text-white uppercase tracking-wider text-xs border-b border-white/5">Cost of Goods Sold (COGS)</td>
                            </tr>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 pl-10 font-medium text-slate-300">Cost of Goods Sold</td>
                                <td class="px-6 py-4 text-right font-bold text-white">${{ number_format($cogsSales, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 pl-10 font-medium text-slate-300">Less: Cost of Goods Returned</td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-400">-${{ number_format($cogsReturns, 2) }}</td>
                            </tr>
                            <tr class="bg-slate-800/30 border-t-2 border-white/10">
                                <td class="px-6 py-4 pl-10 font-black text-white">Net COGS</td>
                                <td class="px-6 py-4 text-right font-black text-white">${{ number_format($netCogs, 2) }}</td>
                            </tr>

                            {{-- BOTTOM LINE --}}
                            <tr class="bg-slate-900 border-t border-white/10">
                                <td class="px-6 py-5 pl-10 font-black text-white text-lg uppercase tracking-wider">Gross Profit</td>
                                <td class="px-6 py-5 text-right font-black text-white text-xl">
                                    {{ $grossProfit < 0 ? '-' : '' }}${{ number_format(abs($grossProfit), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-right">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 border border-white/10 rounded-md text-sm font-bold text-slate-200 hover:bg-slate-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Report
                </button>
            </div>

        </div>
    </div>
    
    <style>
        @media print {
            body { background-color: white !important; }
            nav, header, button { display: none !important; }
            .py-10 { padding-top: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            .bg-slate-900, .bg-slate-800, .bg-slate-950 { background-color: #f8fafc !important; color: #0f172a !important; }
            .text-white, .text-slate-300, .text-slate-400 { color: #0f172a !important; }
            .border-white\/5, .border-white\/10 { border-color: #e2e8f0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</x-app-layout>
