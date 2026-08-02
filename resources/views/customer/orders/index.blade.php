<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
            <h1 class="jewel-page-title text-xl">My Orders</h1>
            <p class="jewel-page-subtitle">Track and manage all your jewellery orders</p>
            </div>
            <a href="{{ route('orders.create') }}" class="jewel-btn">+ New Order</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="jewel-card overflow-hidden">
                @if($orders->isEmpty())
                    <x-empty-state title="No orders found" description="Place your first custom jewellery order today.">
                        <a href="{{ route('orders.create') }}" class="jewel-btn">Place an Order</a>
                    </x-empty-state>
                @else
                    <div class="overflow-x-auto">
                        <table class="jewel-table min-w-full">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Item</th>
                                    <th>Design</th>
                                    <th>Delivery</th>
                                    <th>Price</th>
                                    <th>Invoice</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr class="hover:bg-jewel-cream/30 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-jewel-dark">{{ $order->order_number }}</span>
                                            <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">{{ $order->item_type_label }}</p>
                                            @if($order->item_name)
                                                <p class="text-xs text-gray-500">{{ $order->item_name }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $order->design_type->label() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $order->expected_delivery_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($order->hasPrice())
                                                <span class="font-semibold text-jewel-gold-dark">LKR {{ number_format($order->estimated_price, 0) }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->invoice && $order->invoice->isIssued())
                                                <a href="{{ route('orders.invoice.show', $order) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">
                                                    {{ $order->invoice->invoice_number }}
                                                </a>
                                                <div class="mt-1"><x-invoice-status-badge :status="$order->invoice->invoice_status" /></div>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-order-status-badge :status="$order->status" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('orders.show', $order) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-jewel-gold/10">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
