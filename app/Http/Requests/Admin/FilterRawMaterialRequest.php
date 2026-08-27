<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRawMaterialRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('raw-materials.view') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['search']);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'material_type' => ['nullable', Rule::in(array_keys(config('jewellery.raw_material_types')))],
            'low_stock' => ['nullable', 'boolean'],
        ];
    }
}
