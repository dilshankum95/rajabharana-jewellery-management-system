<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="jewel-page-title text-xl">Orders</h1>
                <p class="jewel-page-subtitle">Manage and process customer orders</p>
            </div>
            @if(($dueOrdersCount ?? 0) > 0)
                <a href="{{ route('admin.orders.index', ['due' => 1]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold {{ ($filters['due'] ?? false) ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-900 hover:bg-amber-200' }} transition">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Due orders ({{ $dueOrdersCount }})
                </a>
            @endif
        </div>
    </x-slot>

    @if($filters['due'] ?? false)
        <x-alert type="warning" class="mb-6">
            Showing due orders only (overdue or due within {{ config('jewellery.delivery_reminder_days') }} days).
            <a href="{{ route('admin.orders.index') }}" class="underline font-medium ml-1">Show all orders</a>
        </x-alert>
    @endif

    {{-- Filters --}}
    <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
        @if($filters['due'] ?? false)
            <input type="hidden" name="due" value="1">
        @endif
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search order #, customer name or email..."
            maxlength="100"
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
    <x-input-error :messages="$errors->get('search')" class="mb-4" />
    <x-input-error :messages="$errors->get('status')" class="mb-4" />

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Order ID</th>
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
                        <tr @class([
                            'transition',
                            'bg-amber-50 hover:bg-amber-100/80 border-l-4 border-l-amber-500' => $order->isDeliveryOverdue(),
                            'bg-sky-50 hover:bg-sky-100/70 border-l-4 border-l-sky-400' => $order->isDeliveryDueSoon(),
                            'hover:bg-jewel-cream/30' => ! $order->isDeliveryOverdue() && ! $order->isDeliveryDueSoon(),
                        ])>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-jewel-dark">{{ $order->order_number }}</span>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                                @if($order->isDeliveryOverdue())
                                    <span class="mt-1 inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-200 text-amber-900">Overdue</span>
                                @elseif($order->isDeliveryDueSoon())
                                    <span class="mt-1 inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-200 text-sky-900">Due soon</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $order->item_type_label }}
                                <p class="text-xs text-gray-400">{{ $order->design_type->label() }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span @class([
                                    'font-semibold' => $order->isDeliveryOverdue() || $order->isDeliveryDueSoon(),
                                    'text-amber-800' => $order->isDeliveryOverdue(),
                                    'text-sky-800' => $order->isDeliveryDueSoon(),
                                    'text-gray-600' => ! $order->isDeliveryOverdue() && ! $order->isDeliveryDueSoon(),
                                ])>
                                    {{ $order->expected_delivery_date->format('M d, Y') }}
                                </span>
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
