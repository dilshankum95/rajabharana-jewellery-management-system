<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to order</a>
            <h1 class="jewel-page-title text-xl mt-1">Create Invoice</h1>
            <p class="jewel-page-subtitle">Order {{ $order->order_number }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <section class="jewel-card jewel-card-body space-y-6">
            <x-alert type="info">
                A draft invoice will be created from the order price. Tax ({{ number_format($taxRate, 2) }}%) and category discount ({{ $categoryLabel }}: {{ number_format($discountPercent, 2) }}%) will be applied automatically.
            </x-alert>

            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400">Customer</dt>
                    <dd class="mt-1 font-medium">{{ $order->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Order Status</dt>
                    <dd class="mt-1"><x-order-status-badge :status="$order->status" /></dd>
                </div>
                <div>
                    <dt class="text-gray-400">Item</dt>
                    <dd class="mt-1 font-medium">{{ $order->item_type_label }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Order Price</dt>
                    <dd class="mt-1 font-semibold text-jewel-gold-dark">LKR {{ number_format($order->estimated_price, 2) }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('admin.invoices.store') }}" class="pt-4 border-t border-jewel-gold/10">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <x-input-error :messages="$errors->get('order_id')" class="mb-4" />

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="jewel-btn">Create Draft Invoice</button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="jewel-btn-outline text-center">Cancel</a>
                </div>
            </form>
        </section>
    </div>
</x-admin-layout>
