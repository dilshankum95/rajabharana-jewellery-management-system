<?php

namespace App\Http\Requests\Admin;

use App\Enums\AvailabilityStatus;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogDesignRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user()?->isAdminOrStaff() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['name', 'description'], ['description']);
    }

    public function rules(): array
    {
        return [
            'name' => ValidationRules::productName(),
            'category' => ['required', Rule::in(array_keys(config('jewellery.catalog_categories')))],
            'gold_quality' => ['required', Rule::in(array_keys(config('jewellery.catalog_gold_qualities')))],
            'weight_grams' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'description' => ValidationRules::longText(max: 2000),
            'selling_price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'availability_status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'name.regex' => 'The product name contains invalid characters.',
            'name.min' => 'The product name must be at least 2 characters.',
            'images.required' => 'Please upload at least one product image.',
            'images.min' => 'Please upload at least one product image.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'weight_grams.min' => 'Weight must be at least 0.01 grams.',
            'selling_price.min' => 'Selling price must be greater than zero.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
