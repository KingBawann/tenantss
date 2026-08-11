<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Staff Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('users.edit', $user) }}">
                    <x-secondary-button>Edit Account</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Left Column: User Card --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 flex flex-col items-center border-b border-slate-100 bg-slate-50/50">
                            <div class="h-20 w-20 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-2xl border border-slate-300 mb-4 shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="text-sm text-slate-500 mb-4">{{ $user->email }}</p>
                            
                            <div class="flex gap-2">
                                @if($user->role === 'admin' || $user->role === 'owner')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                @elseif($user->role === 'manager')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Manager
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        Cashier
                                    </span>
                                @endif
                                
                                @if($user->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                        Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 flex items-center gap-1.5">
                                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Branch Assignment</p>
                                    <p class="text-sm font-medium text-slate-900">{{ $user->branch->name ?? 'None (Main Branch)' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Sales Processed</p>
                                    <p class="text-sm font-medium text-slate-900">{{ number_format($user->sales_count ?? 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Account Created</p>
                                    <p class="text-sm font-medium text-slate-900">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Login Activity --}}
                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-full flex flex-col">
                        <div class="px-6 py-5 border-b border-slate-200 bg-white">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Recent Login Activity</h3>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto">
                            @if($user->loginLogs && $user->loginLogs->count() > 0)
                                <table class="w-full text-left text-sm border-collapse">
                                    <thead class="bg-slate-50/80 border-b border-slate-200">
                                        <tr>
                                            <th class="px-6 py-3 font-semibold text-slate-500">Date & Time</th>
                                            <th class="px-6 py-3 font-semibold text-slate-500">IP Address</th>
                                            <th class="px-6 py-3 font-semibold text-slate-500">Device / Browser</th>
                                            <th class="px-6 py-3 font-semibold text-slate-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($user->loginLogs as $log)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-medium">
                                                {{ $log->logged_in_at->format('M d, Y - H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                                {{ $log->ip_address }}
                                            </td>
                                            <td class="px-6 py-4 text-slate-500 text-xs truncate max-w-[200px]" title="{{ $log->user_agent }}">
                                                {{ Str::limit($log->user_agent, 40) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->status === 'success')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Success
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">
                                                        Failed
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                    <p class="text-sm font-semibold text-slate-600">No login records found</p>
                                    <p class="text-xs mt-1 text-slate-400">This user hasn't logged in recently.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
