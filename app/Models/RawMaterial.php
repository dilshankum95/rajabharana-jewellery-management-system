<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class RawMaterial extends Model
{
    protected $fillable = [
        'name',
        'code',
        'material_type',
        'unit',
        'stock_quantity',
        'reorder_level',
        'unit_cost',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:3',
            'reorder_level' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RawMaterial $material) {
            if (empty($material->code)) {
                $material->code = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RM-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'stockable')->latest();
    }

    public function catalogDesigns(): BelongsToMany
    {
        return $this->belongsToMany(CatalogDesign::class, 'catalog_design_raw_material')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    public function getMaterialTypeLabelAttribute(): string
    {
        return config('jewellery.raw_material_types.'.$this->material_type, $this->material_type);
    }

    public function getUnitLabelAttribute(): string
    {
        return config('jewellery.stock_units.'.$this->unit, $this->unit);
    }

    public function isLowStock(): bool
    {
        if ($this->reorder_level === null) {
            return false;
        }

        return (float) $this->stock_quantity <= (float) $this->reorder_level;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereNotNull('reorder_level')
            ->whereColumn('stock_quantity', '<=', 'reorder_level');
    }
}
