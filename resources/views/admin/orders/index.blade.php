<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Orders</h1>
            <p class="jewel-page-subtitle">Manage and process customer orders</p>
        </div>
    </x-slot>

    {{-- Filters --}}
    <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search order #, customer name or email..."
            class="jewel-input sm:flex-1">
        <select name="status" class="jewel-input sm:w-48">
            <option value="">All statuses</option>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="jewel-btn px-6">Filter</button>
        @if(!empty(array_filter($filters ?? [])))
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm text-gray-500 hover:text-jewel-dark">Clear</a>
        @endif
    </form>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Delivery</th>
                        <th>Quote</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="hover:bg-jewel-cream/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-jewel-dark">{{ $order->order_number }}</span>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $order->item_type_label }}
                                <p class="text-xs text-gray-400">{{ $order->design_type->label() }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $order->expected_delivery_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($order->estimated_price)
                                    <span class="font-medium text-jewel-gold-dark">LKR {{ number_format($order->estimated_price, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-order-status-badge :status="$order->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-jewel-gold/10">{{ $orders->links() }}</div>
        @endif
    </div>
</x-admin-layout>
