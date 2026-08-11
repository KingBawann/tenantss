<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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
    <body class="font-sans antialiased text-slate-100 bg-slate-950 selection:bg-blue-500/30 selection:text-blue-200 overflow-hidden">
        <div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="min-h-screen bg-slate-950 flex h-screen">
            
            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative border-l border-white/5 bg-slate-950">
                
                <!-- Mobile Header & Hamburger -->
                <div class="md:hidden bg-slate-900 border-b border-white/5 text-white flex items-center justify-between p-4 flex-shrink-0 z-20 shadow-sm">
                    <a href="{{ Auth::user() && Auth::user()->hasAdminAccess() ? route('dashboard') : route('sales.create') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded bg-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-bold text-lg tracking-tight">POS</span>
                    </a>
                    <button @click="sidebarOpen = true" class="text-slate-400 hover:text-white focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-slate-900/50 backdrop-blur-md border-b border-white/5 flex-shrink-0 z-10 hidden md:flex items-center">
                        <button @click="sidebarOpen = true" x-show="!sidebarOpen" style="display: none;" class="ml-6 text-slate-400 hover:text-white focus:outline-none transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="py-5 px-6 lg:px-8 flex-1">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto custom-scrollbar">
                    <!-- Mobile Heading injected here if needed -->
                    @isset($header)
                    <div class="md:hidden bg-slate-900/50 border-b border-white/5 px-4 py-4 mb-4">
                        {{ $header }}
                    </div>
                    @endisset

                    <div class="p-6 lg:p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        
        <style>
            /* Custom Scrollbar for a more terminal/premium feel */
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
        </style>
    </body>
</html>
