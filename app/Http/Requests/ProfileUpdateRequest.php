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
            'phone' => ValidationRules::phone(),
            'address' => ValidationRules::address(),
            'city' => ValidationRules::city(),
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
