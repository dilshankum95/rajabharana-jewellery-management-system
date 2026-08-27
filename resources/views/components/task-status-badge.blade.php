@props(['status'])

@php
    $color = $status->color();
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {$color}"]) }}>
    {{ $status->label() }}
</span>
