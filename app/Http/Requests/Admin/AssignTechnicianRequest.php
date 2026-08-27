<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'assigned_technician_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'technician'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_technician_id.exists' => 'Select a valid workshop technician.',
        ];
    }
}
