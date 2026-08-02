<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'billing.manage')
            && $this->route('invoice')?->isEditable();
    }

    public function rules(): array
    {
        return [
            'making_charge' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'discount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'tax' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000', ValidationRules::NO_HTML],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = $this->route('invoice');
            if (! $invoice) {
                return;
            }

            $maxDiscount = (float) $invoice->subtotal
                + (float) $this->input('making_charge', 0)
                + (float) $this->input('tax', 0);

            if ((float) $this->input('discount', 0) > $maxDiscount) {
                $validator->errors()->add('discount', 'Discount cannot exceed subtotal plus charges and tax.');
            }
        });
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }
}
