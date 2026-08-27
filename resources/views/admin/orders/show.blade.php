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

    <x-delivery-alert :order="$order" class="mb-6" />

    @include('admin.orders.partials.workflow-panel')

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

            {{-- Billing --}}
            <section class="jewel-card jewel-card-body">
                <h2 class="jewel-section-title mb-4">Billing</h2>
                @if($order->invoice)
                    <p class="text-sm text-slate-600 mb-3">
                        Invoice <span class="font-medium">{{ $order->invoice->invoice_number }}</span>
                    </p>
                    <x-invoice-status-badge :status="$order->invoice->invoice_status" class="mb-4" />
                    <p class="font-display text-xl font-semibold text-jewel-gold-dark mb-4">
                        LKR {{ number_format($order->invoice->grand_total, 2) }}
                    </p>
                    <a href="{{ route('admin.invoices.show', $order->invoice) }}" class="jewel-btn-outline w-full text-center block">
                        View Invoice
                    </a>
                    @can('permission', 'billing.manage')
                        @if($order->invoice->isEditable())
                            <a href="{{ route('admin.invoices.edit', $order->invoice) }}" class="mt-2 text-center block text-sm text-jewel-gold-dark hover:text-jewel-gold">
                                Edit draft
                            </a>
                        @endif
                    @endcan
                @elseif($order->isBillable())
                    @can('permission', 'billing.manage')
                        <p class="text-sm text-slate-500 mb-4">This order is ready to be invoiced.</p>
                        <a href="{{ route('admin.orders.invoice.create', $order) }}" class="jewel-btn w-full text-center block">
                            Generate Invoice
                        </a>
                    @else
                        <p class="text-sm text-slate-500">No invoice has been created for this order yet.</p>
                    @endcan
                @else
                    <p class="text-sm text-slate-500">
                        @if($order->hasInvoice())
                            Invoice already exists.
                        @elseif(! $order->hasPrice())
                            Set an order price before generating an invoice.
                        @elseif($order->status === \App\Enums\OrderStatus::Pending)
                            Accept the order before generating an invoice.
                        @else
                            Invoice not available for this order status.
                        @endif
                    </p>
                @endif
            </section>

            @can('permission', 'orders.manage')
            <section class="jewel-card jewel-card-body sticky top-24">
                <h2 class="jewel-section-title mb-4">Manage Order</h2>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    @if(auth()->user()->isAdmin())
                        <div>
                            <label for="status" class="jewel-label">Order Status *</label>
                            <select id="status" name="status" required class="jewel-input mt-1.5">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $order->status->value) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Only administrators can change order status.</p>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                    @else
                        <div>
                            <label class="jewel-label">Order Status</label>
                            <div class="mt-1.5"><x-order-status-badge :status="$order->status" /></div>
                            <p class="mt-1 text-xs text-gray-400">Contact an administrator to accept or reject this order.</p>
                        </div>
                    @endif

                    <div>
                        <label for="expected_delivery_date" class="jewel-label">Expected Delivery Date *</label>
                        <input id="expected_delivery_date" name="expected_delivery_date" type="date" required
                            value="{{ old('expected_delivery_date', $order->expected_delivery_date->format('Y-m-d')) }}"
                            min="{{ $order->created_at->format('Y-m-d') }}"
                            max="{{ now()->addYear()->format('Y-m-d') }}"
                            class="jewel-input mt-1.5">
                        <p class="mt-1 text-xs text-gray-400">Adjust if the production schedule requires a different date. Customer will see the updated date.</p>
                        <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-1" />
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div>
                            <label for="estimated_price" class="jewel-label">Order Price (LKR)</label>
                            <input id="estimated_price" name="estimated_price" type="number" step="0.01" min="0.01" max="99999999.99"
                                value="{{ old('estimated_price', $order->estimated_price) }}"
                                placeholder="Enter or adjust order price"
                                class="jewel-input mt-1.5">
                            <p class="mt-1 text-xs text-gray-400">Adjust if specifications differ from catalog. Customer sees this total on their order.</p>
                            <x-input-error :messages="$errors->get('estimated_price')" class="mt-1" />
                        </div>
                    @else
                        <div>
                            <label class="jewel-label">Order Price (LKR)</label>
                            @if($order->hasPrice())
                                <p class="mt-1.5 font-display text-xl font-semibold text-jewel-gold-dark">
                                    LKR {{ number_format($order->estimated_price, 2) }}
                                </p>
                            @else
                                <p class="mt-1.5 text-sm text-amber-600 font-medium">No price set yet</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-400">Contact an administrator to set or change the order price.</p>
                        </div>
                    @endif

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
            @else
            <section class="jewel-card jewel-card-body sticky top-24">
                <h2 class="jewel-section-title mb-4">Order Status</h2>
                <p class="text-sm text-slate-500">You have view-only access to this order. Contact an administrator to update status or pricing.</p>
                <div class="mt-4">
                    <x-order-status-badge :status="$order->status" />
                </div>
            </section>
            @endcan
        </div>
    </div>
</x-admin-layout>
