{{-- Close Shift Form --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.create') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-xl text-white leading-tight">Close Shift</h2>
        </div>
    </x-slot>

    @php
        $activeShift = \App\Models\Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->with('sales')
            ->latest('opened_at')
            ->first();
    @endphp

    <div class="py-10 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(!$activeShift)
                <div class="bg-amber-950 border border-amber-800/50 text-amber-300 px-4 py-4 rounded-xl text-sm font-medium text-center">
                    You don't have an open shift to close.
                    <a href="{{ route('sales.create') }}" class="block mt-2 text-amber-400 font-bold hover:text-amber-300">← Back to POS</a>
                </div>
            @else

            {{-- Shift Summary --}}
            <div class="bg-slate-900 rounded-xl border border-white/5 p-6">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Shift Summary</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-slate-800 rounded-lg p-4 text-center">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Opened At</p>
                        <p class="text-sm font-black text-white">{{ $activeShift->opened_at->format('H:i') }}</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-4 text-center">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Duration</p>
                        <p class="text-sm font-black text-white">{{ $activeShift->opened_at->diffForHumans(null, true) }}</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-4 text-center">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Sales</p>
                        <p class="text-sm font-black text-emerald-400">${{ number_format($activeShift->total_sales, 2) }}</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-4 text-center">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Transactions</p>
                        <p class="text-sm font-black text-white">{{ $activeShift->sales_count }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-slate-800/50 rounded-lg p-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cash Sales</span>
                        <span class="text-sm font-black text-white">${{ number_format($activeShift->cash_sales, 2) }}</span>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Card Sales</span>
                        <span class="text-sm font-black text-white">${{ number_format($activeShift->card_sales, 2) }}</span>
                    </div>
                </div>

                @php
                    $expectedCash = (float) $activeShift->opening_float + $activeShift->cash_sales;
                @endphp
                <div class="mt-4 bg-blue-950/50 border border-blue-800/30 rounded-lg p-4 flex justify-between items-center">
                    <span class="text-xs font-bold text-blue-300 uppercase tracking-wider">Expected Cash in Drawer</span>
                    <span class="text-lg font-black text-blue-300">${{ number_format($expectedCash, 2) }}</span>
                </div>
            </div>

            {{-- Close Form --}}
            <div class="bg-slate-900 rounded-xl border border-white/5 p-6">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Count Your Drawer</h3>

                <form action="{{ route('shift.close.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Actual Cash Count</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-lg">$</span>
                            <input type="number" name="closing_float"
                                   step="0.01" min="0" required
                                   x-data x-model.number="closing"
                                   @input="variance = closing - {{ $expectedCash }}"
                                   class="w-full pl-8 pr-4 py-3 bg-slate-800 border border-white/10 text-white text-xl font-black rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-right"
                                   placeholder="{{ number_format($expectedCash, 2) }}"
                                   autofocus>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Notes (optional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full bg-slate-800 border border-white/10 text-slate-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none placeholder-slate-600"
                                  placeholder="Any notes about this shift..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-500 text-white font-black py-3.5 px-4 rounded-xl text-sm uppercase tracking-widest transition-all shadow-lg shadow-amber-900/20 active:scale-95">
                        Close Shift & Generate Z-Report
                    </button>
                </form>
            </div>

            @endif
        </div>
    </div>
</x-app-layout>
