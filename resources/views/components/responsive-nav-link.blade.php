@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-blue-400 text-left text-base font-semibold text-white bg-slate-800 focus:outline-none focus:text-blue-200 focus:bg-slate-700 focus:border-blue-500 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none focus:text-white focus:bg-slate-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
