<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">{{ $order->order_number }}</h1>
                <p class="jewel-page-subtitle">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <x-order-status-badge :status="$order->status" class="text-sm" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-delivery-alert :order="$order" variant="customer" />

            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Main details --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Design --}}
                    <section class="jewel-card jewel-card-body">
                        <h3 class="jewel-section-title mb-4">Design</h3>
                        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-400">Design Type</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->design_type->label() }}</dd>
                            </div>
                            @if($order->catalogDesign)
                                <div>
                                    <dt class="text-gray-400">Catalog Design</dt>
                                    <dd class="mt-1 font-medium text-jewel-dark">{{ $order->catalogDesign->name }} ({{ $order->catalogDesign->code }})</dd>
                                </div>
                            @endif
                            @if($order->reference_image_url)
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-400 mb-2">Reference Image</dt>
                                    <dd>
                                        <a href="{{ $order->reference_image_url }}" target="_blank">
                                            <img src="{{ $order->reference_image_url }}" alt="Reference design"
                                                class="rounded-lg border border-jewel-gold/20 max-h-64 object-contain">
                                        </a>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </section>

                    {{-- Item specs --}}
                    <section class="jewel-card jewel-card-body">
                        <h3 class="jewel-section-title mb-4">Specifications</h3>
                        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-400">Item Type</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->item_type_label }}</dd>
                            </div>
                            @if($order->item_name)
                                <div>
                                    <dt class="text-gray-400">Piece Name</dt>
                                    <dd class="mt-1 font-medium text-jewel-dark">{{ $order->item_name }}</dd>
                                </div>
                            @endif
                            @if($order->size)
                                <div>
                                    <dt class="text-gray-400">Size</dt>
                                    <dd class="mt-1 font-medium text-jewel-dark">{{ $order->size }}</dd>
                                </div>
                            @endif
                            @if($order->weight_grams)
                                <div>
                                    <dt class="text-gray-400">Weight</dt>
                                    <dd class="mt-1 font-medium text-jewel-dark">{{ $order->weight_grams }} g</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-400">Gold Quality</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->gold_quality_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400">Quantity</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->quantity }}</dd>
                            </div>
                            @if($order->gemstone_type)
                                <div>
                                    <dt class="text-gray-400">Gemstone</dt>
                                    <dd class="mt-1 font-medium text-jewel-dark">{{ $order->gemstone_type }}</dd>
                                </div>
                            @endif
                            @if($order->gemstone_details)
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-400">Gemstone Details</dt>
                                    <dd class="mt-1 text-jewel-dark">{{ $order->gemstone_details }}</dd>
                                </div>
                            @endif
                            @if($order->specifications)
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-400">Specifications</dt>
                                    <dd class="mt-1 text-jewel-dark whitespace-pre-line">{{ $order->specifications }}</dd>
                                </div>
                            @endif
                            @if($order->special_instructions)
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-400">Special Instructions</dt>
                                    <dd class="mt-1 text-jewel-dark whitespace-pre-line">{{ $order->special_instructions }}</dd>
                                </div>
                            @endif
                        </dl>
                    </section>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <section class="jewel-card jewel-card-body">
                        <h3 class="jewel-section-title mb-4">Price</h3>
                        @if($order->hasPrice())
                            @if($order->catalog_unit_price && $order->quantity > 1)
                                <p class="text-xs text-slate-400 mb-2">
                                    LKR {{ number_format($order->catalog_unit_price, 2) }} &times; {{ $order->quantity }}
                                </p>
                            @endif
                            <p class="font-display text-2xl font-semibold text-jewel-gold-dark">
                                LKR {{ number_format($order->estimated_price, 2) }}
                            </p>
                            <p class="text-xs text-slate-400 mt-2">Total order price</p>
                        @else
                            <p class="text-sm text-amber-600 font-medium">Price pending review</p>
                            <p class="text-xs text-slate-400 mt-2">Our team will confirm the price for your custom design shortly.</p>
                        @endif
                    </section>

                    @if($order->invoice && $order->invoice->isIssued())
                    <section class="jewel-card jewel-card-body">
                        <h3 class="jewel-section-title mb-4">Invoice</h3>
                        <p class="text-sm text-slate-600 mb-2">{{ $order->invoice->invoice_number }}</p>
                        <x-invoice-status-badge :status="$order->invoice->invoice_status" class="mb-3" />
                        <p class="font-display text-xl font-semibold text-jewel-gold-dark mb-4">
                            LKR {{ number_format($order->invoice->grand_total, 2) }}
                        </p>
                        <a href="{{ route('orders.invoice.show', $order) }}" class="jewel-btn-outline w-full text-center block">
                            View Invoice
                        </a>
                    </section>
                    @endif

                    <x-order-workflow-status :order="$order" />

                    <section class="jewel-card jewel-card-body">
                        <h3 class="jewel-section-title mb-4">Delivery</h3>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-400">Expected Delivery</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->expected_delivery_date->format('F d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400">Contact Phone</dt>
                                <dd class="mt-1 font-medium text-jewel-dark">{{ $order->contact_phone }}</dd>
                            </div>
                            @if($order->delivery_address)
                                <div>
                                    <dt class="text-gray-400">Address</dt>
                                    <dd class="mt-1 text-jewel-dark whitespace-pre-line">{{ $order->delivery_address }}</dd>
                                </div>
                            @endif
                        </dl>
                    </section>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('orders.index') }}" class="text-center text-sm text-gray-500 hover:text-jewel-dark transition">
                            &larr; Back to My Orders
                        </a>

                        @if($order->status === \App\Enums\OrderStatus::Pending)
                            <form method="POST" action="{{ route('orders.cancel', $order) }}"
                                onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="jewel-btn-danger w-full">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
