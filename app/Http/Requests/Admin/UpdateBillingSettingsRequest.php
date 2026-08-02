<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'billing.settings');
    }

    public function rules(): array
    {
        $categories = array_keys(config('jewellery.catalog_categories', []));

        $rules = [
            'tax_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'category_discounts' => ['required', 'array'],
        ];

        foreach ($categories as $code) {
            $rules['category_discounts.'.$code] = ['required', 'numeric', 'min:0', 'max:100'];
        }

        return $rules;
    }
}
