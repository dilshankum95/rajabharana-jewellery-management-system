<?php

namespace App\Services;

use App\Enums\AvailabilityStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Enums\ReportType;
use App\Enums\UserRole;
use App\Models\CatalogDesign;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RawMaterial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportEngine
{
    public function generate(ReportType $type, ?Carbon $from = null, ?Carbon $to = null): array
    {
        return match ($type) {
            ReportType::OrderSummary => $this->orderSummary($from, $to),
            ReportType::SalesRevenue => $this->salesRevenue($from, $to),
            ReportType::Customer => $this->customerReport($from, $to),
            ReportType::Production => $this->productionReport($from, $to),
            ReportType::Delivery => $this->deliveryReport($from, $to),
            ReportType::Inventory => $this->inventoryReport(),
            ReportType::BillingCollection => $this->billingCollection($from, $to),
        };
    }

    public function orderSummary(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $orders = Order::with(['user', 'catalogDesign'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $byStatus = $orders->groupBy(fn (Order $o) => $o->status->value)->map->count();

        $totalValue = $orders->where('status', '!=', OrderStatus::Rejected)
            ->sum(fn (Order $o) => (float) ($o->estimated_price ?? 0));

        $kpis = [
            ['label' => 'Total Orders', 'value' => (string) $orders->count()],
            ['label' => 'Pending', 'value' => (string) ($byStatus[OrderStatus::Pending->value] ?? 0)],
            ['label' => 'Accepted', 'value' => (string) ($byStatus[OrderStatus::Accepted->value] ?? 0)],
            ['label' => 'Ready to Pickup', 'value' => (string) $orders->where('production_status', ProductionStatus::ReadyToPickup)->count()],
            ['label' => 'Quoted Value', 'value' => 'Rs. '.number_format($totalValue, 2)],
        ];

        $rows = $orders->map(fn (Order $o) => [
            $o->order_number,
            $o->created_at->format('Y-m-d'),
            $o->user->name,
            $o->item_type_label,
            $o->status->label(),
            $o->estimated_price !== null ? number_format((float) $o->estimated_price, 2) : '—',
            $o->expected_delivery_date?->format('Y-m-d') ?? '—',
        ])->all();

        return $this->build(
            ReportType::OrderSummary,
            $from,
            $to,
            $kpis,
            ['Order #', 'Date', 'Customer', 'Item', 'Status', 'Price (Rs.)', 'Delivery'],
            $rows
        );
    }

    public function salesRevenue(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $invoices = Invoice::with(['order', 'customer', 'payments'])
            ->whereNot('invoice_status', InvoiceStatus::Draft)
            ->whereNot('invoice_status', InvoiceStatus::Cancelled)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('issue_date')
                            ->whereBetween('created_at', [$from, $to]);
                    });
            })
            ->latest('issue_date')
            ->get();

        $totalInvoiced = $invoices->sum('grand_total');
        $totalCollected = $invoices->sum(fn (Invoice $i) => $i->amount_paid);
        $outstanding = max(0, $totalInvoiced - $totalCollected);

        $paymentsInPeriod = Payment::completed()
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->sum('payment_amount');

        $kpis = [
            ['label' => 'Invoices', 'value' => (string) $invoices->count()],
            ['label' => 'Total Invoiced', 'value' => 'Rs. '.number_format($totalInvoiced, 2)],
            ['label' => 'Collected (on invoices)', 'value' => 'Rs. '.number_format($totalCollected, 2)],
            ['label' => 'Outstanding', 'value' => 'Rs. '.number_format($outstanding, 2)],
            ['label' => 'Payments in Period', 'value' => 'Rs. '.number_format((float) $paymentsInPeriod, 2)],
        ];

        $rows = $invoices->map(fn (Invoice $i) => [
            $i->invoice_number,
            $i->issue_date?->format('Y-m-d') ?? $i->created_at->format('Y-m-d'),
            $i->customer->name,
            $i->order->order_number,
            number_format((float) $i->grand_total, 2),
            number_format($i->amount_paid, 2),
            number_format($i->balance_due, 2),
            $i->invoice_status->label(),
        ])->all();

        return $this->build(
            ReportType::SalesRevenue,
            $from,
            $to,
            $kpis,
            ['Invoice #', 'Issue Date', 'Customer', 'Order #', 'Total', 'Paid', 'Balance', 'Status'],
            $rows
        );
    }

    public function customerReport(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $customers = User::where('role', UserRole::Customer)
            ->whereHas('orders', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->withCount(['orders as period_orders' => fn ($q) => $q->whereBetween('created_at', [$from, $to])])
            ->with(['orders' => fn ($q) => $q->whereBetween('created_at', [$from, $to])])
            ->orderByDesc('period_orders')
            ->get();

        $totalOrders = $customers->sum('period_orders');
        $totalValue = $customers->flatMap->orders
            ->where('status', '!=', OrderStatus::Rejected)
            ->sum(fn (Order $o) => (float) ($o->estimated_price ?? 0));

        $kpis = [
            ['label' => 'Active Customers', 'value' => (string) $customers->count()],
            ['label' => 'Orders in Period', 'value' => (string) $totalOrders],
            ['label' => 'Total Order Value', 'value' => 'Rs. '.number_format($totalValue, 2)],
            ['label' => 'Avg Orders / Customer', 'value' => $customers->count() > 0
                ? number_format($totalOrders / $customers->count(), 1)
                : '0'],
        ];

        $rows = $customers->map(function (User $c) {
            $value = $c->orders
                ->where('status', '!=', OrderStatus::Rejected)
                ->sum(fn (Order $o) => (float) ($o->estimated_price ?? 0));

            return [
                $c->name,
                $c->email,
                $c->phone,
                $c->city,
                (string) $c->period_orders,
                number_format($value, 2),
            ];
        })->all();

        return $this->build(
            ReportType::Customer,
            $from,
            $to,
            $kpis,
            ['Name', 'Email', 'Phone', 'City', 'Orders', 'Value (Rs.)'],
            $rows
        );
    }

    public function productionReport(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $orders = Order::with('assignedTechnician')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', OrderStatus::Accepted)
            ->get();

        $technicians = User::technicians()->orderBy('name')->get();
        $byTechnician = $orders->groupBy('assigned_technician_id');

        $unassigned = $orders->whereNull('assigned_technician_id')->count();
        $inProduction = $orders->whereIn('production_status', [
            ProductionStatus::InProduction,
            ProductionStatus::QualityCheck,
        ])->count();
        $ready = $orders->where('production_status', ProductionStatus::ReadyToPickup)->count();

        $kpis = [
            ['label' => 'Production Orders', 'value' => (string) $orders->count()],
            ['label' => 'Unassigned', 'value' => (string) $unassigned],
            ['label' => 'In Production / QC', 'value' => (string) $inProduction],
            ['label' => 'Ready to Pickup', 'value' => (string) $ready],
        ];

        $rows = $technicians->map(function (User $tech) use ($byTechnician) {
            /** @var Collection<int, Order> $assigned */
            $assigned = $byTechnician->get($tech->id, collect());

            return [
                $tech->name,
                (string) $assigned->count(),
                (string) $assigned->where('production_status', ProductionStatus::InProduction)->count(),
                (string) $assigned->where('production_status', ProductionStatus::QualityCheck)->count(),
                (string) $assigned->where('production_status', ProductionStatus::ReadyToPickup)->count(),
                (string) $assigned->where('production_status', ProductionStatus::ProductionConfirm)->count(),
            ];
        })->all();

        if ($unassigned > 0) {
            $rows[] = ['Unassigned', (string) $unassigned, '—', '—', '—', '—'];
        }

        return $this->build(
            ReportType::Production,
            $from,
            $to,
            $kpis,
            ['Technician', 'Total Jobs', 'In Production', 'Quality Check', 'Ready to Pickup', 'Production Confirm'],
            $rows
        );
    }

    public function deliveryReport(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $orders = Order::with('user')
            ->whereBetween('expected_delivery_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('expected_delivery_date')
            ->get();

        $overdue = $orders->filter(fn (Order $o) => $o->isDeliveryOverdue())->count();
        $dueSoon = $orders->filter(fn (Order $o) => $o->isDeliveryDueSoon())->count();
        $readyToPickup = $orders->where('production_status', ProductionStatus::ReadyToPickup)->count();
        $onTrack = $orders->filter(function (Order $o) {
            return $o->isActiveForDeliveryTracking()
                && ! $o->isDeliveryOverdue()
                && ! $o->isDeliveryDueSoon();
        })->count();

        $kpis = [
            ['label' => 'Orders in Range', 'value' => (string) $orders->count()],
            ['label' => 'Overdue', 'value' => (string) $overdue],
            ['label' => 'Due Soon', 'value' => (string) $dueSoon],
            ['label' => 'On Track', 'value' => (string) $onTrack],
            ['label' => 'Ready to Pickup', 'value' => (string) $readyToPickup],
        ];

        $rows = $orders->map(function (Order $o) {
            $alert = match ($o->deliveryAlertType()) {
                'overdue' => 'Overdue',
                'due_soon' => 'Due Soon',
                default => $o->production_status === ProductionStatus::ReadyToPickup ? 'Ready to Pickup' : 'On Track',
            };

            return [
                $o->order_number,
                $o->user->name,
                $o->expected_delivery_date->format('Y-m-d'),
                $o->status->label(),
                $o->production_status?->label() ?? '—',
                $alert,
            ];
        })->all();

        return $this->build(
            ReportType::Delivery,
            $from,
            $to,
            $kpis,
            ['Order #', 'Customer', 'Expected Delivery', 'Order Status', 'Production Status', 'Delivery Alert'],
            $rows
        );
    }

    public function inventoryReport(): array
    {
        $designs = CatalogDesign::withCount('orders')->orderBy('category')->orderBy('name')->get();
        $materials = RawMaterial::active()->orderBy('material_type')->orderBy('name')->get();
        $categories = config('jewellery.catalog_categories', []);

        $availableDesigns = $designs->where('availability_status', AvailabilityStatus::Available);
        $outOfStockDesigns = $designs->where('availability_status', AvailabilityStatus::OutOfStock);
        $totalStockUnits = (int) $designs->sum('stock_quantity');
        $availableStockUnits = (int) $availableDesigns->sum('stock_quantity');

        $totalValue = (float) $designs->sum(fn (CatalogDesign $d) => (float) $d->selling_price * (int) $d->stock_quantity);
        $availableValue = (float) $availableDesigns->sum(fn (CatalogDesign $d) => (float) $d->selling_price * (int) $d->stock_quantity);
        $totalWeight = (float) $designs->sum(fn (CatalogDesign $d) => (float) $d->weight_grams * (int) $d->stock_quantity);
        $totalOrders = (int) $designs->sum('orders_count');
        $categoriesWithItems = $designs->groupBy('category')->count();
        $lowStockMaterials = $materials->filter(fn (RawMaterial $m) => $m->isLowStock())->count();

        $kpis = [
            ['label' => 'Catalog Designs', 'value' => (string) $designs->count()],
            ['label' => 'Stock Units', 'value' => (string) $totalStockUnits],
            ['label' => 'Available Units', 'value' => (string) $availableStockUnits],
            ['label' => 'Stock Value', 'value' => 'Rs. '.number_format($totalValue, 2)],
            ['label' => 'Raw Materials', 'value' => (string) $materials->count()],
            ['label' => 'Low Stock Materials', 'value' => (string) $lowStockMaterials],
            ['label' => 'Linked Orders', 'value' => (string) $totalOrders],
            ['label' => 'Categories', 'value' => (string) $categoriesWithItems.' / '.count($categories)],
        ];

        $grouped = $designs->groupBy('category');
        $summaryRows = [];

        foreach ($categories as $key => $label) {
            /** @var \Illuminate\Support\Collection<int, CatalogDesign> $items */
            $items = $grouped->get($key, collect());
            $available = $items->where('availability_status', AvailabilityStatus::Available);
            $outOfStock = $items->where('availability_status', AvailabilityStatus::OutOfStock);
            $stockUnits = (int) $items->sum('stock_quantity');
            $categoryValue = (float) $items->sum(fn (CatalogDesign $d) => (float) $d->selling_price * (int) $d->stock_quantity);
            $categoryAvailableValue = (float) $available->sum(fn (CatalogDesign $d) => (float) $d->selling_price * (int) $d->stock_quantity);
            $categoryWeight = (float) $items->sum(fn (CatalogDesign $d) => (float) $d->weight_grams * (int) $d->stock_quantity);
            $categoryOrders = (int) $items->sum('orders_count');
            $itemCount = $items->count();
            $share = $designs->count() > 0
                ? number_format(($itemCount / $designs->count()) * 100, 1).'%'
                : '0%';

            $summaryRows[] = [
                $label,
                (string) $itemCount,
                (string) $stockUnits,
                (string) $available->count(),
                (string) $outOfStock->count(),
                number_format($categoryValue, 2),
                number_format($categoryAvailableValue, 2),
                $itemCount > 0 ? number_format($categoryValue / max(1, $stockUnits), 2) : '—',
                number_format($categoryWeight, 2),
                (string) $categoryOrders,
                $share,
            ];
        }

        $summaryRows[] = [
            'Grand Total',
            (string) $designs->count(),
            (string) $totalStockUnits,
            (string) $availableDesigns->count(),
            (string) $outOfStockDesigns->count(),
            number_format($totalValue, 2),
            number_format($availableValue, 2),
            $totalStockUnits > 0 ? number_format($totalValue / $totalStockUnits, 2) : '—',
            number_format($totalWeight, 2),
            (string) $totalOrders,
            '100%',
        ];

        $detailRows = $designs->map(fn (CatalogDesign $d) => [
            $d->code,
            $d->name,
            $d->category_label,
            (string) $d->stock_quantity,
            number_format((float) $d->weight_grams, 2),
            number_format((float) $d->selling_price, 2),
            number_format((float) $d->selling_price * (int) $d->stock_quantity, 2),
            $d->availability_status->label(),
            (string) $d->orders_count,
        ])->all();

        $materialRows = $materials->map(fn (RawMaterial $m) => [
            $m->code,
            $m->name,
            $m->material_type_label,
            number_format((float) $m->stock_quantity, 3),
            $m->unit_label,
            $m->reorder_level !== null ? number_format((float) $m->reorder_level, 3) : '—',
            $m->unit_cost ? number_format((float) $m->unit_cost, 2) : '—',
            $m->unit_cost ? number_format((float) $m->stock_quantity * (float) $m->unit_cost, 2) : '—',
            $m->isLowStock() ? 'Low Stock' : 'OK',
        ])->all();

        return $this->build(
            ReportType::Inventory,
            null,
            null,
            $kpis,
            ['Category', 'Designs', 'Stock Units', 'Available', 'Out of Stock', 'Stock Value (Rs.)', 'Available Value (Rs.)', 'Avg Unit Value (Rs.)', 'Total Weight (g)', 'Orders', '% of Catalog'],
            $summaryRows,
            [
                [
                    'title' => 'Catalog Item Detail',
                    'columns' => ['Code', 'Name', 'Category', 'Stock Qty', 'Weight (g)', 'Unit Price (Rs.)', 'Stock Value (Rs.)', 'Status', 'Orders'],
                    'rows' => $detailRows,
                ],
                [
                    'title' => 'Raw Materials Stock',
                    'columns' => ['Code', 'Name', 'Type', 'Stock', 'Unit', 'Reorder Level', 'Unit Cost (Rs.)', 'Total Value (Rs.)', 'Alert'],
                    'rows' => $materialRows,
                ],
            ]
        );
    }

    public function billingCollection(?Carbon $from, ?Carbon $to): array
    {
        [$from, $to] = $this->resolveRange($from, $to);

        $invoices = Invoice::with(['customer', 'payments'])
            ->whereNot('invoice_status', InvoiceStatus::Draft)
            ->whereNot('invoice_status', InvoiceStatus::Cancelled)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('issue_date')
                            ->whereBetween('created_at', [$from, $to]);
                    });
            })
            ->get();

        $byStatus = $invoices->groupBy(fn (Invoice $i) => $i->invoice_status->value);

        $totalDue = $invoices->sum(fn (Invoice $i) => $i->balance_due);

        $payments = Payment::with(['invoice.customer', 'paymentMethod'])
            ->completed()
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->latest('payment_date')
            ->get();

        $kpis = [
            ['label' => 'Invoices', 'value' => (string) $invoices->count()],
            ['label' => 'Paid', 'value' => (string) ($byStatus[InvoiceStatus::Paid->value] ?? 0)],
            ['label' => 'Partial', 'value' => (string) ($byStatus[InvoiceStatus::Partial->value] ?? 0)],
            ['label' => 'Outstanding', 'value' => 'Rs. '.number_format($totalDue, 2)],
            ['label' => 'Collected in Period', 'value' => 'Rs. '.number_format((float) $payments->sum('payment_amount'), 2)],
        ];

        if ($payments->isNotEmpty()) {
            $columns = ['Payment Date', 'Invoice #', 'Customer', 'Method', 'Amount (Rs.)', 'Reference'];
            $rows = $payments->map(fn (Payment $p) => [
                $p->payment_date->format('Y-m-d'),
                $p->invoice->invoice_number,
                $p->invoice->customer->name ?? '—',
                $p->paymentMethod?->label ?? $p->payment_method,
                number_format((float) $p->payment_amount, 2),
                $p->transaction_reference ?? '—',
            ])->all();
        } else {
            $columns = ['Date', 'Invoice #', 'Customer', 'Method', 'Amount (Rs.)', 'Status'];
            $rows = $invoices->map(fn (Invoice $i) => [
                $i->issue_date?->format('Y-m-d') ?? '—',
                $i->invoice_number,
                $i->customer->name,
                '—',
                number_format($i->amount_paid, 2),
                $i->invoice_status->label(),
            ])->all();
        }

        return $this->build(
            ReportType::BillingCollection,
            $from,
            $to,
            $kpis,
            $columns,
            $rows
        );
    }

    /** @param  list<array{label: string, value: string}>  $kpis */
    /** @param  list<string>  $columns */
    /** @param  list<list<string>>  $rows */
    /** @param  list<array{title: string, columns: list<string>, rows: list<list<string>>}>  $sections */
    private function build(
        ReportType $type,
        ?Carbon $from,
        ?Carbon $to,
        array $kpis,
        array $columns,
        array $rows,
        array $sections = []
    ): array {
        return [
            'type' => $type,
            'title' => $type->label(),
            'description' => $type->description(),
            'date_from' => $from?->toDateString(),
            'date_to' => $to?->toDateString(),
            'generated_at' => now(),
            'kpis' => $kpis,
            'columns' => $columns,
            'rows' => $rows,
            'sections' => $sections,
        ];
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function resolveRange(?Carbon $from, ?Carbon $to): array
    {
        $from ??= now()->subDays(30)->startOfDay();
        $to ??= now()->endOfDay();

        return [$from, $to];
    }
}
