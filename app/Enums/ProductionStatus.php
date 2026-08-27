<?php

namespace App\Enums;

enum ProductionStatus: string
{
    case ProductionConfirm = 'production_confirm';
    case InProduction = 'in_production';
    case QualityCheck = 'quality_check';
    case ReadyToPickup = 'ready_to_pickup';

    public function label(): string
    {
        return config('jewellery.production_statuses.'.$this->value, $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::ProductionConfirm => 'bg-sky-50 text-sky-700/90',
            self::InProduction => 'bg-violet-50 text-violet-700/90',
            self::QualityCheck => 'bg-indigo-50 text-indigo-700/90',
            self::ReadyToPickup => 'bg-emerald-50 text-emerald-700/90',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::ProductionConfirm => self::InProduction,
            self::InProduction => self::QualityCheck,
            self::QualityCheck => self::ReadyToPickup,
            self::ReadyToPickup => null,
        };
    }

    public function previous(): ?self
    {
        return match ($this) {
            self::ProductionConfirm => null,
            self::InProduction => self::ProductionConfirm,
            self::QualityCheck => self::InProduction,
            self::ReadyToPickup => self::QualityCheck,
        };
    }

    public static function orderedSteps(): array
    {
        return [
            self::ProductionConfirm,
            self::InProduction,
            self::QualityCheck,
            self::ReadyToPickup,
        ];
    }
}
