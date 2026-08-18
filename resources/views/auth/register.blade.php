<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('app.name', 'POS System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="font-sans text-slate-300 bg-slate-950 antialiased selection:bg-blue-500/30 selection:text-blue-200 h-full flex overflow-hidden">

    <!-- Left Side: Branding & Value Prop -->
    <div class="hidden lg:flex w-[40%] bg-slate-900 relative flex-col justify-between p-12 border-r border-white/5">
        <!-- Background accents -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/20 to-transparent pointer-events-none"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 translate-x-1/3"></div>

        <div class="z-10">
            <div class="flex items-center gap-2 mb-16">
                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">POS<span class="text-slate-500">System</span></span>
            </div>

            <h1 class="text-4xl font-bold text-white mb-6 leading-tight">Scale your retail operations with precision.</h1>
            <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-md">
                Join thousands of businesses managing their inventory, sales, and multi-location operations on our enterprise-grade POS platform.
            </p>

            <ul class="space-y-4">
                <li class="flex items-center gap-3 text-slate-300">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Real-time inventory sync
                </li>
                <li class="flex items-center gap-3 text-slate-300">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Advanced sales analytics
                </li>
                <li class="flex items-center gap-3 text-slate-300">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Multi-tenant architecture
                </li>
            </ul>
        </div>

        <div class="z-10 mt-10">
            <div class="bg-slate-950/50 border border-white/5 p-6 rounded-xl backdrop-blur-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-slate-800 border border-white/10 flex items-center justify-center font-bold text-slate-400">JD</div>
                    <div>
                        <div class="font-bold text-white">Jane Doe</div>
                        <div class="text-sm text-slate-500">Operations Manager, SuperMart</div>
                    </div>
                </div>
                <p class="text-sm text-slate-400 italic">"Switching to this POS system cut our checkout times in half and gave us unprecedented visibility into our multi-store inventory."</p>
            </div>
        </div>
    </div>

    <!-- Right Side: Form -->
    <div class="flex-1 flex flex-col h-full bg-slate-950 overflow-y-auto custom-scrollbar relative">
        <!-- Mobile Header (hidden on lg) -->
        <div class="lg:hidden p-6 border-b border-white/5 flex items-center justify-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-white">POS<span class="text-slate-500">System</span></span>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-white mb-2 tracking-tight">Create your account</h2>
                    <p class="text-slate-500">Get started with a free 14-day trial. No credit card required.</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-900/20 border border-red-500/50">
                        <div class="font-medium text-red-400 mb-1">Whoops! Something went wrong.</div>
                        <ul class="text-sm text-red-300 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-8" x-data="{ plan: 'free' }">
                    @csrf

                    <!-- Section: Account Details -->
                    <div class="space-y-5">
                        <div class="flex items-center gap-4">
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Account Details</span>
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Full Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                placeholder="John Doe">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                placeholder="john@example.com">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                                <input id="password" type="password" name="password" required
                                    class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Business Profile -->
                    <div class="space-y-5 pt-2">
                        <div class="flex items-center gap-4">
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Business Profile</span>
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                        </div>

                        <div>
                            <label for="business_name" class="block text-sm font-medium text-slate-300 mb-1.5">Business Name</label>
                            <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" required
                                class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                placeholder="Acme Corp">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="business_type" class="block text-sm font-medium text-slate-300 mb-1.5">Industry</label>
                                <select id="business_type" name="business_type" required
                                    class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm appearance-none">
                                    <option value="retail" {{ old('business_type') == 'retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="hospitality" {{ old('business_type') == 'hospitality' ? 'selected' : '' }}>Hospitality</option>
                                    <option value="service" {{ old('business_type') == 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-300 mb-1.5">Phone <span class="text-slate-500 font-normal">(Optional)</span></label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full bg-slate-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors shadow-sm"
                                    placeholder="+1 (555) 000-0000">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Select Plan -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Select Plan</span>
                            <div class="h-[1px] flex-1 bg-white/10"></div>
                        </div>

                        <input type="hidden" name="subscription_plan" x-model="plan">

                        <div class="grid grid-cols-1 gap-3">
                            <!-- Free Plan -->
                            <div @click="plan = 'free'" 
                                :class="{'border-blue-500 bg-blue-900/10': plan === 'free', 'border-white/10 bg-slate-900 hover:border-slate-600': plan !== 'free'}"
                                class="relative cursor-pointer border rounded-xl p-4 transition-all duration-200">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="font-bold text-white flex items-center gap-2">
                                        Starter
                                        <span x-show="plan === 'free'" class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    </div>
                                    <div class="font-bold text-white">$0<span class="text-xs text-slate-500 font-normal">/mo</span></div>
                                </div>
                                <div class="text-xs text-slate-400">Basic POS features, 1 register, community support.</div>
                            </div>

                            <!-- Pro Plan -->
                            <div @click="plan = 'pro'" 
                                :class="{'border-blue-500 bg-blue-900/10': plan === 'pro', 'border-white/10 bg-slate-900 hover:border-slate-600': plan !== 'pro'}"
                                class="relative cursor-pointer border rounded-xl p-4 transition-all duration-200">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="font-bold text-white flex items-center gap-2">
                                        Professional
                                        <span class="bg-blue-600 text-white text-[10px] uppercase font-bold px-1.5 py-0.5 rounded shrink-0">Popular</span>
                                        <span x-show="plan === 'pro'" class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    </div>
                                    <div class="font-bold text-white">$49<span class="text-xs text-slate-500 font-normal">/mo</span></div>
                                </div>
                                <div class="text-xs text-slate-400">Unlimited registers, advanced reporting, priority support.</div>
                            </div>

                            <!-- Enterprise Plan -->
                            <div @click="plan = 'enterprise'" 
                                :class="{'border-blue-500 bg-blue-900/10': plan === 'enterprise', 'border-white/10 bg-slate-900 hover:border-slate-600': plan !== 'enterprise'}"
                                class="relative cursor-pointer border rounded-xl p-4 transition-all duration-200">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="font-bold text-white flex items-center gap-2">
                                        Enterprise
                                        <span x-show="plan === 'enterprise'" class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    </div>
                                    <div class="font-bold text-white">$199<span class="text-xs text-slate-500 font-normal">/mo</span></div>
                                </div>
                                <div class="text-xs text-slate-400">Custom integrations, dedicated account manager, API access.</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-blue-500/20 transition-all flex justify-center items-center gap-2">
                            Create Account
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        
                        <p class="text-center text-sm text-slate-500">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">Sign in to your dashboard</a>
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>
