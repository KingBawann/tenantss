<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'POS System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-100 bg-slate-950 antialiased selection:bg-blue-500/30 selection:text-blue-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-950 relative overflow-hidden">
            <!-- Subtle background glow -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="z-10 w-full sm:max-w-[420px] px-6">
                <div class="flex justify-center mb-10">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">POS<span class="text-slate-500">System</span></span>
                    </div>
                </div>

                <div class="bg-slate-900/50 backdrop-blur-xl border border-white/5 shadow-2xl rounded-2xl overflow-hidden p-8">
                    {{ $slot }}
                </div>
                
                <div class="mt-8 text-center text-xs text-slate-500 font-medium tracking-wide">
                    &copy; {{ date('Y') }} Master Tenant Platform. All rights reserved.
                </div>
            </div>
        </div>
    </body>
</html>
