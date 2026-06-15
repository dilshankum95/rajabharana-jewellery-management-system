<?php

namespace App\Http\Requests;

use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    protected $errorBag = 'userDeletion';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'password' => ValidationRules::currentPassword(),
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
