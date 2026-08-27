<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MetalPrice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::Pending)->count(),
            'in_progress' => Order::where('status', OrderStatus::Accepted)
                ->where(function ($query) {
                    $query->whereNull('production_status')
                        ->orWhere('production_status', '!=', ProductionStatus::ReadyToPickup->value);
                })
                ->count(),
            'ready_orders' => Order::where('production_status', ProductionStatus::ReadyToPickup)->count(),
            'total_customers' => User::where('role', UserRole::Customer)->count(),
            'quoted_value' => Order::whereNotNull('estimated_price')
                ->where('status', '!=', OrderStatus::Rejected)
                ->sum('estimated_price'),
            'overdue_deliveries' => Order::deliveryOverdue()->count(),
            'due_soon_deliveries' => Order::deliveryDueSoon()->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $pendingOrders = Order::with('user')
            ->where('status', OrderStatus::Pending)
            ->latest()
            ->limit(5)
            ->get();

        $overdueOrders = Order::with('user')
            ->deliveryOverdue()
            ->orderBy('expected_delivery_date')
            ->limit(20)
            ->get();

        $dueSoonOrders = Order::with('user')
            ->deliveryDueSoon()
            ->orderBy('expected_delivery_date')
            ->limit(20)
            ->get();

        $dueOrders = Order::with('user')
            ->needsDeliveryAttention()
            ->orderBy('expected_delivery_date')
            ->limit(20)
            ->get();

        $metalPrice = MetalPrice::current();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'pendingOrders',
            'overdueOrders',
            'dueSoonOrders',
            'dueOrders',
            'metalPrice',
        ));
    }
}
