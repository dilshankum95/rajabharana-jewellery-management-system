<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Metal Prices</h1>
            <p class="jewel-page-subtitle">Update today's gold and silver price per gram</p>
        </div>
    </x-slot>

    <div class="max-w-xl">
        @if($metalPrice)
            <div class="jewel-card jewel-card-body mb-6 bg-jewel-cream/30">
                <p class="text-xs uppercase tracking-wider text-gray-400">Last updated</p>
                <p class="mt-1 text-sm text-jewel-dark font-medium">
                    {{ $metalPrice->price_date->format('M d, Y') }}
                    @if($metalPrice->updatedBy)
                        · by {{ $metalPrice->updatedBy->name }}
                    @endif
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.metal-prices.update') }}" class="jewel-card jewel-card-body space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="gold_price_per_gram" class="jewel-label">Gold Price (LKR per gram) *</label>
                <input id="gold_price_per_gram" name="gold_price_per_gram" type="number" step="0.01" min="0.01" max="9999999.99"
                    value="{{ old('gold_price_per_gram', $metalPrice?->gold_price_per_gram) }}"
                    required class="jewel-input mt-1.5" placeholder="e.g. 18500.00">
                <x-input-error :messages="$errors->get('gold_price_per_gram')" class="mt-2" />
            </div>

            <div>
                <label for="silver_price_per_gram" class="jewel-label">Silver Price (LKR per gram) *</label>
                <input id="silver_price_per_gram" name="silver_price_per_gram" type="number" step="0.01" min="0.01" max="9999999.99"
                    value="{{ old('silver_price_per_gram', $metalPrice?->silver_price_per_gram) }}"
                    required class="jewel-input mt-1.5" placeholder="e.g. 350.00">
                <x-input-error :messages="$errors->get('silver_price_per_gram')" class="mt-2" />
            </div>

            <p class="text-xs text-gray-500">
                Prices are dated {{ now()->format('M d, Y') }} and shown on the customer dashboard.
            </p>

            <div class="pt-2">
                <button type="submit" class="jewel-btn">Update Prices</button>
            </div>
        </form>
    </div>
</x-admin-layout>
