<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Completed = 'completed';
    case Pending = 'pending';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::Pending => 'Pending',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function countsTowardBalance(): bool
    {
        return $this === self::Completed;
    }
}
