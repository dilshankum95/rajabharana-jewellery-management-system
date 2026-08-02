<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-page { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
    <div class="no-print max-w-3xl mx-auto px-4 py-6 flex justify-between items-center">
        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-sm text-slate-600 hover:text-slate-900">&larr; Back to invoice</a>
        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Print</button>
    </div>

    <div class="print-page max-w-3xl mx-auto bg-white shadow-lg border border-slate-200 rounded-xl p-8 sm:p-12 mb-12">
        <header class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-6 pb-8 border-b border-slate-200">
            <div>
                <p class="font-display text-2xl font-semibold text-slate-900">Rajabharana Jewellery</p>
                <p class="text-sm text-slate-500 mt-1">{{ config('app.name') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-widest text-slate-400">Tax Invoice</p>
                <p class="font-display text-xl font-semibold text-slate-900 mt-1">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-slate-500 mt-2">Issue date: {{ $invoice->issue_date->format('F d, Y') }}</p>
                @if($invoice->due_date)
                    <p class="text-sm text-slate-500">Due date: {{ $invoice->due_date->format('F d, Y') }}</p>
                @endif
            </div>
        </header>

        <div class="grid sm:grid-cols-2 gap-8 py-8">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 mb-2">Bill To</p>
                <p class="font-semibold text-slate-900">{{ $invoice->customer->name }}</p>
                <p class="text-sm text-slate-600">{{ $invoice->customer->email }}</p>
                @if($invoice->order->contact_phone)
                    <p class="text-sm text-slate-600">{{ $invoice->order->contact_phone }}</p>
                @endif
                @if($invoice->order->delivery_address)
                    <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $invoice->order->delivery_address }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <p class="text-xs uppercase tracking-wider text-slate-400 mb-2">Order Reference</p>
                <p class="font-semibold text-slate-900">{{ $invoice->order->order_number }}</p>
                <p class="text-sm text-slate-600 mt-1">{{ $invoice->order->item_type_label }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-8">
            <thead>
                <tr class="border-b-2 border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="py-3 pr-4">Description</th>
                    <th class="py-3 px-2 text-right">Qty</th>
                    <th class="py-3 px-2 text-right">Unit Price</th>
                    <th class="py-3 pl-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="border-b border-slate-100">
                        <td class="py-3 pr-4">{{ $item->description }}</td>
                        <td class="py-3 px-2 text-right">{{ $item->quantity }}</td>
                        <td class="py-3 px-2 text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 pl-2 text-right font-medium">LKR {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <dl class="w-full sm:w-64 space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Subtotal</dt>
                    <dd>LKR {{ number_format($invoice->subtotal, 2) }}</dd>
                </div>
                @if($invoice->making_charge > 0)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Making Charge</dt>
                        <dd>LKR {{ number_format($invoice->making_charge, 2) }}</dd>
                    </div>
                @endif
                @if($invoice->tax > 0)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Tax</dt>
                        <dd>LKR {{ number_format($invoice->tax, 2) }}</dd>
                    </div>
                @endif
                @if($invoice->discount > 0)
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Discount</dt>
                        <dd>− LKR {{ number_format($invoice->discount, 2) }}</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-4 pt-2 border-t border-slate-200 font-semibold text-base">
                    <dt>Total Due</dt>
                    <dd>LKR {{ number_format($invoice->grand_total, 2) }}</dd>
                </div>
            </dl>
        </div>

        @if($invoice->notes)
            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-xs uppercase tracking-wider text-slate-400 mb-2">Notes</p>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
        @endif

        <footer class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400">
            Thank you for choosing Rajabharana Jewellery.
        </footer>
    </div>
</body>
</html>
