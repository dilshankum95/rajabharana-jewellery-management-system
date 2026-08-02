<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class FilterInvoicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'billing.view');
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
            'status' => ['nullable', 'string', 'in:draft,issued,partial,paid,cancelled,overdue'],
        ];
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }
}
