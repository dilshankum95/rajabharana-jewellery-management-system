<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to invoice</a>
                <h1 class="jewel-page-title text-xl mt-1">Edit Draft — {{ $invoice->invoice_number }}</h1>
                <p class="jewel-page-subtitle">Order {{ $invoice->order->order_number }}</p>
            </div>
            <x-invoice-status-badge :status="$invoice->invoice_status" class="text-sm" />
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="jewel-card jewel-card-body">
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
                        <tfoot>
                            <tr class="border-t border-jewel-gold/10">
                                <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-600">Subtotal</td>
                                <td class="px-4 py-3 text-right font-semibold">LKR {{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="mt-3 text-xs text-gray-400">Line items are generated from the order and cannot be edited here.</p>
            </section>
        </div>

        <div>
            <section class="jewel-card jewel-card-body sticky top-24">
                <h2 class="jewel-section-title mb-4">Charges &amp; Dates</h2>

                <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="making_charge" class="jewel-label">Making Charge (LKR)</label>
                        <input id="making_charge" name="making_charge" type="number" step="0.01" min="0" max="99999999.99" required
                            value="{{ old('making_charge', $invoice->making_charge) }}"
                            class="jewel-input mt-1.5">
                        <x-input-error :messages="$errors->get('making_charge')" class="mt-1" />
                    </div>

                    <div>
                        <label for="tax" class="jewel-label">Tax (LKR)</label>
                        <input id="tax" name="tax" type="number" step="0.01" min="0" max="99999999.99" required
                            value="{{ old('tax', $invoice->tax) }}"
                            class="jewel-input mt-1.5">
                        <x-input-error :messages="$errors->get('tax')" class="mt-1" />
                    </div>

                    <div>
                        <label for="discount" class="jewel-label">Discount (LKR)</label>
                        <input id="discount" name="discount" type="number" step="0.01" min="0" max="99999999.99" required
                            value="{{ old('discount', $invoice->discount) }}"
                            class="jewel-input mt-1.5">
                        <x-input-error :messages="$errors->get('discount')" class="mt-1" />
                    </div>

                    <div>
                        <label for="due_date" class="jewel-label">Due Date</label>
                        <input id="due_date" name="due_date" type="date" required
                            value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}"
                            min="{{ today()->format('Y-m-d') }}"
                            class="jewel-input mt-1.5">
                        <x-input-error :messages="$errors->get('due_date')" class="mt-1" />
                    </div>

                    <div>
                        <label for="notes" class="jewel-label">Notes (optional)</label>
                        <textarea id="notes" name="notes" rows="3" maxlength="2000"
                            placeholder="Payment terms or notes for the customer..."
                            class="jewel-input mt-1.5">{{ old('notes', $invoice->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>

                    <div class="pt-3 border-t border-jewel-gold/10">
                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-gray-500">Grand Total</span>
                            <span class="font-display text-xl font-semibold text-jewel-gold-dark">
                                LKR {{ number_format($invoice->grand_total, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mb-4">Total updates after you save changes.</p>
                        <button type="submit" class="jewel-btn w-full">Save Draft</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-admin-layout>
