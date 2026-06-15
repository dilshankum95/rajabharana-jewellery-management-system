<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMetalPriceRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('metal-prices.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'gold_price_per_gram' => ValidationRules::metalPrice(),
            'silver_price_per_gram' => ValidationRules::metalPrice(),
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'gold_price_per_gram.required' => 'Gold price per gram is required.',
            'silver_price_per_gram.required' => 'Silver price per gram is required.',
            'gold_price_per_gram.min' => 'Gold price must be greater than zero.',
            'silver_price_per_gram.min' => 'Silver price must be greater than zero.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
