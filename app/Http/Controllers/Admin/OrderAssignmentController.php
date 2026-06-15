<?php

namespace App\Http\Controllers\Admin;

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

        if (! $order->isAssignableToTechnician()) {
            return back()->with('error', 'This order cannot be assigned — it must be confirmed and in active production.');
        }

        $technicianId = (int) $request->validated('assigned_technician_id');
        $wasAssigned = $order->assigned_technician_id === $technicianId;

        $order->update([
            'assigned_technician_id' => $technicianId,
            'assigned_at' => $wasAssigned ? $order->assigned_at : now(),
        ]);

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
