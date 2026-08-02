<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    /** @var array<int, InvoiceStatus> */
    private const PAYABLE_STATUSES = [
        InvoiceStatus::Issued,
        InvoiceStatus::Partial,
        InvoiceStatus::Overdue,
    ];

    public function canAcceptPayment(Invoice $invoice): bool
    {
        return in_array($invoice->invoice_status, self::PAYABLE_STATUSES, true)
            && $invoice->balance_due > 0;
    }

    public function recordPayment(Invoice $invoice, array $data, User $recorder): Payment
    {
        if (! $this->canAcceptPayment($invoice)) {
            throw new InvalidArgumentException('This invoice cannot accept payments.');
        }

        $method = PaymentMethod::query()
            ->where('code', $data['payment_method'])
            ->where('is_active', true)
            ->first();

        if (! $method) {
            throw new InvalidArgumentException('Invalid payment method.');
        }

        if ($method->requires_reference && empty($data['transaction_reference'])) {
            throw new InvalidArgumentException('A transaction reference is required for this payment method.');
        }

        $amount = round((float) $data['payment_amount'], 2);

        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if ($amount > $invoice->balance_due) {
            throw new InvalidArgumentException('Payment amount cannot exceed the balance due (LKR '.number_format($invoice->balance_due, 2).').');
        }

        if ($invoice->issue_date && $data['payment_date'] < $invoice->issue_date->format('Y-m-d')) {
            throw new InvalidArgumentException('Payment date cannot be before the invoice issue date.');
        }

        return DB::transaction(function () use ($invoice, $data, $recorder, $amount, $method) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_method' => $method->code,
                'payment_amount' => $amount,
                'payment_status' => PaymentStatus::Completed,
                'payment_date' => $data['payment_date'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $recorder->id,
            ]);

            $this->syncInvoiceStatus($invoice->fresh(['payments', 'customer', 'order']));

            $invoice = $invoice->fresh(['payments', 'customer', 'order']);

            $invoice->customer?->notify(new PaymentReceivedNotification($invoice, $payment));

            return $payment->load(['recorder', 'paymentMethod']);
        });
    }

    public function syncInvoiceStatus(Invoice $invoice): Invoice
    {
        if (in_array($invoice->invoice_status, [InvoiceStatus::Draft, InvoiceStatus::Cancelled], true)) {
            return $invoice;
        }

        $paid = $invoice->payments()->completed()->sum('payment_amount');
        $grandTotal = (float) $invoice->grand_total;

        $newStatus = match (true) {
            $paid >= $grandTotal && $grandTotal > 0 => InvoiceStatus::Paid,
            $paid > 0 => InvoiceStatus::Partial,
            default => $invoice->invoice_status === InvoiceStatus::Overdue
                ? InvoiceStatus::Overdue
                : InvoiceStatus::Issued,
        };

        if ($invoice->invoice_status !== $newStatus) {
            $invoice->update(['invoice_status' => $newStatus]);
        }

        return $invoice->fresh();
    }
}
