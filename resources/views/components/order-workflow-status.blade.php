@props(['order'])

<section {{ $attributes->merge(['class' => 'jewel-card jewel-card-body']) }}>
    <h3 class="jewel-section-title mb-4">Order Progress</h3>
    <dl class="space-y-4 text-sm">
        <div>
            <dt class="text-gray-400">Order Status</dt>
            <dd class="mt-1.5"><x-order-status-badge :status="$order->status" /></dd>
        </div>

        @if($order->status === \App\Enums\OrderStatus::Accepted)
            <div>
                <dt class="text-gray-400">Assigned Technician</dt>
                <dd class="mt-1 font-medium text-jewel-dark">
                    {{ $order->assignedTechnician?->name ?? 'Not assigned yet' }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-400">Task Status</dt>
                <dd class="mt-1.5"><x-task-status-badge :status="$order->task_status" /></dd>
            </div>

            <div>
                <dt class="text-gray-400">Production Status</dt>
                <dd class="mt-1.5"><x-production-status-badge :status="$order->production_status" /></dd>
            </div>
        @endif
    </dl>
</section>
