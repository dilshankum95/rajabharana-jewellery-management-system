<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Http\Controllers\Controller;
use App\Models\MetalPrice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $orders = $user->orders()
            ->with(['catalogDesign', 'assignedTechnician'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', OrderStatus::Pending)->count(),
            'in_progress' => $user->orders()
                ->where('status', OrderStatus::Accepted)
                ->where(function ($query) {
                    $query->whereNull('production_status')
                        ->orWhere('production_status', '!=', ProductionStatus::ReadyToPickup->value);
                })
                ->count(),
            'ready' => $user->orders()
                ->where('production_status', ProductionStatus::ReadyToPickup)
                ->count(),
        ];

        return view('customer.dashboard', [
            'orders' => $orders,
            'stats' => $stats,
            'metalPrice' => MetalPrice::current(),
            'overdueDeliveryCount' => $user->orders()->deliveryOverdue()->count(),
        ]);
    }
}
