<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public Payment $payment
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $balance = max(0, (float) $this->invoice->grand_total - (float) $this->invoice->amount_paid);

        return [
            'type' => 'payment_received',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'order_id' => $this->invoice->order_id,
            'order_number' => $this->invoice->order?->order_number,
            'payment_amount' => (float) $this->payment->payment_amount,
            'balance_due' => $balance,
            'message' => 'Payment of LKR '.number_format($this->payment->payment_amount, 2).' received for invoice '.$this->invoice->invoice_number.'. Balance due: LKR '.number_format($balance, 2),
            'url' => route('orders.invoice.show', $this->invoice->order_id),
        ];
    }
}
