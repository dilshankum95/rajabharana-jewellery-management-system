<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterOrdersRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
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
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $wasConfirmed = $order->status === OrderStatus::Confirmed;
        $order->update($request->validated());
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

        if (! $wasConfirmed && $order->status === OrderStatus::Confirmed
            && $order->expected_delivery_date->lte(today()->addDays(2))) {
            return $redirect->with(
                'warning',
                'This order was confirmed with a tight delivery date ('.$order->expected_delivery_date->format('M d, Y').'). Monitor progress closely.'
            );
        }

        return $redirect;
    }
}
