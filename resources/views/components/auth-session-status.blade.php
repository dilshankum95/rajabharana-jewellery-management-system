@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-emerald-700/90']) }}>
        {{ $status }}
    </div>
@endif
