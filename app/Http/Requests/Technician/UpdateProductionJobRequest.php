<?php

namespace App\Http\Requests\Technician;

use App\Enums\OrderStatus;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionJobRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user()?->isTechnician()
            && $order->technicianCanUpdate($this->user());
    }

    protected function prepareForValidation(): void
    {
        $this->trimStrings(['note'], ['note']);
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(OrderStatus::class),
            ],
            'note' => ValidationRules::longText(max: 2000, required: false),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');
            $newStatus = OrderStatus::tryFrom($this->input('status'));

            if (! $newStatus) {
                return;
            }

            if ($newStatus === $order->status) {
                return;
            }

            if (! $order->isValidTechnicianStatusTransition($newStatus)) {
                $validator->errors()->add(
                    'status',
                    'This status change is not allowed from the current order state.'
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
