<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdjustRawMaterialStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('raw-materials.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'quantity_delta' => ['required', 'numeric', 'not_in:0', 'min:-9999999', 'max:9999999'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity_delta.not_in' => 'Adjustment amount cannot be zero.',
        ];
    }
}
