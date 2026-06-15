<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $technician = $request->user();

        $activeJobs = Order::with(['catalogDesign'])
            ->assignedToTechnician($technician->id)
            ->activeProduction()
            ->orderBy('expected_delivery_date')
            ->get();

        $readyJobs = Order::with(['catalogDesign'])
            ->assignedToTechnician($technician->id)
            ->where('status', 'ready')
            ->latest()
            ->limit(5)
            ->get();

        return view('technician.dashboard', [
            'activeJobs' => $activeJobs,
            'readyJobs' => $readyJobs,
            'stats' => [
                'active' => $activeJobs->count(),
                'in_production' => $activeJobs->where('status', \App\Enums\OrderStatus::InProduction)->count(),
                'quality_check' => $activeJobs->where('status', \App\Enums\OrderStatus::QualityCheck)->count(),
                'due_soon' => $activeJobs->filter(fn (Order $order) => $order->isDeliveryDueSoon())->count(),
            ],
        ]);
    }
}
