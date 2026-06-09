<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case InProduction = 'in_production';
    case QualityCheck = 'quality_check';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return config('jewellery.order_statuses.'.$this->value, $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700/90',
            self::Confirmed => 'bg-sky-50 text-sky-700/90',
            self::InProduction => 'bg-violet-50 text-violet-700/90',
            self::QualityCheck => 'bg-indigo-50 text-indigo-700/90',
            self::Ready => 'bg-emerald-50 text-emerald-700/90',
            self::Delivered => 'bg-stone-100 text-stone-600',
            self::Cancelled => 'bg-rose-50 text-rose-700/90',
        };
    }
}
