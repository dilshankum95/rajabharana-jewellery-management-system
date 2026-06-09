@props(['value'])

<label {{ $attributes->merge(['class' => 'jewel-label']) }}>
    {{ $value ?? $slot }}
</label>
