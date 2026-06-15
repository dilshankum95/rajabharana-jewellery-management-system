<?php

namespace App\Http\Requests\Admin;

use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('production.view') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search') && is_string($this->search)) {
            $this->merge(['search' => trim($this->search)]);
        }
    }

    public function rules(): array
    {
        $productionStatuses = [
            'confirmed',
            'in_production',
            'quality_check',
            'ready',
        ];

        return [
            'search' => ValidationRules::searchQuery(),
            'status' => ['nullable', 'string', Rule::in($productionStatuses)],
            'technician_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'technician')],
            'unassigned' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
