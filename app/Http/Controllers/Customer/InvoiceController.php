<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $invoice = $order->invoice()
            ->with(['items', 'order.catalogDesign', 'payments' => fn ($q) => $q->completed()])
            ->firstOrFail();

        abort_unless($invoice->isIssued(), 404);

        return view('customer.invoices.show', compact('order', 'invoice'));
    }
}
