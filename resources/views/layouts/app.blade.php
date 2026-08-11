<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 selection:bg-blue-100 selection:text-blue-900 overflow-hidden">
        <div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="min-h-screen bg-slate-50 flex h-screen">
            
            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative">
                
                <!-- Mobile Header & Hamburger -->
                <div class="md:hidden bg-slate-900 text-white flex items-center justify-between p-4 flex-shrink-0 z-20">
                    <a href="{{ Auth::user() && Auth::user()->hasAdminAccess() ? route('dashboard') : route('sales.create') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-6 w-auto fill-current text-white" />
                        <span class="font-bold text-lg tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    <button @click="sidebarOpen = true" class="text-slate-300 hover:text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200 shadow-sm flex-shrink-0 z-10 hidden md:flex items-center">
                        <button @click="sidebarOpen = true" x-show="!sidebarOpen" style="display: none;" class="ml-6 text-slate-500 hover:text-slate-900 focus:outline-none transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="py-5 px-6 lg:px-8 flex-1">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    <!-- Mobile Heading injected here if needed -->
                    @isset($header)
                    <div class="md:hidden bg-white border-b border-slate-200 px-4 py-4 shadow-sm mb-4">
                        {{ $header }}
                    </div>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
