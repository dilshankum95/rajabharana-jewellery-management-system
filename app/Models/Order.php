<?php

namespace App\Models;

use App\Enums\DesignType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'assigned_technician_id',
        'assigned_at',
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
            'assigned_at' => 'datetime',
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

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function productionLogs(): HasMany
    {
        return $this->hasMany(ProductionLog::class)->latest();
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

    /** @return array<int, OrderStatus> */
    public static function activeDeliveryStatuses(): array
    {
        return [
            OrderStatus::Confirmed,
            OrderStatus::InProduction,
            OrderStatus::QualityCheck,
            OrderStatus::Ready,
        ];
    }

    public function isActiveForDeliveryTracking(): bool
    {
        return in_array($this->status, self::activeDeliveryStatuses(), true);
    }

    public function isDeliveryOverdue(): bool
    {
        return $this->isActiveForDeliveryTracking()
            && $this->expected_delivery_date->lt(today());
    }

    public function isDeliveryDueSoon(?int $withinDays = null): bool
    {
        if (! $this->isActiveForDeliveryTracking() || $this->isDeliveryOverdue()) {
            return false;
        }

        $withinDays = $withinDays ?? (int) config('jewellery.delivery_reminder_days', 3);

        return $this->expected_delivery_date->gte(today())
            && $this->expected_delivery_date->lte(today()->addDays($withinDays));
    }

    public function deliveryAlertType(): ?string
    {
        if ($this->isDeliveryOverdue()) {
            return 'overdue';
        }

        if ($this->isDeliveryDueSoon()) {
            return 'due_soon';
        }

        return null;
    }

    public function deliveryAlertMessage(): ?string
    {
        $date = $this->expected_delivery_date->format('M d, Y');

        return match ($this->deliveryAlertType()) {
            'overdue' => "Expected delivery was {$date}. This order is not finished yet — update the delivery date or expedite production.",
            'due_soon' => "Expected delivery is {$date} (within ".config('jewellery.delivery_reminder_days', 3).' days). Confirm the workshop can meet this deadline.',
            default => null,
        };
    }

    public function scopeActiveForDelivery($query)
    {
        return $query->whereIn('status', array_map(
            fn (OrderStatus $status) => $status->value,
            self::activeDeliveryStatuses()
        ));
    }

    public function scopeDeliveryOverdue($query)
    {
        return $query->activeForDelivery()
            ->whereDate('expected_delivery_date', '<', today());
    }

    public function scopeDeliveryDueSoon($query, ?int $withinDays = null)
    {
        $withinDays = $withinDays ?? (int) config('jewellery.delivery_reminder_days', 3);

        return $query->activeForDelivery()
            ->whereDate('expected_delivery_date', '>=', today())
            ->whereDate('expected_delivery_date', '<=', today()->addDays($withinDays));
    }

    public function scopeNeedsDeliveryAttention($query, ?int $withinDays = null)
    {
        $withinDays = $withinDays ?? (int) config('jewellery.delivery_reminder_days', 3);

        return $query->activeForDelivery()
            ->whereDate('expected_delivery_date', '<=', today()->addDays($withinDays));
    }

    /** @return array<int, OrderStatus> */
    public static function technicianAssignableStatuses(): array
    {
        return [
            OrderStatus::Confirmed,
            OrderStatus::InProduction,
            OrderStatus::QualityCheck,
        ];
    }

    /** @return array<int, OrderStatus> */
    public static function technicianUpdatableStatuses(): array
    {
        return [
            OrderStatus::InProduction,
            OrderStatus::QualityCheck,
            OrderStatus::Ready,
        ];
    }

    public function isAssignableToTechnician(): bool
    {
        return in_array($this->status, self::technicianAssignableStatuses(), true);
    }

    public function isAssignedTo(User $technician): bool
    {
        return $this->assigned_technician_id === $technician->id;
    }

    public function technicianCanUpdate(User $technician): bool
    {
        return $this->isAssignedTo($technician)
            && in_array($this->status, [
                OrderStatus::Confirmed,
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
            ], true);
    }

    public function isValidTechnicianStatusTransition(OrderStatus $newStatus): bool
    {
        if (! in_array($newStatus, self::technicianUpdatableStatuses(), true)) {
            return false;
        }

        $allowed = match ($this->status) {
            OrderStatus::Confirmed => [
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
                OrderStatus::Ready,
            ],
            OrderStatus::InProduction => [
                OrderStatus::QualityCheck,
                OrderStatus::Ready,
            ],
            OrderStatus::QualityCheck => [
                OrderStatus::InProduction,
                OrderStatus::Ready,
            ],
            default => [],
        };

        return in_array($newStatus, $allowed, true);
    }

    public function scopeAssignedToTechnician($query, int $technicianId)
    {
        return $query->where('assigned_technician_id', $technicianId);
    }

    public function scopeActiveProduction($query)
    {
        return $query->whereIn('status', array_map(
            fn (OrderStatus $status) => $status->value,
            [
                OrderStatus::Confirmed,
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
            ]
        ));
    }

    public function scopeInProductionQueue($query)
    {
        return $query->whereIn('status', array_map(
            fn (OrderStatus $status) => $status->value,
            [
                OrderStatus::Confirmed,
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
                OrderStatus::Ready,
            ]
        ));
    }

    public function scopeNeedsTechnicianAssignment($query)
    {
        return $query->whereNull('assigned_technician_id')
            ->whereIn('status', array_map(
                fn (OrderStatus $status) => $status->value,
                self::technicianAssignableStatuses()
            ));
    }
}
