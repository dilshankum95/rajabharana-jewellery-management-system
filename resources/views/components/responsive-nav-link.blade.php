@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2.5 rounded-lg border-l-2 border-jewel-gold text-base font-semibold text-white bg-white/5'
    : 'block w-full ps-3 pe-4 py-2.5 rounded-lg border-l-2 border-transparent text-base font-medium text-slate-400 hover:text-white hover:bg-white/5';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
