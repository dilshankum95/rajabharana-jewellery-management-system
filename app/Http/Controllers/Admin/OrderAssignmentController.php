<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTechnicianRequest;
use App\Models\Order;
use App\Models\ProductionLog;
use Illuminate\Http\RedirectResponse;

class OrderAssignmentController extends Controller
{
    public function update(AssignTechnicianRequest $request, Order $order): RedirectResponse
    {
        if (! $request->filled('assigned_technician_id') && $order->assigned_technician_id) {
            $order->update([
                'assigned_technician_id' => null,
                'assigned_at' => null,
                'task_status' => TaskStatus::Pending,
                'production_status' => null,
            ]);

            ProductionLog::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'from_status' => $order->status,
                'to_status' => $order->status,
                'note' => 'Technician assignment removed.',
            ]);

            return back()->with('success', 'Technician unassigned from this order.');
        }

        if (! $order->isAssignableToTechnician() && ! $order->assigned_technician_id) {
            return back()->with('error', 'Only accepted orders can be assigned to a technician.');
        }

        $technicianId = (int) $request->validated('assigned_technician_id');

        if ($technicianId <= 0) {
            return back()->with('error', 'Please select a technician to assign.');
        }

        $wasAssigned = $order->assigned_technician_id === $technicianId;

        $updates = [
            'assigned_technician_id' => $technicianId,
            'assigned_at' => $wasAssigned ? $order->assigned_at : now(),
        ];

        if (! $wasAssigned) {
            $updates['task_status'] = TaskStatus::Pending;
            $updates['production_status'] = null;
        }

        $order->update($updates);

        if (! $wasAssigned) {
            $technician = \App\Models\User::find($technicianId);

            ProductionLog::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'from_status' => $order->status,
                'to_status' => $order->status,
                'note' => 'Assigned to technician: '.($technician?->name ?? 'Unknown'),
            ]);
        }

        return back()->with('success', 'Technician assignment updated.');
    }
}
