<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesCatalogMaterials
{
    protected function filterEmptyCatalogMaterialRows(): void
    {
        $materials = $this->input('materials');

        if (! is_array($materials)) {
            return;
        }

        $filtered = array_values(array_filter($materials, function ($row) {
            if (! is_array($row)) {
                return false;
            }

            return filled($row['raw_material_id'] ?? null) || filled($row['quantity_required'] ?? null);
        }));

        $this->merge([
            'materials' => $filtered === [] ? null : $filtered,
        ]);
    }

    /** @return array<string, mixed> */
    protected function catalogMaterialRules(): array
    {
        return [
            'materials' => ['nullable', 'array', 'max:20'],
            'materials.*.raw_material_id' => [
                'nullable',
                'integer',
                Rule::exists('raw_materials', 'id')->where('is_active', true),
            ],
            'materials.*.quantity_required' => [
                'nullable',
                'numeric',
                'min:0.001',
                'max:9999999',
            ],
        ];
    }

    protected function validateCatalogMaterialRows(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('materials', []) as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $hasId = filled($row['raw_material_id'] ?? null);
                $hasQty = filled($row['quantity_required'] ?? null);

                if ($hasId && ! $hasQty) {
                    $validator->errors()->add(
                        "materials.{$index}.quantity_required",
                        'Please enter the quantity required for each linked material.'
                    );
                }

                if ($hasQty && ! $hasId) {
                    $validator->errors()->add(
                        "materials.{$index}.raw_material_id",
                        'Please select a raw material for each quantity entered.'
                    );
                }
            }
        });
    }
}
