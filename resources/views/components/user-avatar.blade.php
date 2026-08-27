@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-9 h-9 text-xs',
        'lg' => 'w-16 h-16 text-lg',
        'xl' => 'w-20 h-20 text-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if($user->profile_photo_url)
    <div {{ $attributes->merge(['class' => "$sizeClass rounded-full overflow-hidden ring-2 ring-jewel-gold/30 shrink-0"]) }}>
        <img class="h-full w-full object-cover"
            src="{{ $user->profile_photo_url }}"
            alt="{{ $user->name }}">
    </div>
@else
    <span {{ $attributes->merge(['class' => "$sizeClass rounded-full bg-gradient-to-br from-jewel-gold to-jewel-gold-dark flex items-center justify-center font-bold text-jewel-dark shrink-0 ring-2 ring-jewel-gold/30"]) }}>
        {{ $user->initials }}
    </span>
@endif
