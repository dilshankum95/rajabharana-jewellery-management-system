<?php

namespace App\Models;

use App\Enums\DesignType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'design_type',
        'catalog_design_id',
        'reference_image_path',
        'item_type',
        'item_name',
        'size',
        'weight_grams',
        'specifications',
        'gold_quality',
        'gemstone_type',
        'gemstone_details',
        'quantity',
        'special_instructions',
        'expected_delivery_date',
        'contact_phone',
        'delivery_address',
        'status',
        'estimated_price',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'design_type' => DesignType::class,
            'status' => OrderStatus::class,
            'expected_delivery_date' => 'date',
            'weight_grams' => 'decimal:2',
            'estimated_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'RJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogDesign(): BelongsTo
    {
        return $this->belongsTo(CatalogDesign::class);
    }

    public function getReferenceImageUrlAttribute(): ?string
    {
        return $this->reference_image_path
            ? asset('storage/'.$this->reference_image_path)
            : null;
    }

    public function getGoldQualityLabelAttribute(): string
    {
        return config('jewellery.gold_qualities.'.$this->gold_quality, $this->gold_quality);
    }

    public function getItemTypeLabelAttribute(): string
    {
        return config('jewellery.item_types.'.$this->item_type, $this->item_type);
    }

    public function getCatalogUnitPriceAttribute(): ?float
    {
        return $this->catalogDesign?->selling_price !== null
            ? (float) $this->catalogDesign->selling_price
            : null;
    }

    public function hasPrice(): bool
    {
        return $this->estimated_price !== null;
    }
}
