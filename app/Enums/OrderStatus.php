<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return config('jewellery.order_statuses.'.$this->value, $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700/90',
            self::Accepted => 'bg-emerald-50 text-emerald-700/90',
            self::Rejected => 'bg-rose-50 text-rose-700/90',
        };
    }
}
