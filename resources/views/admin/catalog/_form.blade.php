@props(['design' => null, 'categories', 'goldQualities', 'availabilityStatuses'])

@php
    $requiresImages = ! $design || $design->images->isEmpty();
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    @if($design)
        <div>
            <label class="jewel-label">Item Code</label>
            <input type="text" value="{{ $design->code }}" readonly
                class="jewel-input mt-1.5 bg-stone-100 text-stone-500 cursor-not-allowed">
            <p class="mt-1 text-xs text-stone-400">Auto-generated and cannot be changed</p>
        </div>
    @else
        <div class="sm:col-span-2">
            <p class="text-sm text-stone-500 bg-stone-50 rounded-xl px-4 py-3 border border-stone-200">
                Item code will be auto-generated when you save (e.g. RJ-20250608-A3XK)
            </p>
        </div>
    @endif

    <div class="{{ $design ? '' : 'sm:col-span-1' }}">
        <label for="name" class="jewel-label">Item Name *</label>
        <input id="name" name="name" type="text" required minlength="2" maxlength="255"
            value="{{ old('name', $design?->name) }}"
            placeholder="e.g. Classic Temple Ring"
            class="jewel-input mt-1.5">
        <x-form-hint>Letters, numbers, spaces, and basic punctuation only. Min 2 characters.</x-form-hint>
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="category" class="jewel-label">Category *</label>
        <select id="category" name="category" required class="jewel-input mt-1.5">
            <option value="">Select category</option>
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $design?->category) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category')" />
    </div>

    <div>
        <label for="gold_quality" class="jewel-label">Gold Quality *</label>
        <select id="gold_quality" name="gold_quality" required class="jewel-input mt-1.5">
            <option value="">Select gold quality</option>
            @foreach($goldQualities as $value => $label)
                <option value="{{ $value }}" @selected(old('gold_quality', $design?->gold_quality) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('gold_quality')" />
    </div>

    <div>
        <label for="weight_grams" class="jewel-label">Weight (grams) *</label>
        <input id="weight_grams" name="weight_grams" type="number" step="0.01" min="0.01" max="99999" required
            value="{{ old('weight_grams', $design?->weight_grams) }}"
            placeholder="e.g. 8.50"
            class="jewel-input mt-1.5">
        <x-form-hint>Numeric only. 0.01 to 99,999 grams.</x-form-hint>
        <x-input-error :messages="$errors->get('weight_grams')" />
    </div>

    <div>
        <label for="selling_price" class="jewel-label">Selling Price (LKR) *</label>
        <input id="selling_price" name="selling_price" type="number" step="0.01" min="0.01" max="99999999.99" required
            value="{{ old('selling_price', $design?->selling_price) }}"
            placeholder="e.g. 125000.00"
            class="jewel-input mt-1.5">
        <x-form-hint>Numeric only. Must be greater than zero.</x-form-hint>
        <x-input-error :messages="$errors->get('selling_price')" />
    </div>

    <div>
        <label for="stock_quantity" class="jewel-label">Stock Quantity *</label>
        <input id="stock_quantity" name="stock_quantity" type="number" min="0" max="99999" required
            value="{{ old('stock_quantity', $design?->stock_quantity ?? 1) }}"
            placeholder="e.g. 5"
            class="jewel-input mt-1.5">
        <x-form-hint>Number of units available. Set to 0 to mark out of stock.</x-form-hint>
        <x-input-error :messages="$errors->get('stock_quantity')" />
    </div>

    <div>
        <label for="availability_status" class="jewel-label">Availability Status *</label>
        <select id="availability_status" name="availability_status" required class="jewel-input mt-1.5">
            @foreach($availabilityStatuses as $value => $label)
                <option value="{{ $value }}" @selected(old('availability_status', $design?->availability_status?->value ?? $design?->availability_status ?? 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('availability_status')" />
    </div>

    <div class="sm:col-span-2">
        <label for="description" class="jewel-label">Description (optional)</label>
        <textarea id="description" name="description" rows="3" maxlength="2000"
            placeholder="Describe the item, materials, and craftsmanship..."
            class="jewel-input mt-1.5">{{ old('description', $design?->description) }}</textarea>
        <x-form-hint>Letters, numbers, and basic punctuation. Min 3 characters if provided.</x-form-hint>
        <x-input-error :messages="$errors->get('description')" />
    </div>

    <div class="sm:col-span-2">
        <label for="images" class="jewel-label">
            Product Images {{ $requiresImages ? '*' : '(add more)' }}
        </label>
        <input id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple
            {{ $requiresImages ? 'required' : '' }}
            class="mt-1.5 jewel-file-input">
        <x-form-hint>
            @if($design && $design->images->isNotEmpty())
                This item has {{ $design->images->count() }} image(s). Upload up to {{ 10 - $design->images->count() }} more (10 total max).
                Leave empty to keep existing images.
            @else
                Upload 1 to 10 images (JPEG, PNG, WebP — max 5MB each).
            @endif
        </x-form-hint>
        <x-input-error :messages="$errors->get('images')" />
        <x-input-error :messages="$errors->get('images.*')" />
    </div>
</div>

@include('admin.catalog._materials', ['design' => $design ?? null, 'rawMaterials' => $rawMaterials ?? collect()])
