<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-400 hover:text-jewel-gold-dark">&larr; Back to customers</a>
            <h1 class="font-display text-xl font-semibold text-jewel-dark mt-1">{{ $customer->name }}</h1>
            <p class="text-sm text-gray-500">Customer since {{ $customer->created_at->format('F Y') }}</p>
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <section class="bg-white rounded-xl border border-jewel-gold/10 shadow-sm p-6">
            <h2 class="font-display text-lg font-semibold text-jewel-dark mb-4">Contact Info</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Email</dt><dd class="mt-0.5">{{ $customer->email }}</dd></div>
                <div><dt class="text-gray-400">Phone</dt><dd class="mt-0.5">{{ $customer->phone ?? '—' }}</dd></div>
                @if($customer->address)
                    <div><dt class="text-gray-400">Address</dt><dd class="mt-0.5 whitespace-pre-line">{{ $customer->address }}</dd></div>
                @endif
                @if($customer->city)
                    <div><dt class="text-gray-400">City</dt><dd class="mt-0.5">{{ $customer->city }}</dd></div>
                @endif
                <div class="pt-3 border-t border-jewel-gold/10">
                    <dt class="text-gray-400">Total Orders</dt>
                    <dd class="mt-0.5 font-display text-2xl font-semibold text-jewel-gold-dark">{{ $customer->orders_count }}</dd>
                </div>
            </dl>
        </section>

        <section class="lg:col-span-2 bg-white rounded-xl border border-jewel-gold/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-jewel-gold/10">
                <h2 class="font-display text-lg font-semibold text-jewel-dark">Order History</h2>
            </div>
            @if($orders->isEmpty())
                <p class="px-6 py-10 text-center text-gray-400">No orders yet</p>
            @else
                <div class="divide-y divide-jewel-gold/10">
                    @foreach($orders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="block px-6 py-4 hover:bg-jewel-cream/50 transition">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-jewel-dark">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->item_type_label }} · {{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($order->estimated_price)
                                        <span class="text-sm text-jewel-gold-dark">LKR {{ number_format($order->estimated_price, 0) }}</span>
                                    @endif
                                    <x-order-status-badge :status="$order->status" />
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-jewel-gold/10">{{ $orders->links() }}</div>
                @endif
            @endif
        </section>
    </div>
</x-admin-layout>
