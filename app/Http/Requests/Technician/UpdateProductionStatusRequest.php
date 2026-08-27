<?php

namespace App\Http\Requests\Technician;

use App\Enums\ProductionStatus;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionStatusRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user()?->isTechnician()
            && $order->technicianCanUpdateProduction($this->user());
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['note'], ['note']);
    }

    public function rules(): array
    {
        return [
            'production_status' => [
                'required',
                Rule::enum(ProductionStatus::class),
            ],
            'note' => ValidationRules::longText(max: 2000, required: false),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');
            $newStatus = ProductionStatus::tryFrom($this->input('production_status'));

            if (! $newStatus) {
                return;
            }

            if ($newStatus === $order->production_status) {
                return;
            }

            if (! $order->isValidProductionTransition($newStatus)) {
                $validator->errors()->add(
                    'production_status',
                    'Production status can only move forward one step at a time.'
                );
            }
        });
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
