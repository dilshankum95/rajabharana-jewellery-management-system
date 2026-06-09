<?php

namespace App\Support;

use App\Models\User;
use App\Rules\ValidPhone;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ValidationRules
{
    public const NO_HTML = 'not_regex:/<[^>]+>/';

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
            'min:3',
            'max:'.$max,
            self::NO_HTML,
        ];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    public static function password(bool $confirmed = true): array
    {
        $rules = ['required', 'string', Password::defaults()];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
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
            'item_name.regex' => 'The piece name contains invalid characters.',
            'size.regex' => 'The size contains invalid characters.',
            'gemstone_type.regex' => 'The gemstone type contains invalid characters.',
            'specifications.min' => 'Specifications must be at least 3 characters when provided.',
            'special_instructions.min' => 'Special instructions must be at least 3 characters when provided.',
            'gemstone_details.min' => 'Gemstone details must be at least 3 characters when provided.',
            'description.min' => 'The description must be at least 3 characters when provided.',
            'admin_notes.min' => 'Notes must be at least 3 characters when provided.',
            '*.not_regex' => 'HTML tags are not allowed in this field.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }

    public static function attributes(): array
    {
        return [
            'catalog_design_id' => 'catalog design',
            'reference_image' => 'reference image',
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
        ];
    }
}
