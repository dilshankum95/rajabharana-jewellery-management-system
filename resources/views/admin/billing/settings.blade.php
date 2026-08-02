<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Billing Settings</h1>
            <p class="jewel-page-subtitle">Configure tax rate and category discounts applied to invoices</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.billing.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PATCH')

        <section class="jewel-card jewel-card-body">
            <h2 class="jewel-section-title mb-4">Tax Rate</h2>
            <p class="text-sm text-slate-500 mb-4">Tax is calculated automatically on each invoice as a percentage of (subtotal + making charge − discount).</p>

            <div class="max-w-xs">
                <label for="tax_rate_percent" class="jewel-label">Tax Rate (%) *</label>
                <input id="tax_rate_percent" name="tax_rate_percent" type="number" step="0.01" min="0" max="100" required
                    value="{{ old('tax_rate_percent', $billingSetting->tax_rate_percent) }}"
                    class="jewel-input mt-1.5">
                <x-input-error :messages="$errors->get('tax_rate_percent')" class="mt-1" />
            </div>

            @if($billingSetting->updatedBy)
                <p class="mt-3 text-xs text-gray-400">Last updated by {{ $billingSetting->updatedBy->name }} · {{ $billingSetting->updated_at->format('M d, Y h:i A') }}</p>
            @endif
        </section>

        <section class="jewel-card jewel-card-body">
            <h2 class="jewel-section-title mb-4">Category Discounts</h2>
            <p class="text-sm text-slate-500 mb-4">Set a discount percentage for each product category. It is applied automatically when creating or editing draft invoices.</p>

            <div class="overflow-x-auto">
                <table class="jewel-table min-w-full text-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="w-40">Discount (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $code => $label)
                            @php
                                $current = $categoryDiscounts->get($code);
                                $value = old('category_discounts.'.$code, $current?->discount_percent ?? 0);
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $label }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="category_discounts[{{ $code }}]" step="0.01" min="0" max="100" required
                                        value="{{ $value }}"
                                        class="jewel-input w-full">
                                    <x-input-error :messages="$errors->get('category_discounts.'.$code)" class="mt-1" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            <button type="submit" class="jewel-btn">Save Billing Settings</button>
        </div>
    </form>
</x-admin-layout>
