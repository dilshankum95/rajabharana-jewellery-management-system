<?php

namespace App\Http\Requests\Admin;

use App\Enums\AvailabilityStatus;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCatalogDesignRequest extends FormRequest
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
        $catalog = $this->route('catalog');
        $hasExistingImages = $catalog && $catalog->images()->exists();

        return [
            'name' => ValidationRules::productName(),
            'category' => ['required', Rule::in(array_keys(config('jewellery.catalog_categories')))],
            'gold_quality' => ['required', Rule::in(array_keys(config('jewellery.catalog_gold_qualities')))],
            'weight_grams' => ValidationRules::weight(required: true),
            'description' => ValidationRules::orderNotes(max: 2000),
            'selling_price' => ValidationRules::money(),
            'availability_status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'images' => [$hasExistingImages ? 'nullable' : 'required', 'array', 'max:10'],
            'images.*' => ValidationRules::imageFile(required: true),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $catalog = $this->route('catalog');

            if (! $catalog) {
                return;
            }

            $newImages = $this->file('images') ?? [];
            $newCount = is_array($newImages) ? count($newImages) : 0;
            $existingCount = $catalog->images()->count();

            if ($existingCount === 0 && $newCount === 0) {
                $validator->errors()->add('images', 'Please upload at least one product image.');

                return;
            }

            if ($existingCount + $newCount > 10) {
                $validator->errors()->add(
                    'images',
                    "This item already has {$existingCount} image(s). You can upload at most ".(10 - $existingCount).' more (10 total).'
                );
            }
        });
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::catalogItemMessages(), [
            'images.required' => 'The item must have at least one product image.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
