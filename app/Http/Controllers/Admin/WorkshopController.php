<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterProductionRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(FilterProductionRequest $request): View
    {
        $validated = $request->validated();

        $query = Order::with(['user', 'catalogDesign', 'assignedTechnician'])
            ->inProductionQueue()
            ->orderBy('expected_delivery_date');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['technician_id'])) {
            $query->where('assigned_technician_id', $validated['technician_id']);
        }

        if ($request->boolean('unassigned')) {
            $query->needsTechnicianAssignment();
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $queueBase = Order::inProductionQueue();

        return view('admin.workshop.index', [
            'orders' => $orders,
            'technicians' => User::technicians()->orderBy('name')->get(),
            'filters' => $request->only(['status', 'search', 'technician_id', 'unassigned']),
            'stats' => [
                'queue_total' => (clone $queueBase)->count(),
                'unassigned' => Order::needsTechnicianAssignment()->count(),
                'in_production' => (clone $queueBase)->where('status', OrderStatus::InProduction)->count(),
                'quality_check' => (clone $queueBase)->where('status', OrderStatus::QualityCheck)->count(),
                'ready' => (clone $queueBase)->where('status', OrderStatus::Ready)->count(),
            ],
            'statuses' => collect(config('jewellery.order_statuses'))
                ->only(['confirmed', 'in_production', 'quality_check', 'ready'])
                ->all(),
        ]);
    }

    public function technicians(): View
    {
        $technicians = User::technicians()
            ->withCount([
                'assignedOrders as active_jobs_count' => fn ($query) => $query->activeProduction(),
                'assignedOrders as ready_jobs_count' => fn ($query) => $query->where('status', OrderStatus::Ready),
                'assignedOrders as overdue_jobs_count' => fn ($query) => $query->activeProduction()->deliveryOverdue(),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.workshop.technicians', [
            'technicians' => $technicians,
        ]);
    }

    public function showTechnician(User $technician): View
    {
        abort_unless($technician->role === UserRole::Technician, 404);

        $activeJobs = Order::with(['catalogDesign'])
            ->assignedToTechnician($technician->id)
            ->inProductionQueue()
            ->orderBy('expected_delivery_date')
            ->get();

        $recentReady = Order::with(['catalogDesign'])
            ->assignedToTechnician($technician->id)
            ->where('status', OrderStatus::Ready)
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('admin.workshop.technician-show', [
            'technician' => $technician,
            'activeJobs' => $activeJobs,
            'recentReady' => $recentReady,
            'stats' => [
                'active' => $activeJobs->count(),
                'in_production' => $activeJobs->where('status', OrderStatus::InProduction)->count(),
                'quality_check' => $activeJobs->where('status', OrderStatus::QualityCheck)->count(),
                'overdue' => $activeJobs->filter(fn (Order $order) => $order->isDeliveryOverdue())->count(),
            ],
        ]);
    }
}
