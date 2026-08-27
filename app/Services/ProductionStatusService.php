<?php

namespace App\Services;

use App\Enums\ProductionStatus;
use App\Models\Order;
use App\Models\ProductionLog;
use App\Models\User;

class ProductionStatusService
{
    public function update(Order $order, User $actor, ProductionStatus $newStatus, ?string $note = null): string
    {
        $previousStatus = $order->production_status;

        if ($newStatus === $previousStatus) {
            if ($note) {
                ProductionLog::create([
                    'order_id' => $order->id,
                    'user_id' => $actor->id,
                    'from_status' => $order->status,
                    'to_status' => $order->status,
                    'note' => $note,
                ]);
            }

            return 'Workshop note saved.';
        }

        $order->update(['production_status' => $newStatus]);

        $logNote = 'Production status: '.($previousStatus?->label() ?? 'Not started').' → '.$newStatus->label();
        if ($note) {
            $logNote .= ' — '.$note;
        }

        ProductionLog::create([
            'order_id' => $order->id,
            'user_id' => $actor->id,
            'from_status' => $order->status,
            'to_status' => $order->status,
            'note' => $logNote,
        ]);

        return 'Production status updated to '.$newStatus->label().'.';
    }
}
