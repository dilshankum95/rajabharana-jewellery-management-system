<?php

namespace App\Enums;

enum AvailabilityStatus: string
{
    case Available = 'available';
    case OutOfStock = 'out_of_stock';

    public function label(): string
    {
        return config('jewellery.availability_statuses.'.$this->value, $this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'jewel-badge-active',
            self::OutOfStock => 'jewel-badge-inactive',
        };
    }
}
