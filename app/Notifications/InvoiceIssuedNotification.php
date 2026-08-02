<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceIssuedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_issued',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'order_id' => $this->invoice->order_id,
            'order_number' => $this->invoice->order?->order_number,
            'grand_total' => (float) $this->invoice->grand_total,
            'message' => 'Invoice '.$this->invoice->invoice_number.' has been issued for order '.$this->invoice->order?->order_number.'. Total: LKR '.number_format($this->invoice->grand_total, 2),
            'url' => route('orders.invoice.show', $this->invoice->order_id),
        ];
    }
}
