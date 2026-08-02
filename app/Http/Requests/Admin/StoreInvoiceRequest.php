<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Rbac::userHasPermission($this->user(), 'billing.manage');
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = Order::find($this->input('order_id'));

            if (! $order) {
                return;
            }

            if (! app(InvoiceService::class)->orderCanBeInvoiced($order)) {
                $validator->errors()->add('order_id', 'This order cannot be invoiced. Confirm it, set a price, and ensure it is not already invoiced.');
            }
        });
    }
}
