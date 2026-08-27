@props(['design' => null, 'rawMaterials'])

@php
    $materialRows = old('materials');

    if ($materialRows === null && $design) {
        $materialRows = $design->rawMaterials->map(fn ($m) => [
            'raw_material_id' => (string) $m->id,
            'quantity_required' => (string) $m->pivot->quantity_required,
        ])->values()->all();
    }

    if (empty($materialRows)) {
        $materialRows = [['raw_material_id' => '', 'quantity_required' => '']];
    }
@endphp

<div class="mt-4 pt-6 border-t border-stone-100">
    <div class="mb-4">
        <h3 class="jewel-section-title">Linked Raw Materials</h3>
        <p class="text-sm text-stone-500 mt-1">Materials used per 1 unit of this catalog item. Deducted automatically when an order is accepted.</p>
    </div>

    <div class="space-y-3" id="material-rows">
        @foreach($materialRows as $index => $row)
            <div class="material-row grid sm:grid-cols-[1fr_140px] gap-3 items-start p-4 rounded-xl border border-stone-200 bg-stone-50/50">
                <div>
                    <label class="jewel-label text-xs">Raw Material</label>
                    <select name="materials[{{ $index }}][raw_material_id]" class="jewel-input mt-1">
                        <option value="">Select material</option>
                        @foreach($rawMaterials as $material)
                            <option value="{{ $material->id }}" @selected((string) ($row['raw_material_id'] ?? '') === (string) $material->id)>
                                {{ $material->name }} ({{ number_format($material->stock_quantity, 3) }} {{ $material->unit_label }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="jewel-label text-xs">Qty per Unit</label>
                    <input type="number" step="0.001" min="0.001" max="9999999"
                        name="materials[{{ $index }}][quantity_required]"
                        value="{{ $row['quantity_required'] ?? '' }}"
                        placeholder="e.g. 8.5"
                        class="jewel-input mt-1">
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-material-row" class="mt-3 jewel-btn-outline text-sm px-4 py-2">+ Add Material</button>

    <x-input-error :messages="$errors->get('materials')" />
    <x-input-error :messages="$errors->get('materials.*.raw_material_id')" />
    <x-input-error :messages="$errors->get('materials.*.quantity_required')" />
</div>

<template id="material-row-template">
    <div class="grid sm:grid-cols-[1fr_140px] gap-3 items-start p-4 rounded-xl border border-stone-200 bg-stone-50/50 material-row">
        <div>
            <label class="jewel-label text-xs">Raw Material</label>
            <select data-name="raw_material_id" class="jewel-input mt-1 material-select">
                <option value="">Select material</option>
                @foreach($rawMaterials as $material)
                    <option value="{{ $material->id }}">
                        {{ $material->name }} ({{ number_format($material->stock_quantity, 3) }} {{ $material->unit_label }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="jewel-label text-xs">Qty per Unit</label>
            <input type="number" step="0.001" min="0.001" max="9999999"
                data-name="quantity_required"
                placeholder="e.g. 8.5"
                class="jewel-input mt-1 material-qty">
        </div>
    </div>
</template>

<script>
    document.getElementById('add-material-row')?.addEventListener('click', function () {
        const container = document.getElementById('material-rows');
        const template = document.getElementById('material-row-template');
        if (!container || !template) return;

        const index = container.querySelectorAll('.material-row').length;
        const clone = template.content.firstElementChild.cloneNode(true);

        clone.querySelector('.material-select')?.setAttribute('name', `materials[${index}][raw_material_id]`);
        clone.querySelector('.material-qty')?.setAttribute('name', `materials[${index}][quantity_required]`);

        container.appendChild(clone);
    });
</script>
