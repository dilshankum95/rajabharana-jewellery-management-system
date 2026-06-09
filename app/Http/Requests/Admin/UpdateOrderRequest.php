<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return $this->user()?->isAdminOrStaff() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['admin_notes'], ['admin_notes']);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'expected_delivery_date' => [
                'required',
                'date',
                'after_or_equal:'.$this->route('order')->created_at->format('Y-m-d'),
                'before:'.now()->addYear()->format('Y-m-d'),
            ],
            'estimated_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'admin_notes' => ValidationRules::longText(max: 2000),
        ];
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'expected_delivery_date.after_or_equal' => 'Delivery date cannot be before the order was placed.',
            'expected_delivery_date.before' => 'Delivery date cannot be more than one year from today.',
            'estimated_price.min' => 'Order price must be greater than zero when provided.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
