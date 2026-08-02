<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">Invoice {{ $invoice->invoice_number }}</h1>
                <p class="jewel-page-subtitle">Order {{ $order->order_number }}</p>
            </div>
            <x-invoice-status-badge :status="$invoice->invoice_status" class="text-sm" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <section class="jewel-card jewel-card-body">
                <div class="grid sm:grid-cols-2 gap-6 mb-8 pb-6 border-b border-jewel-gold/10">
                    <div>
                        <h2 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Invoice Details</h2>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-400">Issue Date</dt>
                                <dd class="font-medium text-jewel-dark">{{ $invoice->issue_date->format('F d, Y') }}</dd>
                            </div>
                            @if($invoice->due_date)
                                <div>
                                    <dt class="text-gray-400">Due Date</dt>
                                    <dd class="font-medium text-jewel-dark">{{ $invoice->due_date->format('F d, Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                    <div>
                        <h2 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Order</h2>
                        <p class="text-sm font-medium text-jewel-dark">{{ $order->item_type_label }}</p>
                        @if($order->catalogDesign)
                            <p class="text-sm text-gray-500">{{ $order->catalogDesign->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto mb-6">
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

                <div class="flex justify-end">
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
                                <dt class="text-gray-500">Tax ({{ number_format($invoice->tax_rate_percent, 2) }}%)</dt>
                                <dd>LKR {{ number_format($invoice->tax, 2) }}</dd>
                            </div>
                        @endif
                        @if($invoice->discount > 0)
                            <div class="flex justify-between gap-4 text-emerald-700">
                                <dt>Discount ({{ number_format($invoice->discount_percent, 2) }}%)</dt>
                                <dd>− LKR {{ number_format($invoice->discount, 2) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4 pt-2 border-t border-jewel-gold/10 font-semibold text-lg">
                            <dt>Total</dt>
                            <dd class="text-jewel-gold-dark">LKR {{ number_format($invoice->grand_total, 2) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 text-gray-500">
                            <dt>Amount Paid</dt>
                            <dd>LKR {{ number_format($invoice->amount_paid, 2) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 font-medium">
                            <dt>Balance Due</dt>
                            <dd>LKR {{ number_format($invoice->balance_due, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                @if($invoice->notes)
                    <div class="mt-6 pt-6 border-t border-jewel-gold/10">
                        <h3 class="text-xs uppercase tracking-wider text-gray-400 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </section>

            @if($invoice->payments->isNotEmpty())
            <section class="jewel-card jewel-card-body mt-6">
                <h3 class="jewel-section-title mb-4">Payment History</h3>
                <div class="space-y-3">
                    @foreach($invoice->payments as $payment)
                        <div class="flex justify-between items-center text-sm border-b border-jewel-gold/10 pb-3 last:border-0 last:pb-0">
                            <div>
                                <p class="font-medium text-jewel-dark">{{ $payment->payment_date->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $payment->paymentMethod?->label ?? $payment->payment_method }}</p>
                            </div>
                            <p class="font-semibold text-emerald-700">LKR {{ number_format($payment->payment_amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <div class="mt-6">
                <a href="{{ route('orders.show', $order) }}" class="text-sm text-gray-500 hover:text-jewel-dark transition">
                    &larr; Back to Order {{ $order->order_number }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
