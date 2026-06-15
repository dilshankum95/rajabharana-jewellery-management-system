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
        return $this->user()?->hasPermission('catalog.manage') ?? false;
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
            'weight_grams' => ValidationRules::weight(required: true),
            'description' => ValidationRules::orderNotes(max: 2000),
            'selling_price' => ValidationRules::money(),
            'availability_status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ValidationRules::imageFile(required: true),
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::catalogItemMessages(), [
            'images.required' => 'Please upload at least one product image.',
            'images.min' => 'Please upload at least one product image.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
