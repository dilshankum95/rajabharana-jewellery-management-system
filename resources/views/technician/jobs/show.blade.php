<x-technician-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <a href="{{ route('technician.dashboard') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to my jobs</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $order->order_number }}</h1>
                <p class="jewel-page-subtitle">Workshop job details — specifications only</p>
            </div>
            <x-order-status-badge :status="$order->status" class="text-sm" />
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
            @if($order->technicianCanUpdate(auth()->user()))
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Update Progress</h2>

                    <form method="POST" action="{{ route('technician.jobs.update', $order) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="jewel-label">Status *</label>
                            <select id="status" name="status" required class="jewel-input mt-1.5">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $order->status->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
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
            @else
                <section class="jewel-card jewel-card-body sticky top-24">
                    <h2 class="jewel-section-title mb-4">Job Status</h2>
                    <p class="text-sm text-slate-500">
                        @if($order->status === \App\Enums\OrderStatus::Ready)
                            This job is marked ready. No further workshop updates are needed.
                        @else
                            This job is no longer open for workshop updates.
                        @endif
                    </p>
                    <div class="mt-4">
                        <x-order-status-badge :status="$order->status" />
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-technician-layout>
