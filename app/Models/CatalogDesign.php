<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CatalogDesign extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'gold_quality',
        'weight_grams',
        'selling_price',
        'availability_status',
    ];

    protected function casts(): array
    {
        return [
            'weight_grams' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'availability_status' => AvailabilityStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CatalogDesign $design) {
            if (empty($design->code)) {
                $design->code = self::generateItemCode();
            }
        });
    }

    public static function generateItemCode(): string
    {
        do {
            $code = 'RJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CatalogImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(CatalogImage::class)->where('is_primary', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        $primary = $this->relationLoaded('images')
            ? $this->images->firstWhere('is_primary', true) ?? $this->images->first()
            : $this->images()->where('is_primary', true)->first() ?? $this->images()->first();

        return $primary?->url;
    }

    public function getGoldQualityLabelAttribute(): string
    {
        return config('jewellery.catalog_gold_qualities.'.$this->gold_quality, strtoupper($this->gold_quality));
    }

    public function getCategoryLabelAttribute(): string
    {
        return config('jewellery.catalog_categories.'.$this->category, $this->category);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', AvailabilityStatus::Available);
    }

    /** @deprecated Use scopeAvailable */
    public function scopeActive($query)
    {
        return $this->scopeAvailable($query);
    }

    public function isAvailable(): bool
    {
        return $this->availability_status === AvailabilityStatus::Available;
    }
}
