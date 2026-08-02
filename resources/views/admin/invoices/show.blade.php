<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <a href="{{ route('admin.invoices.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to billing</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $invoice->invoice_number }}</h1>
                <p class="jewel-page-subtitle">
                    Order
                    <a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-jewel-gold-dark hover:text-jewel-gold">{{ $invoice->order->order_number }}</a>
                </p>
            </div>
            <x-invoice-status-badge :status="$invoice->invoice_status" class="text-sm" />
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="jewel-card jewel-card-body">
                <div class="grid sm:grid-cols-2 gap-6 mb-6 pb-6 border-b border-jewel-gold/10">
                    <div>
                        <h2 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Bill To</h2>
                        <p class="font-medium text-jewel-dark">{{ $invoice->customer->name }}</p>
                        <p class="text-sm text-gray-500">{{ $invoice->customer->email }}</p>
                        @if($invoice->order->contact_phone)
                            <p class="text-sm text-gray-500 mt-1">{{ $invoice->order->contact_phone }}</p>
                        @endif
                    </div>
                    <div class="text-sm sm:text-right">
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-gray-400">Issue Date</dt>
                                <dd class="font-medium">{{ $invoice->issue_date?->format('F d, Y') ?? '— (draft)' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400">Due Date</dt>
                                <dd class="font-medium">{{ $invoice->due_date?->format('F d, Y') ?? '—' }}</dd>
                            </div>
                            @if($invoice->creator)
                                <div>
                                    <dt class="text-gray-400">Created By</dt>
                                    <dd class="font-medium">{{ $invoice->creator->name }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <h2 class="jewel-section-title mb-4">Line Items</h2>
                <div class="overflow-x-auto">
                    <table class="jewel-table min-w-full text-sm">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-medium">LKR {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <dl class="w-full sm:w-72 space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Subtotal</dt>
                            <dd>LKR {{ number_format($invoice->subtotal, 2) }}</dd>
                        </div>
                        @if($invoice->making_charge > 0)
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Making Charge</dt>
                                <dd>LKR {{ number_format($invoice->making_charge, 2) }}</dd>
                            </div>
                        @endif
                        @if($invoice->tax > 0)
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Tax</dt>
                                <dd>LKR {{ number_format($invoice->tax, 2) }}</dd>
                            </div>
                        @endif
                        @if($invoice->discount > 0)
                            <div class="flex justify-between gap-4 text-emerald-700">
                                <dt>Discount</dt>
                                <dd>− LKR {{ number_format($invoice->discount, 2) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4 pt-2 border-t border-jewel-gold/10 font-semibold text-base">
                            <dt>Grand Total</dt>
                            <dd class="text-jewel-gold-dark">LKR {{ number_format($invoice->grand_total, 2) }}</dd>
                        </div>
                        @if($invoice->isIssued())
                            <div class="flex justify-between gap-4 text-gray-500">
                                <dt>Amount Paid</dt>
                                <dd>LKR {{ number_format($invoice->amount_paid, 2) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 font-medium">
                                <dt>Balance Due</dt>
                                <dd>LKR {{ number_format($invoice->balance_due, 2) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($invoice->notes)
                    <div class="mt-6 pt-6 border-t border-jewel-gold/10">
                        <h3 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </section>
        </div>

        <div class="space-y-6">
            @can('permission', 'billing.manage')
            <section class="jewel-card jewel-card-body sticky top-24 space-y-3">
                <h2 class="jewel-section-title mb-2">Actions</h2>

                @if($invoice->isEditable())
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="jewel-btn-outline w-full text-center block">Edit Draft</a>

                    <form method="POST" action="{{ route('admin.invoices.issue', $invoice) }}"
                        onsubmit="return confirm('Issue this invoice? The customer will be able to view it and it can no longer be edited.')">
                        @csrf
                        <button type="submit" class="jewel-btn w-full">Issue Invoice</button>
                    </form>
                @endif

                @if($invoice->isIssued())
                    <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="jewel-btn-outline w-full text-center block">Print / PDF</a>
                @endif

                @if($invoice->invoice_status !== \App\Enums\InvoiceStatus::Cancelled && ! in_array($invoice->invoice_status, [\App\Enums\InvoiceStatus::Paid, \App\Enums\InvoiceStatus::Partial]))
                    <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}"
                        onsubmit="return confirm('Cancel this invoice?')">
                        @csrf
                        <button type="submit" class="jewel-btn-danger w-full">Cancel Invoice</button>
                    </form>
                @endif
            </section>
            @endcan

            <section class="jewel-card jewel-card-body text-sm">
                <h2 class="jewel-section-title mb-4">Order Summary</h2>
                <dl class="space-y-2">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-400">Order</dt>
                        <dd><a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-jewel-gold-dark hover:text-jewel-gold">{{ $invoice->order->order_number }}</a></dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-400">Status</dt>
                        <dd><x-order-status-badge :status="$invoice->order->status" /></dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-400">Item</dt>
                        <dd class="text-right">{{ $invoice->order->item_type_label }}</dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</x-admin-layout>
