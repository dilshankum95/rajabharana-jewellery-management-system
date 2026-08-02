<?php

namespace App\Http\Requests\Admin;

use App\Models\PaymentMethod;
use App\Services\PaymentService;
use App\Support\Rbac;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return Rbac::userHasPermission($this->user(), 'billing.manage')
            && $invoice
            && app(PaymentService::class)->canAcceptPayment($invoice);
    }

    public function rules(): array
    {
        $activeMethods = PaymentMethod::active()->pluck('code')->all();

        return [
            'payment_method' => ['required', 'string', Rule::in($activeMethods)],
            'payment_amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'transaction_reference' => ['nullable', 'string', 'max:255', ValidationRules::NO_HTML],
            'notes' => ['nullable', 'string', 'max:1000', ValidationRules::NO_HTML],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $method = PaymentMethod::query()
                ->where('code', $this->input('payment_method'))
                ->where('is_active', true)
                ->first();

            if ($method?->requires_reference && blank($this->input('transaction_reference'))) {
                $validator->errors()->add('transaction_reference', 'A transaction reference is required for this payment method.');
            }
        });
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }
}
