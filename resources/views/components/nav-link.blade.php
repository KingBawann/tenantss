@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-semibold leading-5 text-white bg-slate-800 rounded-lg transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-slate-300 hover:text-white hover:bg-slate-800/50 rounded-lg transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
