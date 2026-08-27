<x-technician-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <a href="{{ route('technician.dashboard') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to my jobs</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $order->order_number }}</h1>
                <p class="jewel-page-subtitle">Workshop job details</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-task-status-badge :status="$order->task_status" />
                <x-production-status-badge :status="$order->production_status" />
            </div>
        </div>
    </x-slot>

    @if($order->isDeliveryOverdue() || $order->isDeliveryDueSoon())
        <x-alert type="warning" class="mb-6">
            @if($order->isDeliveryOverdue())
                This job is past the expected delivery date ({{ $order->expected_delivery_date->format('M d, Y') }}). Prioritize completion or notify admin if more time is needed.
            @else
                Expected delivery is {{ $order->expected_delivery_date->format('M d, Y') }} — within {{ config('jewellery.delivery_reminder_days', 3) }} days.
            @endif
        </x-alert>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="jewel-card jewel-card-body">
                <h2 class="jewel-section-title mb-4">Design</h2>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-400">Design Type</dt>
                        <dd class="mt-1 font-medium">{{ $order->design_type->label() }}</dd>
                    </div>
                    @if($order->catalogDesign)
                        <div>
                            <dt class="text-gray-400">Catalog Design</dt>
                            <dd class="mt-1 font-medium">{{ $order->catalogDesign->name }} ({{ $order->catalogDesign->code }})</dd>
                        </div>
                    @endif
                    @if($order->reference_image_url)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-400 mb-2">Reference Image</dt>
                            <dd>
                                <a href="{{ $order->reference_image_url }}" target="_blank">
                                    <img src="{{ $order->reference_image_url }}" alt="Reference"
                                        class="rounded-lg border border-jewel-gold/20 max-h-72 object-contain">
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="jewel-card jewel-card-body">
                <h2 class="jewel-section-title mb-4">Specifications</h2>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-400">Item Type</dt><dd class="mt-1 font-medium">{{ $order->item_type_label }}</dd></div>
                    @if($order->item_name)<div><dt class="text-gray-400">Piece Name</dt><dd class="mt-1">{{ $order->item_name }}</dd></div>@endif
                    @if($order->size)<div><dt class="text-gray-400">Size</dt><dd class="mt-1">{{ $order->size }}</dd></div>@endif
                    @if($order->weight_grams)<div><dt class="text-gray-400">Weight</dt><dd class="mt-1">{{ $order->weight_grams }} g</dd></div>@endif
                    <div><dt class="text-gray-400">Gold Quality</dt><dd class="mt-1">{{ $order->gold_quality_label }}</dd></div>
                    <div><dt class="text-gray-400">Quantity</dt><dd class="mt-1">{{ $order->quantity }}</dd></div>
                    @if($order->gemstone_type)<div><dt class="text-gray-400">Gemstone</dt><dd class="mt-1">{{ $order->gemstone_type }}</dd></div>@endif
                    @if($order->gemstone_details)<div class="sm:col-span-2"><dt class="text-gray-400">Gemstone Details</dt><dd class="mt-1 whitespace-pre-line">{{ $order->gemstone_details }}</dd></div>@endif
                    @if($order->specifications)<div class="sm:col-span-2"><dt class="text-gray-400">Specifications</dt><dd class="mt-1 whitespace-pre-line">{{ $order->specifications }}</dd></div>@endif
                    @if($order->special_instructions)<div class="sm:col-span-2"><dt class="text-gray-400">Special Instructions</dt><dd class="mt-1 whitespace-pre-line">{{ $order->special_instructions }}</dd></div>@endif
                    <div><dt class="text-gray-400">Expected Delivery</dt><dd class="mt-1 font-medium">{{ $order->expected_delivery_date->format('F d, Y') }}</dd></div>
                    <div><dt class="text-gray-400">Assigned</dt><dd class="mt-1">{{ $order->assigned_at?->format('M d, Y') ?? '—' }}</dd></div>
                </dl>
            </section>

            @if($order->productionLogs->isNotEmpty())
                <section class="jewel-card jewel-card-body">
                    <h2 class="jewel-section-title mb-4">Production Log</h2>
                    <ol class="space-y-4">
                        @foreach($order->productionLogs as $log)
                            <li class="text-sm border-l-2 border-violet-200 pl-4">
                                <p class="font-medium text-slate-700">
                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                    @if($log->from_status && $log->to_status && $log->from_status !== $log->to_status)
                                        · {{ $log->from_status->label() }} → {{ $log->to_status->label() }}
                                    @endif
                                </p>
                                @if($log->note)
                                    <p class="mt-1 text-slate-600 whitespace-pre-line">{{ $log->note }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>

        <div class="space-y-6">
            @if($order->technicianCanRespondToTask(auth()->user()))
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Respond to Task</h2>
                    <p class="text-sm text-slate-500 mb-4">This order has been assigned to you. Accept to begin production or reject if you cannot take it.</p>
                    <div class="flex flex-col gap-3">
                        <form method="POST" action="{{ route('technician.jobs.task', $order) }}">
                            @csrf
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="jewel-btn w-full">Accept Task</button>
                        </form>
                        <form method="POST" action="{{ route('technician.jobs.task', $order) }}"
                            onsubmit="return confirm('Reject this task? The administrator will need to reassign it.')">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="jewel-btn-danger w-full">Reject Task</button>
                        </form>
                    </div>
                </section>
            @elseif($order->task_status === \App\Enums\TaskStatus::Rejected)
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Task Rejected</h2>
                    <p class="text-sm text-slate-500">You rejected this task. Contact the administrator if reassignment is needed.</p>
                    <div class="mt-4"><x-task-status-badge :status="$order->task_status" /></div>
                </section>
            @elseif($order->technicianCanUpdateProduction(auth()->user()))
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Update Production</h2>

                    <form method="POST" action="{{ route('technician.jobs.production', $order) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="production_status" class="jewel-label">Production Status *</label>
                            <select id="production_status" name="production_status" required class="jewel-input mt-1.5">
                                @foreach($productionOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('production_status', $order->production_status?->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Status can only move forward one step at a time.</p>
                            <x-input-error :messages="$errors->get('production_status')" class="mt-1" />
                        </div>

                        <div>
                            <label for="note" class="jewel-label">Workshop Note</label>
                            <textarea id="note" name="note" rows="4" maxlength="2000"
                                placeholder="Progress update, issues, or completion notes..."
                                class="jewel-input mt-1.5">{{ old('note') }}</textarea>
                            <x-input-error :messages="$errors->get('note')" class="mt-1" />
                        </div>

                        <button type="submit" class="jewel-btn w-full">Save Update</button>
                    </form>
                </section>
            @elseif($order->production_status === \App\Enums\ProductionStatus::ReadyToPickup)
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Production Complete</h2>
                    <p class="text-sm text-slate-500">This order is ready for customer pickup. No further production updates are needed.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-task-status-badge :status="$order->task_status" />
                        <x-production-status-badge :status="$order->production_status" />
                    </div>
                </section>
            @else
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Job Status</h2>
                    <p class="text-sm text-slate-500">This job is not open for updates at the moment.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-task-status-badge :status="$order->task_status" />
                        <x-production-status-badge :status="$order->production_status" />
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-technician-layout>
