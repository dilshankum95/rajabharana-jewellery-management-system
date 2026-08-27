<?php

namespace App\Http\Requests\Admin;

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

        return $this->user()?->isAdmin()
            && $order->adminCanUpdateProduction();
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

    public function messages(): array
    {
        return ValidationRules::messages();
    }

    public function attributes(): array
    {
        return ValidationRules::attributes();
    }
}
