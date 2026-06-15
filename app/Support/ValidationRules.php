<?php

namespace App\Support;

use App\Models\User;
use App\Rules\ValidPhone;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ValidationRules
{
    public const NO_HTML = 'not_regex:/<[^>]+>/';

    public const SEARCH_MAX = 100;

    public static function personName(bool $required = true): array
    {
        $rules = [
            'string',
            'min:2',
            'max:255',
            'regex:/^[\p{L}\s\'\-\.]+$/u',
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function email(bool $required = true, ?int $uniqueIgnoreUserId = null): array
    {
        $rules = ['string', 'lowercase', 'email:filter', 'max:255'];

        if ($uniqueIgnoreUserId !== null) {
            $rules[] = Rule::unique(User::class)->ignore($uniqueIgnoreUserId);
        }

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function uniqueEmail(): array
    {
        return array_merge(self::email(), [Rule::unique(User::class)]);
    }

    public static function phone(bool $required = true): array
    {
        $rules = ['string', 'max:25', new ValidPhone];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function address(bool $required = false): array
    {
        $rules = [
            'string',
            'min:5',
            'max:500',
            'regex:/^[\p{L}\p{N}\s\'\-\.\,\#\/\(\)]+$/u',
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function city(bool $required = false): array
    {
        $rules = [
            'string',
            'min:2',
            'max:100',
            'regex:/^[\p{L}\s\'\-\.]+$/u',
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function productName(bool $required = true): array
    {
        $rules = [
            'string',
            'min:2',
            'max:255',
            'regex:/^[\p{L}\p{N}\s\'\-\&\.\,\(\)]+$/u',
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function shortText(bool $required = false, int $max = 100): array
    {
        $rules = [
            'string',
            'max:'.$max,
            'regex:/^[\p{L}\p{N}\s\'\-\.\,\/\(\)\"]+$/u',
            self::NO_HTML,
        ];

        if (! $required) {
            array_unshift($rules, 'nullable');
        } else {
            array_unshift($rules, 'required', 'min:1');
        }

        return $rules;
    }

    public static function longText(bool $required = false, int $max = 2000): array
    {
        $rules = [
            'string',
            'max:'.$max,
            self::NO_HTML,
        ];

        if ($required) {
            array_unshift($rules, 'required', 'min:3');
        } else {
            array_unshift($rules, 'nullable', 'min:3');
        }

        return $rules;
    }

    public static function pieceName(bool $required = false): array
    {
        return self::productName($required);
    }

    public static function jewellerySize(bool $required = false): array
    {
        $rules = [
            'string',
            'max:100',
            'regex:/^[\p{L}\p{N}\s\'\-\.\,\/#\"]+$/u',
            self::NO_HTML,
        ];

        if (! $required) {
            array_unshift($rules, 'nullable', 'min:1');
        } else {
            array_unshift($rules, 'required', 'min:1');
        }

        return $rules;
    }

    public static function gemstoneName(bool $required = false): array
    {
        $rules = [
            'string',
            'min:2',
            'max:100',
            'regex:/^[\p{L}\s\-]+$/u',
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function orderNotes(bool $required = false, int $max = 2000): array
    {
        $rules = [
            'string',
            'max:'.$max,
            'regex:/^[\p{L}\p{N}\s\'\-\.\,\#\/\(\)\:\"]+$/u',
            self::NO_HTML,
        ];

        if ($required) {
            array_unshift($rules, 'required', 'min:3');
        } else {
            array_unshift($rules, 'nullable', 'min:3');
        }

        return $rules;
    }

    public static function password(bool $confirmed = true): array
    {
        $rules = ['required', 'string', 'max:255', Password::defaults()];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    public static function currentPassword(): array
    {
        return ['required', 'string', 'max:255', 'current_password'];
    }

    public static function money(bool $required = true): array
    {
        $rules = ['numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function metalPrice(bool $required = true): array
    {
        $rules = ['numeric', 'decimal:0,2', 'min:0.01', 'max:9999999.99'];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function weight(bool $required = false): array
    {
        $rules = ['numeric', 'decimal:0,2', 'min:0.01', 'max:99999'];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function quantity(bool $required = true): array
    {
        $rules = ['integer', 'min:1', 'max:50'];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function imageFile(bool $required = false, int $maxKb = 5120): array
    {
        $rules = ['image', 'mimes:jpeg,jpg,png,webp', 'max:'.$maxKb];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function profilePhoto(): array
    {
        return self::imageFile(required: false, maxKb: 2048);
    }

    public static function searchQuery(bool $required = false): array
    {
        $rules = ['string', 'max:'.self::SEARCH_MAX, self::NO_HTML];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function deliveryDate(bool $required = true, ?string $minDate = null, ?string $maxDate = null): array
    {
        $rules = ['date'];

        if ($minDate !== null) {
            $rules[] = 'after_or_equal:'.$minDate;
        } else {
            $rules[] = 'after:today';
        }

        if ($maxDate !== null) {
            $rules[] = 'before_or_equal:'.$maxDate;
        } else {
            $rules[] = 'before:'.now()->addYear()->format('Y-m-d');
        }

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function rememberMe(): array
    {
        return ['nullable', 'boolean'];
    }

    public static function catalogItemMessages(): array
    {
        return array_merge(self::messages(), [
            'name.required' => 'Item name is required.',
            'name.regex' => 'Item name may only contain letters, numbers, spaces, and basic punctuation.',
            'name.min' => 'Item name must be at least 2 characters.',
            'category.required' => 'Please select a category.',
            'category.in' => 'Please select a valid category.',
            'gold_quality.required' => 'Please select a gold quality.',
            'gold_quality.in' => 'Please select a valid gold quality.',
            'weight_grams.required' => 'Weight is required.',
            'weight_grams.min' => 'Weight must be at least 0.01 grams.',
            'weight_grams.max' => 'Weight cannot exceed 99,999 grams.',
            'weight_grams.decimal' => 'Weight must be a valid number with up to 2 decimal places.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.min' => 'Selling price must be greater than zero.',
            'selling_price.max' => 'Selling price is too large.',
            'selling_price.decimal' => 'Selling price must be a valid amount with up to 2 decimal places.',
            'availability_status.required' => 'Please select an availability status.',
            'description.min' => 'Description must be at least 3 characters when provided.',
            'description.regex' => 'Description contains invalid characters.',
            'images.max' => 'You can upload a maximum of 10 images per item.',
            'images.*.image' => 'Each upload must be a valid image file.',
            'images.*.mimes' => 'Images must be JPG, PNG, or WebP.',
            'images.*.max' => 'Each image must not exceed 5MB.',
        ]);
    }

    public static function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'name.min' => 'The name must be at least 2 characters.',
            'phone.max' => 'The phone number may not exceed 25 characters.',
            'address.regex' => 'The address contains invalid characters.',
            'address.min' => 'The address must be at least 5 characters.',
            'city.regex' => 'The city may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'city.min' => 'The city must be at least 2 characters.',
            'size.regex' => 'Size may only contain letters, numbers, and common size symbols (e.g. 7, 18 inch, 2.5 cm).',
            'size.min' => 'Please enter a valid size.',
            'gemstone_type.regex' => 'Gemstone type may only contain letters, spaces, and hyphens.',
            'gemstone_type.min' => 'Gemstone type must be at least 2 characters when provided.',
            'item_name.regex' => 'The piece name contains invalid characters.',
            'specifications.regex' => 'Specifications contain invalid characters.',
            'special_instructions.regex' => 'Special instructions contain invalid characters.',
            'gemstone_details.regex' => 'Gemstone details contain invalid characters.',
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'delivery_address.required' => 'Delivery address is required.',
            'contact_phone.required' => 'Contact phone number is required.',
            'specifications.min' => 'Specifications must be at least 3 characters when provided.',
            'special_instructions.min' => 'Special instructions must be at least 3 characters when provided.',
            'gemstone_details.min' => 'Gemstone details must be at least 3 characters when provided.',
            'description.min' => 'The description must be at least 3 characters when provided.',
            'admin_notes.min' => 'Notes must be at least 3 characters when provided.',
            'search.max' => 'Search text may not exceed '.self::SEARCH_MAX.' characters.',
            '*.not_regex' => 'HTML tags are not allowed in this field.',
            'email.email' => 'Please enter a valid email address.',
            'password.confirmed' => 'The password confirmation does not match.',
            'current_password.current_password' => 'The password is incorrect.',
            '*.decimal' => 'Please enter a valid amount with up to 2 decimal places.',
            '*.mimes' => 'Please upload a JPG, PNG, or WebP image.',
            '*.max' => 'The uploaded file is too large.',
        ];
    }

    public static function attributes(): array
    {
        return [
            'catalog_design_id' => 'catalog design',
            'reference_image' => 'reference image',
            'design_type' => 'design type',
            'item_type' => 'item type',
            'item_name' => 'piece name',
            'gold_quality' => 'gold quality',
            'gemstone_type' => 'gemstone type',
            'gemstone_details' => 'gemstone details',
            'expected_delivery_date' => 'expected delivery date',
            'contact_phone' => 'contact phone',
            'delivery_address' => 'delivery address',
            'special_instructions' => 'special instructions',
            'estimated_price' => 'order price',
            'admin_notes' => 'internal notes',
            'weight_grams' => 'weight',
            'selling_price' => 'selling price',
            'availability_status' => 'availability status',
            'gold_price_per_gram' => 'gold price per gram',
            'silver_price_per_gram' => 'silver price per gram',
            'current_password' => 'current password',
            'password_confirmation' => 'password confirmation',
            'profile_photo' => 'profile photo',
            'category' => 'category',
            'status' => 'status',
            'name' => 'item name',
            'description' => 'description',
            'images' => 'product images',
            'images.*' => 'product image',
        ];
    }
}
