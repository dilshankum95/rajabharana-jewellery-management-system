<?php

namespace App\Services;

use App\Models\BillingSetting;
use App\Models\CategoryDiscount;
use App\Models\Invoice;
use App\Models\Order;

class InvoiceCalculator
{
    /**
     * @return array{making_charge: float, tax_rate_percent: float, discount_percent: float, discount: float, tax: float, grand_total: float}
     */
    public function calculate(Invoice $invoice, Order $order, ?float $makingCharge = null, ?float $discountOverride = null): array
    {
        $subtotal = (float) $invoice->subtotal;
        $makingCharge = $makingCharge ?? (float) $invoice->making_charge;
        $taxRate = BillingSetting::currentTaxRate();
        $discountPercent = CategoryDiscount::discountPercentForOrder($order);

        $discount = $discountOverride ?? round($subtotal * $discountPercent / 100, 2);
        $discount = min($discount, $subtotal + $makingCharge);

        $taxableBase = max(0, $subtotal + $makingCharge - $discount);
        $tax = round($taxableBase * $taxRate / 100, 2);

        $grandTotal = max(0, round($subtotal + $makingCharge + $tax - $discount, 2));

        return [
            'making_charge' => $makingCharge,
            'tax_rate_percent' => $taxRate,
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'tax' => $tax,
            'grand_total' => $grandTotal,
        ];
    }

    public function applyToInvoice(Invoice $invoice, Order $order, ?float $makingCharge = null, ?float $discountOverride = null): Invoice
    {
        $charges = $this->calculate($invoice, $order, $makingCharge, $discountOverride);

        $invoice->fill($charges);
        $invoice->recalculateGrandTotal();

        return $invoice;
    }
}
