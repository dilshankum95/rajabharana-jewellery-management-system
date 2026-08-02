<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterInvoicesRequest;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Http\Requests\Admin\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function index(FilterInvoicesRequest $request): View
    {
        $validated = $request->validated();
        $query = Invoice::with(['order', 'customer', 'creator'])->latest();

        if (! empty($validated['status'])) {
            $query->where('invoice_status', $validated['status']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                    ->orWhereHas('customer', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
            'statuses' => config('jewellery.invoice_statuses'),
        ]);
    }

    public function create(Order $order): View|RedirectResponse
    {
        if ($order->invoice) {
            return redirect()->route('admin.invoices.show', $order->invoice);
        }

        if (! $this->invoiceService->orderCanBeInvoiced($order)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'This order cannot be invoiced yet. Confirm the order and set a price first.');
        }

        $order->load(['user', 'catalogDesign']);

        $categoryCode = \App\Models\CategoryDiscount::categoryCodeForOrder($order);
        $categoryLabel = config('jewellery.catalog_categories.'.$categoryCode, ucfirst($categoryCode));
        $discountPercent = \App\Models\CategoryDiscount::discountPercentForOrder($order);
        $taxRate = \App\Models\BillingSetting::currentTaxRate();

        return view('admin.invoices.create', compact('order', 'categoryCode', 'categoryLabel', 'discountPercent', 'taxRate'));
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $order = Order::findOrFail($request->validated('order_id'));

        try {
            $invoice = $this->invoiceService->createDraftFromOrder($order, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.edit', $invoice)
            ->with('success', 'Draft invoice created. Review charges and issue when ready.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['items', 'order.catalogDesign', 'customer', 'creator', 'payments.recorder', 'payments.paymentMethod']);

        $paymentMethods = \App\Models\PaymentMethod::active()->get();
        $canRecordPayment = app(\App\Services\PaymentService::class)->canAcceptPayment($invoice);

        return view('admin.invoices.show', compact('invoice', 'paymentMethods', 'canRecordPayment'));
    }

    public function edit(Invoice $invoice): View|RedirectResponse
    {
        if (! $invoice->isEditable()) {
            return redirect()
                ->route('admin.invoices.show', $invoice)
                ->with('warning', 'Issued invoices cannot be edited.');
        }

        $invoice->load(['items', 'order.catalogDesign', 'customer']);

        $categoryCode = \App\Models\CategoryDiscount::categoryCodeForOrder($invoice->order);
        $categoryLabel = config('jewellery.catalog_categories.'.$categoryCode, ucfirst($categoryCode));
        $taxRate = \App\Models\BillingSetting::currentTaxRate();

        return view('admin.invoices.edit', compact('invoice', 'categoryCode', 'categoryLabel', 'taxRate'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->updateDraft($invoice, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Draft invoice updated.');
    }

    public function issue(Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->issue($invoice);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice issued successfully.');
    }

    public function cancel(Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->cancel($invoice);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice cancelled.');
    }

    public function print(Invoice $invoice): View
    {
        abort_unless($invoice->isIssued(), 404);

        $invoice->load(['items', 'order', 'customer', 'creator']);

        return view('admin.invoices.print', compact('invoice'));
    }
}
