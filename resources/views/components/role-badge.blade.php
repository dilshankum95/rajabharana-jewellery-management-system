@props(['role'])

@php
    $colors = match($role->value) {
        'admin' => 'bg-violet-100 text-violet-800',
        'manager' => 'bg-sky-100 text-sky-800',
        'staff' => 'bg-emerald-100 text-emerald-800',
        'customer' => 'bg-slate-100 text-slate-600',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {$colors}"]) }}>
    {{ $role->label() }}
</span>
