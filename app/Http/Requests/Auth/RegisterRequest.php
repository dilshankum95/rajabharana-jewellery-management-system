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
        $this->trimStrings(['name', 'email', 'phone']);
    }

    public function rules(): array
    {
        return [
            'name' => ValidationRules::personName(),
            'email' => ValidationRules::uniqueEmail(),
            'phone' => ValidationRules::phone(),
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
