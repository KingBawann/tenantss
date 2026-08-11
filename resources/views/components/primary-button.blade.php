<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-slate-900 border border-transparent rounded-md font-semibold text-sm text-white tracking-wide hover:bg-black focus:bg-black active:bg-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
