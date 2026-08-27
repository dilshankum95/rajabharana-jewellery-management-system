<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\User;
use App\Notifications\InvoiceIssuedNotification;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceService
{
    public function __construct(
        private InvoiceCalculator $calculator
    ) {}

    /** @return array<int, OrderStatus> */
    public static function billableOrderStatuses(): array
    {
        return [
            OrderStatus::Accepted,
        ];
    }

    public function orderCanBeInvoiced(Order $order): bool
    {
        if ($order->invoice()->exists()) {
            return false;
        }

        if ($order->status !== OrderStatus::Accepted) {
            return false;
        }

        return $order->hasPrice();
    }

    public function createDraftFromOrder(Order $order, User $creator): Invoice
    {
        if (! $this->orderCanBeInvoiced($order)) {
            throw new InvalidArgumentException('This order cannot be invoiced yet. Confirm the order and set a price first.');
        }

        return DB::transaction(function () use ($order, $creator) {
            $order->loadMissing(['user', 'catalogDesign']);

            $quantity = max(1, (int) $order->quantity);
            $unitPrice = round((float) $order->estimated_price / $quantity, 2);
            $lineTotal = round($unitPrice * $quantity, 2);

            $invoice = new Invoice([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'subtotal' => $lineTotal,
                'making_charge' => 0,
                'invoice_status' => InvoiceStatus::Draft,
                'due_date' => today()->addDays((int) config('jewellery.invoice_due_days', 14)),
                'created_by' => $creator->id,
            ]);

            $this->calculator->applyToInvoice($invoice, $order);
            $invoice->save();

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_id' => $order->id,
                'description' => $invoice->buildLineDescriptionForOrder($order),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);

            return $invoice->load(['items', 'order', 'customer', 'creator']);
        });
    }

    public function updateDraft(Invoice $invoice, array $data): Invoice
    {
        if (! $invoice->isEditable()) {
            throw new InvalidArgumentException('Only draft invoices can be edited.');
        }

        $invoice->loadMissing('order.catalogDesign');

        $discountOverride = array_key_exists('discount', $data)
            ? (float) $data['discount']
            : null;

        $this->calculator->applyToInvoice(
            $invoice,
            $invoice->order,
            (float) ($data['making_charge'] ?? $invoice->making_charge),
            $discountOverride
        );

        $invoice->due_date = $data['due_date'] ?? $invoice->due_date;
        $invoice->notes = $data['notes'] ?? $invoice->notes;
        $invoice->save();

        return $invoice->fresh(['items', 'order', 'customer', 'creator']);
    }

    public function issue(Invoice $invoice): Invoice
    {
        if (! $invoice->isEditable()) {
            throw new InvalidArgumentException('This invoice has already been issued.');
        }

        if ($invoice->items()->count() === 0) {
            throw new InvalidArgumentException('Cannot issue an invoice without line items.');
        }

        $invoice->loadMissing('order.catalogDesign', 'customer');

        $this->calculator->applyToInvoice($invoice, $invoice->order);
        $invoice->update([
            'invoice_status' => InvoiceStatus::Issued,
            'issue_date' => today(),
        ]);

        $invoice = $invoice->fresh(['items', 'order', 'customer', 'creator']);

        $invoice->customer?->notify(new InvoiceIssuedNotification($invoice));

        return $invoice;
    }

    public function cancel(Invoice $invoice): Invoice
    {
        if ($invoice->invoice_status === InvoiceStatus::Cancelled) {
            throw new InvalidArgumentException('This invoice is already cancelled.');
        }

        if (in_array($invoice->invoice_status, [InvoiceStatus::Partial, InvoiceStatus::Paid], true)) {
            throw new InvalidArgumentException('Cannot cancel an invoice that has payments recorded.');
        }

        $invoice->update([
            'invoice_status' => InvoiceStatus::Cancelled,
        ]);

        return $invoice->fresh(['items', 'order', 'customer', 'creator']);
    }
}
