<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'email', 'phone', 'address', 'city'], ['address', 'city']);
    }

    public function rules(): array
    {
        return [
            'name' => ValidationRules::personName(),
            'email' => ValidationRules::email(uniqueIgnoreUserId: $this->user()->id),
            'phone' => ValidationRules::phone(required: true),
            'address' => ValidationRules::address(required: true),
            'city' => ValidationRules::city(required: true),
            'profile_photo' => ValidationRules::profilePhoto(),
            'remove_profile_photo' => ['nullable', 'boolean'],
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
