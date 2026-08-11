<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Sale Returns') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Return ID</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Sale ID</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Date</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Staff</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Reason</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Total Refunded</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($returns as $return)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    #{{ str_pad($return->id, 8, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    <a href="{{ route('sales.show', $return->sale_id) }}" class="text-blue-600 hover:underline">
                                        #{{ str_pad($return->sale_id, 8, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $return->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-900">
                                    {{ $return->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 truncate max-w-[200px]" title="{{ $return->reason }}">
                                    {{ $return->reason ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-slate-900">
                                    ${{ number_format($return->total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('sale-returns.show', $return) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                                    <p class="text-sm font-bold text-slate-500">No returns found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($returns->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $returns->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
