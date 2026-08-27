@props(['material' => null, 'materialTypes', 'stockUnits'])

<div class="grid sm:grid-cols-2 gap-5">
    @if($material)
        <div>
            <label class="jewel-label">Material Code</label>
            <input type="text" value="{{ $material->code }}" readonly
                class="jewel-input mt-1.5 bg-stone-100 text-stone-500 cursor-not-allowed">
        </div>
    @else
        <div class="sm:col-span-2">
            <p class="text-sm text-stone-500 bg-stone-50 rounded-xl px-4 py-3 border border-stone-200">
                Material code will be auto-generated when you save (e.g. RM-20250818-A3XK)
            </p>
        </div>
    @endif

    <div>
        <label for="name" class="jewel-label">Material Name *</label>
        <input id="name" name="name" type="text" required minlength="2" maxlength="255"
            value="{{ old('name', $material?->name) }}"
            placeholder="e.g. 22K Gold Wire"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="material_type" class="jewel-label">Material Type *</label>
        <select id="material_type" name="material_type" required class="jewel-input mt-1.5">
            <option value="">Select type</option>
            @foreach($materialTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('material_type', $material?->material_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('material_type')" />
    </div>

    <div>
        <label for="unit" class="jewel-label">Unit *</label>
        <select id="unit" name="unit" required class="jewel-input mt-1.5">
            <option value="">Select unit</option>
            @foreach($stockUnits as $value => $label)
                <option value="{{ $value }}" @selected(old('unit', $material?->unit) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('unit')" />
    </div>

    <div>
        <label for="stock_quantity" class="jewel-label">Current Stock *</label>
        <input id="stock_quantity" name="stock_quantity" type="number" step="0.001" min="0" max="9999999" required
            value="{{ old('stock_quantity', $material?->stock_quantity ?? 0) }}"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('stock_quantity')" />
    </div>

    <div>
        <label for="reorder_level" class="jewel-label">Reorder Level</label>
        <input id="reorder_level" name="reorder_level" type="number" step="0.001" min="0" max="9999999"
            value="{{ old('reorder_level', $material?->reorder_level) }}"
            placeholder="Alert when stock falls to this level"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('reorder_level')" />
    </div>

    <div>
        <label for="unit_cost" class="jewel-label">Unit Cost (LKR)</label>
        <input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" max="99999999.99"
            value="{{ old('unit_cost', $material?->unit_cost) }}"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('unit_cost')" />
    </div>

    <div>
        <label for="is_active" class="jewel-label">Status</label>
        <select id="is_active" name="is_active" class="jewel-input mt-1.5">
            <option value="1" @selected(old('is_active', $material?->is_active ?? true) == true)>Active</option>
            <option value="0" @selected(old('is_active', $material?->is_active ?? true) == false)>Inactive</option>
        </select>
    </div>

    <div class="sm:col-span-2">
        <label for="notes" class="jewel-label">Notes</label>
        <textarea id="notes" name="notes" rows="3" maxlength="2000"
            placeholder="Supplier, storage location, usage notes..."
            class="jewel-input mt-1.5">{{ old('notes', $material?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" />
    </div>
</div>
