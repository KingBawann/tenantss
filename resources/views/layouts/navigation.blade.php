<aside 
    class="border-r border-white/5 text-white w-64 flex-shrink-0 fixed inset-y-0 left-0 z-50 transition-all duration-300 md:relative md:translate-x-0 bg-slate-900"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full md:-ml-64': !sidebarOpen}"
    @click.away="if(window.innerWidth < 768) sidebarOpen = false"
>
    <div class="h-full flex flex-col">
        <!-- Sidebar Header (Logo) -->
        <div class="h-16 flex items-center px-6 border-b border-white/5">
            <a href="{{ Auth::user() && Auth::user()->hasAdminAccess() ? route('dashboard') : route('sales.create') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 rounded bg-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="font-bold text-xl tracking-tight">POS</span>
            </a>
            <!-- Close button (Visible on both mobile & desktop) -->
            <button @click="sidebarOpen = false" class="ml-auto text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Sidebar User Profile (Moved to Top) -->
        <div class="p-4 border-b border-white/5 bg-slate-800/50">
            <x-dropdown align="left" width="48">
                <x-slot name="trigger">
                    <button class="w-full flex items-center justify-between px-2 py-1 border border-transparent text-sm leading-4 font-semibold rounded-md text-slate-300 hover:text-white focus:outline-none transition ease-in-out duration-150">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                                {{ strtoupper(substr(Auth::user()?->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-white leading-tight">{{ Auth::user()?->name ?? 'Guest' }}</div>
                                <div class="text-xs text-slate-400">{{ Auth::user()?->role ?? 'User' }}</div>
                            </div>
                        </div>
                        <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <!-- Sidebar Links -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1" id="sidebar-scroll" @scroll.debounce.100ms="localStorage.setItem('sidebarScroll', $event.target.scrollTop)">
            
            @if(!(Auth::user()->tenant->is_master ?? false))
            <a href="{{ route('sales.create') }}" class="active-scale flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-lg shadow-sm transition-all duration-150 mb-4">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                {{ __('Point of Sale') }}
            </a>
            @endif

            @if(Auth::user() && Auth::user()->hasAdminAccess())
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Overview</p>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    {{ __('Dashboard') }}
                </x-nav-link>
            @endif

            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Transactions</p>
            
            <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*') && !request()->routeIs('sales.create')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                {{ __('Sales History') }}
            </x-nav-link>

            <x-nav-link :href="route('sale-returns.index')" :active="request()->routeIs('sale-returns.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sale-returns.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('sale-returns.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                {{ __('Returns') }}
            </x-nav-link>

            {{-- Shift Management (all roles) --}}
            @php $hasOpenShift = \App\Models\Shift::where('user_id', Auth::id())->where('status', 'open')->exists(); @endphp
            @if($hasOpenShift)
                <a href="{{ route('shift.close') }}" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-amber-300 hover:bg-amber-900/20 hover:text-amber-200 transition-colors border border-amber-800/30 mt-2">
                    <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Close Shift
                    <span class="ml-auto text-[9px] font-black uppercase tracking-wider bg-amber-600 text-white px-1.5 py-0.5 rounded">OPEN</span>
                </a>
            @else
                <a href="{{ route('shift.open') }}" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors mt-2">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Open Shift
                </a>
            @endif

            <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Reports</p>
            
            <x-nav-link :href="route('reports.z-report')" :active="request()->routeIs('reports.z-report')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.z-report') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.z-report') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                {{ __('Shift Report') }}
            </x-nav-link>

            @if(Auth::user() && Auth::user()->hasAdminAccess())
                <x-nav-link :href="route('reports.profit-loss')" :active="request()->routeIs('reports.profit-loss')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.profit-loss') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.profit-loss') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('P&L Report') }}
                </x-nav-link>
                
                <x-nav-link :href="route('reports.inventory')" :active="request()->routeIs('reports.inventory')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.inventory') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.inventory') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    {{ __('Inventory Report') }}
                </x-nav-link>
            
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Management</p>
                <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('categories.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    {{ __('Categories') }}
                </x-nav-link>
                <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('products.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    {{ __('Products') }}
                </x-nav-link>
                <a href="{{ route('products.import') }}" class="w-full flex items-center pl-11 pr-3 py-1.5 text-xs font-medium rounded-md text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    CSV Import
                </a>
                <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('customers.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Customers') }}
                </x-nav-link>
                <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('suppliers.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ __('Suppliers') }}
                </x-nav-link>
                <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('purchases.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    {{ __('Purchases') }}
                </x-nav-link>
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Staff') }}
                </x-nav-link>
            @endif
        </div>

        <!-- Sidebar Footer (Theme Toggle) -->
        <div class="p-4 border-t border-white/5 bg-slate-800/50 mt-auto">
            <button
                x-data="{ dark: document.documentElement.classList.contains('dark') }"
                @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('contentTheme', dark ? 'dark' : 'light')"
                class="w-full flex items-center justify-center px-4 py-2 border border-slate-700 rounded-md shadow-sm text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white focus:outline-none transition-colors"
            >
                <template x-if="!dark">
                    <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </template>
                <template x-if="dark">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </template>
                <span x-text="dark ? 'Light Mode' : 'Dark Mode'"></span>
            </button>
        </div>
        
        <script>
            (function() {
                var scrollContainer = document.getElementById('sidebar-scroll');
                var scrollPos = localStorage.getItem('sidebarScroll');
                if (scrollContainer && scrollPos) {
                    // Use a tiny timeout to ensure Alpine/styles have applied the height
                    setTimeout(function() {
                        scrollContainer.scrollTop = scrollPos;
                    }, 0);
                }
            })();
        </script>
    </div>
</aside>
