<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Sales</h2>
            <a href="{{ route('sales.create') }}" class="bg-[#103b2f] hover:bg-[#0c2b22] text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Sale
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f4f7f6] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Bar (Impeccable) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Sales History
                    </h3>
                    @if(request()->anyFilled(['search', 'status', 'date_start', 'date_end']))
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                            Active
                        </span>
                    @endif
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap lg:flex-nowrap items-end gap-4">
                        
                        {{-- Search --}}
                        <div class="flex-1 min-w-[240px]">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Sale ID or Customer Name..." class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md text-sm bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-inner">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="w-full sm:w-40">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="status" class="block w-full border border-slate-300 rounded-md text-sm font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 pl-3 pr-10 shadow-inner transition-colors">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        {{-- Dates --}}
                        <div class="w-full sm:w-40">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">From Date</label>
                            <input type="date" name="date_start" value="{{ request('date_start') }}" class="block w-full border border-slate-300 rounded-md text-sm bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 px-3 shadow-inner transition-colors text-slate-700">
                        </div>
                        <div class="w-full sm:w-40">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">To Date</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}" class="block w-full border border-slate-300 rounded-md text-sm bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 px-3 shadow-inner transition-colors text-slate-700">
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 w-full lg:w-auto mt-2 lg:mt-0">
                            <button type="submit" class="flex-1 lg:flex-none bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-5 py-2 rounded-md text-sm font-bold shadow-sm transition-all flex items-center justify-center h-[38px] gap-2">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Apply
                            </button>
                            <a href="{{ route('sales.index') }}" class="flex-1 lg:flex-none bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 px-4 py-2 rounded-md text-sm font-bold transition-colors flex items-center justify-center h-[38px]">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-0 border border-gray-100">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Customer</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Items</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Total</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-sm text-gray-600">{{ $sale->id }}</td>
                                <td class="p-4 text-sm font-semibold text-gray-900">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td class="p-4 text-sm text-gray-600">{{ $sale->items->count() }} item(s)</td>
                                <td class="p-4 text-sm font-bold text-gray-900">${{ number_format($sale->total, 2) }}</td>
                                <td class="p-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                        {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $sale->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $sale->payment_status === 'pending' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ $sale->payment_status }}</span>
                                </td>
                                <td class="p-4 text-sm text-gray-500">{{ $sale->created_at->format('M d, Y') }}</td>
                                <td class="p-4 text-sm text-right">
                                    <div class="flex justify-end gap-3 items-center">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="View Sale">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        @if(Auth::user() && Auth::user()->hasAdminAccess())
                                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Delete this sale and restore stock?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 transition-colors pt-1" title="Delete Sale">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-500">No sales recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Pagination Links -->
            @if($sales->hasPages())
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            @endif

            <!-- Bottom Create Button -->
            <div class="mt-8 flex justify-end">
                <a href="{{ route('sales.create') }}" class="bg-[#103b2f] hover:bg-[#0c2b22] text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Sale
                </a>
            </div>

        </div>
    </div>
</x-app-layout>