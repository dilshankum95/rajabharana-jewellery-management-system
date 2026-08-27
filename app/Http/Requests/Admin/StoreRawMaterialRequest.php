<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRawMaterialRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('raw-materials.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'notes'], ['notes']);

        if ($this->has('is_active')) {
            $this->merge(['is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'material_type' => ['required', Rule::in(array_keys(config('jewellery.raw_material_types')))],
            'unit' => ['required', Rule::in(array_keys(config('jewellery.stock_units')))],
            'stock_quantity' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'reorder_level' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
