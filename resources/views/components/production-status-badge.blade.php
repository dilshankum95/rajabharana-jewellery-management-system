@props(['status'])

@if($status)
    @php
        $color = $status->color();
    @endphp
    <span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {$color}"]) }}>
        {{ $status->label() }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500']) }}>
        Not started
    </span>
@endif
