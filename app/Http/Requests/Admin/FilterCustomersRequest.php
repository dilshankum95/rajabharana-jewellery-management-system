<?php

namespace App\Http\Requests\Admin;

use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class FilterCustomersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('customers.view') ?? false;
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
        ];
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }
}
