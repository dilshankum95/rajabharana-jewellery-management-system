<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityStatus;
use App\Enums\DesignType;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings([
            'item_name',
            'size',
            'specifications',
            'gemstone_type',
            'gemstone_details',
            'special_instructions',
            'contact_phone',
            'delivery_address',
        ], [
            'item_name',
            'size',
            'specifications',
            'gemstone_type',
            'gemstone_details',
            'special_instructions',
            'delivery_address',
        ]);
    }

    public function rules(): array
    {
        $designType = $this->input('design_type');

        return [
            'design_type' => ['required', Rule::enum(DesignType::class)],
            'catalog_design_id' => [
                Rule::requiredIf($designType === DesignType::Catalog->value),
                'nullable',
                'integer',
                Rule::exists('catalog_designs', 'id')->where(
                    'availability_status',
                    AvailabilityStatus::Available->value
                ),
            ],
            'reference_image' => [
                Rule::requiredIf($designType === DesignType::Custom->value),
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],
            'item_type' => ['required', Rule::in(array_keys(config('jewellery.item_types')))],
            'item_name' => ValidationRules::shortText(max: 255),
            'size' => ValidationRules::shortText(max: 100),
            'weight_grams' => ['nullable', 'numeric', 'min:0.01', 'max:99999'],
            'specifications' => ValidationRules::longText(),
            'gold_quality' => ['required', Rule::in(array_keys(config('jewellery.gold_qualities')))],
            'gemstone_type' => ValidationRules::shortText(max: 100),
            'gemstone_details' => ValidationRules::longText(max: 1000),
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'special_instructions' => ValidationRules::longText(),
            'expected_delivery_date' => [
                'required',
                'date',
                'after:today',
                'before:'.now()->addYear()->format('Y-m-d'),
            ],
            'contact_phone' => ValidationRules::phone(),
            'delivery_address' => ValidationRules::address(),
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'catalog_design_id.required' => 'Please select a design from our catalog.',
            'catalog_design_id.exists' => 'The selected catalog item is unavailable. Please choose another design.',
            'reference_image.required' => 'Please upload a reference image for your custom design.',
            'expected_delivery_date.after' => 'Expected delivery date must be at least one day from today.',
            'expected_delivery_date.before' => 'Expected delivery date cannot be more than one year from today.',
            'weight_grams.min' => 'Weight must be at least 0.01 grams.',
            'weight_grams.max' => 'Weight cannot exceed 99,999 grams.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 50.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
