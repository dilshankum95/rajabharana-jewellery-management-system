<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
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
            'in_progress' => Order::whereIn('status', [
                OrderStatus::Confirmed,
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
            ])->count(),
            'ready_orders' => Order::where('status', OrderStatus::Ready)->count(),
            'total_customers' => User::where('role', UserRole::Customer)->count(),
            'quoted_value' => Order::whereNotNull('estimated_price')
                ->whereNot('status', OrderStatus::Cancelled)
                ->sum('estimated_price'),
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

        return view('admin.dashboard', compact('stats', 'recentOrders', 'pendingOrders'));
    }
}
