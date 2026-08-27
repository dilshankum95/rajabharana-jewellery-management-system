<?php

namespace App\Enums;

enum StockMovementReason: string
{
    case ManualAdjustment = 'manual_adjustment';
    case CatalogRestock = 'catalog_restock';
    case OrderAccepted = 'order_accepted';
    case OrderRejected = 'order_rejected';
    case OrderCancelled = 'order_cancelled';
    case WorkshopUsage = 'workshop_usage';
    case MaterialReceived = 'material_received';

    public function label(): string
    {
        return match ($this) {
            self::ManualAdjustment => 'Manual adjustment',
            self::CatalogRestock => 'Catalog restock',
            self::OrderAccepted => 'Order accepted',
            self::OrderRejected => 'Order rejected',
            self::OrderCancelled => 'Order cancelled',
            self::WorkshopUsage => 'Workshop usage',
            self::MaterialReceived => 'Material received',
        };
    }
}
