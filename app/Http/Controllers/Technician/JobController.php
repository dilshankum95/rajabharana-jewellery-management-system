<?php

namespace App\Http\Controllers\Technician;

use App\Enums\ProductionStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\RespondToTaskRequest;
use App\Http\Requests\Technician\UpdateProductionStatusRequest;
use App\Models\Order;
use App\Models\ProductionLog;
use App\Services\ProductionStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(
        private ProductionStatusService $productionStatusService
    ) {}

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->isAssignedTo($request->user()), 404);

        $order->load(['catalogDesign', 'productionLogs.user']);

        return view('technician.jobs.show', [
            'order' => $order,
            'productionOptions' => $order->availableProductionStatusOptions(),
        ]);
    }

    public function respondToTask(RespondToTaskRequest $request, Order $order): RedirectResponse
    {
        $previousTaskStatus = $order->task_status;
        $newTaskStatus = $request->validated('action') === 'accept'
            ? TaskStatus::Accepted
            : TaskStatus::Rejected;

        $order->update(['task_status' => $newTaskStatus]);

        ProductionLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'from_status' => $order->status,
            'to_status' => $order->status,
            'note' => 'Task status: '.$previousTaskStatus->label().' → '.$newTaskStatus->label(),
        ]);

        $message = $newTaskStatus === TaskStatus::Accepted
            ? 'You have accepted this task.'
            : 'You have rejected this task. The administrator will be notified.';

        return redirect()
            ->route('technician.jobs.show', $order)
            ->with('success', $message);
    }

    public function updateProduction(UpdateProductionStatusRequest $request, Order $order): RedirectResponse
    {
        $newStatus = $request->enum('production_status', ProductionStatus::class);

        $message = $this->productionStatusService->update(
            $order,
            $request->user(),
            $newStatus,
            $request->validated('note')
        );

        return redirect()
            ->route('technician.jobs.show', $order)
            ->with('success', $message);
    }
}
