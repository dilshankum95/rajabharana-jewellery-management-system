<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'email', 'phone', 'address', 'city'], ['address', 'city']);
    }

    public function rules(): array
    {
        return [
            'name' => ValidationRules::personName(),
            'email' => ValidationRules::uniqueEmail(),
            'phone' => ValidationRules::phone(required: true),
            'address' => ValidationRules::address(required: true),
            'city' => ValidationRules::city(required: true),
            'password' => ValidationRules::password(),
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
