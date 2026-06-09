<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to orders</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $order->order_number }}</h1>
                <p class="jewel-page-subtitle">Submitted {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <x-order-status-badge :status="$order->status" class="text-sm" />
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Customer --}}
            <section class="jewel-card jewel-card-body">
                <h2 class="jewel-section-title mb-4">Customer</h2>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-400">Name</dt>
                        <dd class="mt-1 font-medium">
                            <a href="{{ route('admin.customers.show', $order->user) }}" class="text-jewel-gold-dark hover:text-jewel-gold">
                                {{ $order->user->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Email</dt>
                        <dd class="mt-1">{{ $order->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Contact Phone</dt>
                        <dd class="mt-1">{{ $order->contact_phone }}</dd>
                    </div>
                    @if($order->delivery_address)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-400">Delivery Address</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $order->delivery_address }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- Design --}}
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

            {{-- Specifications --}}
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
                </dl>
            </section>
        </div>

        {{-- Admin actions --}}
        <div class="space-y-6">
            {{-- Pricing summary --}}
            <section class="jewel-card jewel-card-body">
                <h2 class="jewel-section-title mb-4">Order Price</h2>
                @if($order->catalogDesign && $order->catalog_unit_price)
                    <dl class="space-y-2 text-sm mb-4 pb-4 border-b border-slate-100">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Catalog unit price</dt>
                            <dd class="font-medium">LKR {{ number_format($order->catalog_unit_price, 2) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Quantity</dt>
                            <dd class="font-medium">&times; {{ $order->quantity }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 pt-2">
                            <dt class="text-slate-600 font-medium">Calculated total</dt>
                            <dd class="font-semibold text-jewel-gold-dark">LKR {{ number_format($order->catalog_unit_price * $order->quantity, 2) }}</dd>
                        </div>
                    </dl>
                @elseif($order->design_type === \App\Enums\DesignType::Custom)
                    <p class="text-sm text-slate-500 mb-4">Custom design — set the quoted price below once reviewed.</p>
                @endif

                @if($order->hasPrice())
                    <p class="font-display text-2xl font-semibold text-jewel-gold-dark">
                        LKR {{ number_format($order->estimated_price, 2) }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Current price shown to customer</p>
                @else
                    <p class="text-sm text-amber-600 font-medium">No price set yet</p>
                @endif
            </section>

            <section class="jewel-card jewel-card-body sticky top-24">
                <h2 class="jewel-section-title mb-4">Manage Order</h2>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="status" class="jewel-label">Status *</label>
                        <select id="status" name="status" required class="jewel-input mt-1.5">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $order->status->value) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div>
                        <label for="expected_delivery_date" class="jewel-label">Expected Delivery Date *</label>
                        <input id="expected_delivery_date" name="expected_delivery_date" type="date" required
                            value="{{ old('expected_delivery_date', $order->expected_delivery_date->format('Y-m-d')) }}"
                            class="jewel-input mt-1.5">
                        <p class="mt-1 text-xs text-gray-400">Adjust if the production schedule requires a different date. Customer will see the updated date.</p>
                        <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-1" />
                    </div>

                    <div>
                        <label for="estimated_price" class="jewel-label">Order Price (LKR)</label>
                        <input id="estimated_price" name="estimated_price" type="number" step="0.01" min="0.01" max="99999999.99"
                            value="{{ old('estimated_price', $order->estimated_price) }}"
                            placeholder="Enter or adjust order price"
                            class="jewel-input mt-1.5">
                        <p class="mt-1 text-xs text-gray-400">Adjust if specifications differ from catalog. Customer sees this total on their order.</p>
                        <x-input-error :messages="$errors->get('estimated_price')" class="mt-1" />
                    </div>

                    <div>
                        <label for="admin_notes" class="jewel-label">Internal Notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4" maxlength="2000" minlength="3"
                            placeholder="Notes for staff (not visible to customer)..."
                            class="jewel-input mt-1.5">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                        <x-input-error :messages="$errors->get('admin_notes')" class="mt-1" />
                    </div>

                    <button type="submit" class="jewel-btn w-full">Save Changes</button>
                </form>
            </section>
        </div>
    </div>
</x-admin-layout>
