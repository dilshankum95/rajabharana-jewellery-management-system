<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryDiscount extends Model
{
    protected $fillable = [
        'category_code',
        'discount_percent',
        'is_active',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function categoryCodeForOrder(Order $order): string
    {
        if ($order->catalogDesign?->category) {
            return $order->catalogDesign->category;
        }

        return match ($order->item_type) {
            'earrings' => 'earring',
            default => $order->item_type,
        };
    }

    public static function forOrder(Order $order): ?self
    {
        return static::query()
            ->where('category_code', static::categoryCodeForOrder($order))
            ->where('is_active', true)
            ->first();
    }

    public static function discountPercentForOrder(Order $order): float
    {
        return (float) (static::forOrder($order)?->discount_percent ?? 0);
    }

    public function getCategoryLabelAttribute(): string
    {
        return config('jewellery.catalog_categories.'.$this->category_code, ucfirst($this->category_code));
    }
}
