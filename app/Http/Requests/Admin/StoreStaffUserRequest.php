<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\Rbac;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffUserRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'users.manage');
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'email', 'phone']);
    }

    public function rules(): array
    {
        return [
            'name' => ValidationRules::personName(),
            'email' => ValidationRules::uniqueEmail(),
            'phone' => ValidationRules::phone(required: false),
            'role' => ['required', Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::assignableRoles()))],
            'password' => ValidationRules::password(),
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
