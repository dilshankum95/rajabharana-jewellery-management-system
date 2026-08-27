<?php

namespace App\Http\Controllers\Technician;

use App\Enums\ProductionStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $technician = $request->user();

        $assignedJobs = Order::with(['catalogDesign'])
            ->assignedToTechnician($technician->id)
            ->openTechnicianJobs()
            ->orderBy('expected_delivery_date')
            ->get();

        $activeJobs = $assignedJobs
            ->filter(fn (Order $order) => $order->production_status !== ProductionStatus::ReadyToPickup
                || $order->task_status === TaskStatus::Pending)
            ->values();

        $readyJobs = $assignedJobs
            ->where('production_status', ProductionStatus::ReadyToPickup)
            ->sortByDesc('updated_at')
            ->take(5)
            ->values();

        return view('technician.dashboard', [
            'activeJobs' => $activeJobs,
            'readyJobs' => $readyJobs,
            'stats' => [
                'active' => $activeJobs->count(),
                'in_production' => $activeJobs->where('production_status', ProductionStatus::InProduction)->count(),
                'quality_check' => $activeJobs->where('production_status', ProductionStatus::QualityCheck)->count(),
                'due_soon' => $activeJobs->filter(fn (Order $order) => $order->isDeliveryDueSoon())->count(),
            ],
        ]);
    }
}
