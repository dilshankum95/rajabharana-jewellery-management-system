@props(['order', 'variant' => 'admin'])

@if($variant === 'customer' && $order->isDeliveryOverdue())
    <x-alert type="info" {{ $attributes }}>
        <p class="font-semibold">Delivery date update may be needed</p>
        <p class="mt-1">
            Your expected delivery was {{ $order->expected_delivery_date->format('M d, Y') }}.
            Our workshop is still working on this piece — we will contact you if the date changes.
        </p>
    </x-alert>
@elseif($variant === 'admin' && ($message = $order->deliveryAlertMessage()))
    @php
        $type = $order->deliveryAlertType() === 'overdue' ? 'warning' : 'info';
    @endphp
    <x-alert :type="$type" {{ $attributes }}>
        <p class="font-semibold">
            {{ $order->deliveryAlertType() === 'overdue' ? 'Delivery overdue' : 'Delivery deadline approaching' }}
        </p>
        <p class="mt-1">{{ $message }}</p>
    </x-alert>
@endif
