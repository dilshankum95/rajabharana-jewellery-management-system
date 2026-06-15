@props(['variant' => 'light'])

@php
    $lightClass = 'text-xs text-slate-500 tabular-nums';
    $darkClass = 'text-xs text-slate-400 tabular-nums';
    $adminClass = 'text-xs text-slate-500 tabular-nums hidden md:block';
    $class = match ($variant) {
        'dark' => $darkClass,
        'admin' => $adminClass,
        default => $lightClass,
    };
@endphp

<div {{ $attributes->merge(['class' => $class]) }}
    x-data="{
        display: '',
        update() {
            this.display = new Intl.DateTimeFormat('en-LK', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: '{{ config('app.timezone') }}'
            }).format(new Date());
        }
    }"
    x-init="update(); setInterval(() => update(), 1000)"
    x-text="display">
</div>
