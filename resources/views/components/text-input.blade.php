@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 rounded-md shadow-sm transition-colors duration-150 ease-in-out disabled:opacity-50 disabled:bg-slate-50 disabled:cursor-not-allowed']) }}>
