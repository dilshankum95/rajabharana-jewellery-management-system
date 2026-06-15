<?php

namespace App\Http\Requests;

use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search') && is_string($this->search)) {
            $this->merge(['search' => trim($this->search)]);
        }
    }

    public function rules(): array
    {
        return [
            'search' => ValidationRules::searchQuery(),
            'category' => ['nullable', 'string', Rule::in(array_keys(config('jewellery.catalog_categories')))],
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
