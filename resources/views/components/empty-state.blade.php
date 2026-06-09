@props(['title', 'description' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'jewel-empty']) }}>
    <x-jewellery-logo class="w-14 h-14 text-jewel-gold/30 mb-5" />
    <p class="font-display text-lg font-semibold text-jewel-dark">{{ $title }}</p>
    @if($description)
        <p class="mt-2 text-sm text-gray-500 max-w-sm">{{ $description }}</p>
    @endif
    @if($action)
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
