<?php

namespace App\Models;

use App\Enums\StockMovementReason;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'stockable_type',
        'stockable_id',
        'quantity_before',
        'quantity_delta',
        'quantity_after',
        'reason',
        'order_id',
        'user_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity_before' => 'decimal:3',
            'quantity_delta' => 'decimal:3',
            'quantity_after' => 'decimal:3',
            'reason' => StockMovementReason::class,
        ];
    }

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function orderDeductionExists(Order $order, CatalogDesign $design): bool
    {
        return self::query()
            ->where('order_id', $order->id)
            ->where('stockable_type', CatalogDesign::class)
            ->where('stockable_id', $design->id)
            ->where('reason', StockMovementReason::OrderAccepted)
            ->exists();
    }

    public static function orderMaterialDeductionExists(Order $order, RawMaterial $material): bool
    {
        return self::query()
            ->where('order_id', $order->id)
            ->where('stockable_type', RawMaterial::class)
            ->where('stockable_id', $material->id)
            ->where('reason', StockMovementReason::OrderAccepted)
            ->exists();
    }
}
