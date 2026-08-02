@props(['status'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium '.$status->badgeClass()]) }}>
    {{ $status->label() }}
</span>
