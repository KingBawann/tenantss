{{-- Open Shift Modal / Page --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Open Register</h2>
    </x-slot>

    <div class="py-10 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md mx-auto px-4">

            @if(session('error'))
                <div class="mb-4 bg-red-950 border border-red-800/50 text-red-300 px-4 py-3 rounded-xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-2xl">
                {{-- Header --}}
                <div class="bg-blue-600 px-6 py-5 text-center">
                    <div class="w-14 h-14 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-black text-xl">Start Your Shift</h3>
                    <p class="text-blue-200 text-sm mt-1">{{ now()->format('l, F j, Y — g:i A') }}</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('shift.open.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Opening Float (Cash in Drawer)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-lg">$</span>
                            <input type="number" name="opening_float" value="{{ old('opening_float', '200.00') }}"
                                   step="0.01" min="0" required
                                   class="w-full pl-8 pr-4 py-3 bg-slate-800 border border-white/10 text-white text-xl font-black rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-right"
                                   autofocus>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Count your drawer before starting. This amount is recorded for your Z-Report.</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3.5 px-4 rounded-xl text-sm uppercase tracking-widest transition-all shadow-lg shadow-blue-900/30 active:scale-95">
                        Open Shift & Start Selling
                    </button>

                    <a href="{{ route('sales.create') }}" class="block text-center text-xs text-slate-600 hover:text-slate-400 transition-colors mt-2">
                        Skip (no shift tracking)
                    </a>
                </form>
            </div>

            <p class="text-center text-xs text-slate-600 mt-4">
                Cashier: <span class="text-slate-400 font-bold">{{ Auth::user()->name }}</span>
            </p>
        </div>
    </div>
</x-app-layout>
