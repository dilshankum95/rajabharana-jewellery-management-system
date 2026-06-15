<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\Rbac;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffUserRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'users.manage');
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'email', 'phone'], ['phone']);
    }

    public function rules(): array
    {
        $staffUser = $this->route('user');

        return [
            'name' => ValidationRules::personName(),
            'email' => ValidationRules::email(uniqueIgnoreUserId: $staffUser->id),
            'phone' => ValidationRules::phone(required: false),
            'role' => ['required', Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::assignableRoles()))],
            'password' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $staffUser = $this->route('user');
            $actor = $this->user();

            if ($staffUser->id === $actor->id && $this->input('role') !== UserRole::Admin->value && $actor->isAdmin()) {
                $adminCount = \App\Models\User::where('role', UserRole::Admin)->where('id', '!=', $staffUser->id)->count();
                if ($adminCount === 0) {
                    $validator->errors()->add('role', 'You cannot remove the last administrator role from yourself.');
                }
            }

            if ($staffUser->id !== $actor->id && $staffUser->role === UserRole::Admin && $this->input('role') !== UserRole::Admin->value) {
                $adminCount = \App\Models\User::where('role', UserRole::Admin)->where('id', '!=', $staffUser->id)->count();
                if ($adminCount === 0) {
                    $validator->errors()->add('role', 'At least one administrator account must remain in the system.');
                }
            }
        });
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
