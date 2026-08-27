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
        return $this->user()?->hasPermission('orders.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $fields = ['admin_notes'];

        if ($this->user()?->isAdmin()) {
            $fields[] = 'estimated_price';
        }

        $this->trimStrings($fields, ['admin_notes', 'estimated_price']);
    }

    public function rules(): array
    {
        $order = $this->route('order');

        $rules = [
            'expected_delivery_date' => ValidationRules::deliveryDate(
                required: true,
                minDate: $order->created_at->format('Y-m-d'),
                maxDate: now()->addYear()->format('Y-m-d'),
            ),
            'admin_notes' => ValidationRules::longText(max: 2000),
        ];

        if ($this->user()?->isAdmin()) {
            $rules['status'] = ['required', Rule::enum(OrderStatus::class)];
            $rules['estimated_price'] = ValidationRules::money(required: false);
        }

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(ValidationRules::messages(), [
            'expected_delivery_date.after_or_equal' => 'Delivery date cannot be before the order was placed.',
            'expected_delivery_date.before_or_equal' => 'Delivery date cannot be more than one year from today.',
            'expected_delivery_date.after' => 'Expected delivery date must be at least one day from today.',
            'expected_delivery_date.before' => 'Expected delivery date cannot be more than one year from today.',
            'estimated_price.min' => 'Order price must be greater than zero when provided.',
        ]);
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
