<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\UpdateProductionJobRequest;
use App\Models\Order;
use App\Models\ProductionLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->isAssignedTo($request->user()), 404);

        $order->load(['catalogDesign', 'productionLogs.user']);

        return view('technician.jobs.show', [
            'order' => $order,
            'statusOptions' => $this->availableStatusOptions($order),
        ]);
    }

    public function update(UpdateProductionJobRequest $request, Order $order): RedirectResponse
    {
        $previousStatus = $order->status;
        $newStatus = $request->enum('status', \App\Enums\OrderStatus::class);

        $order->update(['status' => $newStatus]);

        ProductionLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'from_status' => $previousStatus,
            'to_status' => $newStatus,
            'note' => $request->validated('note'),
        ]);

        $message = $previousStatus === $newStatus
            ? 'Workshop note saved.'
            : 'Job status updated to '.$newStatus->label().'.';

        return redirect()
            ->route('technician.jobs.show', $order)
            ->with('success', $message);
    }

    /** @return array<string, string> */
    private function availableStatusOptions(Order $order): array
    {
        $options = [];

        foreach (Order::technicianUpdatableStatuses() as $status) {
            if ($order->isValidTechnicianStatusTransition($status) || $status === $order->status) {
                $options[$status->value] = $status->label();
            }
        }

        return $options;
    }
}
