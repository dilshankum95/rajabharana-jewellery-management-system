@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-9 h-9 text-xs',
        'lg' => 'w-16 h-16 text-lg',
        'xl' => 'w-24 h-24 text-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if($user->profile_photo_url)
    <img {{ $attributes->merge(['class' => "$sizeClass rounded-full object-cover ring-2 ring-jewel-gold/30 shrink-0"]) }}
        src="{{ $user->profile_photo_url }}"
        alt="{{ $user->name }}">
@else
    <span {{ $attributes->merge(['class' => "$sizeClass rounded-full bg-gradient-to-br from-jewel-gold to-jewel-gold-dark flex items-center justify-center font-bold text-jewel-dark shrink-0 ring-2 ring-jewel-gold/30"]) }}>
        {{ $user->initials }}
    </span>
@endif
