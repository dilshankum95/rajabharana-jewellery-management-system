<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Billing</h1>
            <p class="jewel-page-subtitle">Manage invoices for customer orders</p>
        </div>
    </x-slot>

    <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search invoice #, order #, customer..."
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
            <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm text-gray-500 hover:text-jewel-dark">Clear</a>
        @endif
    </form>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-jewel-cream/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-jewel-dark">{{ $invoice->invoice_number }}</span>
                                <p class="text-xs text-gray-400">
                                    @if($invoice->issue_date)
                                        Issued {{ $invoice->issue_date->format('M d, Y') }}
                                    @else
                                        Created {{ $invoice->created_at->format('M d, Y') }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.orders.show', $invoice->order) }}" class="font-medium text-jewel-gold-dark hover:text-jewel-gold">
                                    {{ $invoice->order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $invoice->customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $invoice->customer->email }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-jewel-gold-dark">
                                LKR {{ number_format($invoice->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-invoice-status-badge :status="$invoice->invoice_status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No invoices yet. Generate one from a confirmed order with a set price.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-jewel-gold/10">{{ $invoices->links() }}</div>
        @endif
    </div>
</x-admin-layout>
