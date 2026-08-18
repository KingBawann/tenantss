<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      x-init="
        let theme = localStorage.getItem('contentTheme') || 'light';
        if (theme === 'dark') document.documentElement.classList.add('dark');
      "
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'POS System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-hidden">
        <div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="min-h-screen flex h-screen bg-slate-950">
            
            <!-- Sidebar (always dark) -->
            @include('layouts.navigation')

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative border-l border-white/5 bg-slate-50">
                
                <!-- Mobile Header & Hamburger -->
                <div class="md:hidden bg-white border-b border-slate-200 flex items-center justify-between p-4 flex-shrink-0 z-20 shadow-sm">
                    <a href="{{ Auth::user() && Auth::user()->hasAdminAccess() ? route('dashboard') : route('sales.create') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded bg-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-bold text-lg tracking-tight text-slate-900">POS</span>
                    </a>
                    <button @click="sidebarOpen = true" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white backdrop-blur-md border-b border-slate-200 flex-shrink-0 z-10 hidden md:flex items-center shadow-sm">
                        <button @click="sidebarOpen = true" x-show="!sidebarOpen" style="display: none;" class="ml-6 text-slate-400 hover:text-slate-900 focus:outline-none transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="py-5 px-6 lg:px-8 flex-1">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto custom-scrollbar flex flex-col">
                    <!-- Mobile Heading -->
                    @isset($header)
                    <div class="md:hidden bg-white border-b border-slate-200 px-4 py-4 mb-4 shadow-sm">
                        {{ $header }}
                    </div>
                    @endisset

                    <div class="flex-1 flex flex-col {{ request()->routeIs('sales.create') ? '' : 'p-6 lg:p-8' }}">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        
        <style>
            /* Easing & Animation */
            :root {
                --ease-out-quart: cubic-bezier(0.25, 1, 0.5, 1);
                --ease-out-quint: cubic-bezier(0.22, 1, 0.36, 1);
                --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes fade-up {
                0% { opacity: 0; transform: translateY(12px) scale(0.98); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }
            .animate-fade-up { animation: fade-up 0.5s var(--ease-out-quint) backwards; }
            @keyframes slide-in-right {
                0% { opacity: 0; transform: translateX(10px); }
                100% { opacity: 1; transform: translateX(0); }
            }
            .animate-slide-in-right { animation: slide-in-right 0.3s var(--ease-out-quart) backwards; }
            .active-scale:active {
                transform: scale(0.97);
                transition: transform 100ms var(--ease-out-quint) !important;
            }

            /* Sidebar is always dark — override theme variables inside it */
            aside {
                --color-surface: 2 6 23;
                --color-surface-alt: 15 23 42;
                --color-border: 255 255 255;
                color: #e2e8f0;
            }
        </style>
    </body>
</html>
