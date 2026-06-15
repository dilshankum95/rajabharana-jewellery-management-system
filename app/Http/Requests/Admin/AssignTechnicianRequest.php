<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'production.assign');
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
