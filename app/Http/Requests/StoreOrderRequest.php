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
            'specifications',
            'gemstone_type',
            'gemstone_details',
            'special_instructions',
            'weight_grams',
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
            'reference_image' => array_merge(
                ValidationRules::imageFile(required: false),
                [Rule::requiredIf($designType === DesignType::Custom->value)]
            ),
            'item_type' => ['required', Rule::in(array_keys(config('jewellery.item_types')))],
            'item_name' => ValidationRules::pieceName(),
            'size' => ValidationRules::jewellerySize(required: true),
            'weight_grams' => ValidationRules::weight(required: true),
            'specifications' => ValidationRules::orderNotes(max: 2000),
            'gold_quality' => ['required', Rule::in(array_keys(config('jewellery.gold_qualities')))],
            'gemstone_type' => ValidationRules::gemstoneName(),
            'gemstone_details' => ValidationRules::orderNotes(max: 1000),
            'quantity' => ValidationRules::quantity(),
            'special_instructions' => ValidationRules::orderNotes(max: 2000),
            'expected_delivery_date' => ValidationRules::deliveryDate(),
            'contact_phone' => ValidationRules::phone(required: true),
            'delivery_address' => ValidationRules::address(required: true),
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
            'weight_grams.required' => 'Estimated weight is required.',
            'weight_grams.min' => 'Weight must be at least 0.01 grams.',
            'weight_grams.max' => 'Weight cannot exceed 99,999 grams.',
            'size.required' => 'Size is required (e.g. ring size, chain length).',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 50.',
            'delivery_address.required' => 'Delivery address is required.',
            'contact_phone.required' => 'Contact phone number is required.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
