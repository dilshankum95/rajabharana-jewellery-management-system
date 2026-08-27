<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterOrdersRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\UpdateProductionStatusRequest;
use App\Models\Order;
use App\Models\ProductionLog;
use App\Services\InventoryService;
use App\Services\ProductionStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private ProductionStatusService $productionStatusService,
        private InventoryService $inventoryService
    ) {}
    public function index(FilterOrdersRequest $request): View
    {
        $validated = $request->validated();
        $query = Order::with(['user', 'catalogDesign']);

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->boolean('due')) {
            $query->needsDeliveryAttention()->orderBy('expected_delivery_date');
        } else {
            $query->latest();
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => config('jewellery.order_statuses'),
            'filters' => $request->only(['status', 'search', 'due']),
            'dueOrdersCount' => Order::needsDeliveryAttention()->count(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'catalogDesign', 'assignedTechnician', 'productionLogs.user', 'invoice']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => config('jewellery.order_statuses'),
            'technicians' => \App\Models\User::technicians()->orderBy('name')->get(),
            'adminProductionOptions' => $order->adminAvailableProductionStatusOptions(),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $previousStatus = $order->status;

        if ($request->user()->isAdmin() && isset($data['status'])) {
            $newStatus = OrderStatus::from($data['status']);

            if ($newStatus !== $previousStatus) {
                if ($newStatus === OrderStatus::Rejected) {
                    $data['task_status'] = TaskStatus::Rejected;
                }

                if ($newStatus === OrderStatus::Accepted && $previousStatus === OrderStatus::Pending) {
                    $data['task_status'] = TaskStatus::Pending;
                }
            }
        } else {
            unset($data['status'], $data['estimated_price']);
        }

        $wasAccepted = $order->status === OrderStatus::Accepted;

        try {
            if ($request->user()->isAdmin() && isset($data['status'])) {
                $newStatus = OrderStatus::from($data['status']);

                if ($newStatus === OrderStatus::Accepted && $previousStatus !== OrderStatus::Accepted) {
                    $this->inventoryService->deductForAcceptedOrder($order, $request->user());
                }

                if ($newStatus === OrderStatus::Rejected && $wasAccepted) {
                    $this->inventoryService->restoreForRejectedOrder($order, $request->user());
                }

                if ($newStatus !== $previousStatus) {
                    ProductionLog::create([
                        'order_id' => $order->id,
                        'user_id' => $request->user()->id,
                        'from_status' => $previousStatus,
                        'to_status' => $newStatus,
                        'note' => 'Order status changed by administrator.',
                    ]);
                }
            }

            $order->update($data);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $order->refresh();

        $redirect = redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');

        if ($order->isDeliveryOverdue()) {
            return $redirect->with(
                'warning',
                'Reminder: expected delivery ('.$order->expected_delivery_date->format('M d, Y').') has passed and this order is not completed. Please update the delivery date or finish production.'
            );
        }

        if ($order->isDeliveryDueSoon()) {
            return $redirect->with(
                'warning',
                'Reminder: expected delivery is '.$order->expected_delivery_date->format('M d, Y').'. Confirm the workshop can finish on time or adjust the date.'
            );
        }

        if (! $wasAccepted && $order->status === OrderStatus::Accepted
            && $order->expected_delivery_date->lte(today()->addDays(2))) {
            return $redirect->with(
                'warning',
                'This order was accepted with a tight delivery date ('.$order->expected_delivery_date->format('M d, Y').'). Monitor progress closely.'
            );
        }

        return $redirect;
    }

    public function updateProduction(UpdateProductionStatusRequest $request, Order $order): RedirectResponse
    {
        $newStatus = $request->enum('production_status', \App\Enums\ProductionStatus::class);

        $message = $this->productionStatusService->update(
            $order,
            $request->user(),
            $newStatus,
            $request->validated('note')
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $message);
    }
}
