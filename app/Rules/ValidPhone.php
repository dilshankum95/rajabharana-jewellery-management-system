<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid phone number.');

            return;
        }

        if (! preg_match('/^[\+]?[0-9\s\-().]{7,25}$/', $value)) {
            $fail('The :attribute format is invalid. Use digits with optional +, spaces, or hyphens.');

            return;
        }

        $digits = preg_replace('/\D/', '', $value);

        if ($digits === null || strlen($digits) < 7 || strlen($digits) > 15) {
            $fail('The :attribute must contain 7 to 15 digits.');
        }
    }
}
