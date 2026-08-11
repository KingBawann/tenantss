<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white tracking-tight">Welcome back</h2>
        <p class="text-sm text-slate-400 mt-1">Enter your credentials to access your terminal.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block w-full bg-slate-900 border border-slate-700/50 rounded-lg shadow-sm py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ease-out-expo duration-200"
                   placeholder="cashier@example.com">
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full bg-slate-900 border border-slate-700/50 rounded-lg shadow-sm py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ease-out-expo duration-200"
                   placeholder="••••••••">
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-1">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
            <label for="remember_me" class="ml-2 block text-sm text-slate-400">
                Remember me for 30 days
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-blue-500 transition-all ease-out-expo duration-200">
                Sign in to POS
            </button>
        </div>
    </form>
</x-guest-layout>
