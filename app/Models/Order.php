<?php

namespace App\Models;

use App\Enums\DesignType;
use App\Enums\OrderStatus;
use App\Enums\ProductionStatus;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'task_status',
        'production_status',
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
            'task_status' => TaskStatus::class,
            'production_status' => ProductionStatus::class,
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

            if (empty($order->status)) {
                $order->status = OrderStatus::Pending;
            }

            if (empty($order->task_status)) {
                $order->task_status = TaskStatus::Pending;
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

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function hasInvoice(): bool
    {
        return $this->invoice()->exists();
    }

    public function isBillable(): bool
    {
        if ($this->hasInvoice()) {
            return false;
        }

        if ($this->status !== OrderStatus::Accepted) {
            return false;
        }

        return $this->hasPrice();
    }

    public function isAssignableToTechnician(): bool
    {
        return $this->status === OrderStatus::Accepted;
    }

    public function isAssignedTo(User $technician): bool
    {
        return $this->assigned_technician_id === $technician->id;
    }

    public function technicianCanRespondToTask(User $technician): bool
    {
        return $this->isAssignedTo($technician)
            && $this->status === OrderStatus::Accepted
            && $this->task_status === TaskStatus::Pending;
    }

    public function technicianCanUpdateProduction(User $technician): bool
    {
        return $this->isAssignedTo($technician)
            && $this->isProductionUpdatable();
    }

    public function adminCanUpdateProduction(): bool
    {
        return $this->status === OrderStatus::Accepted
            && $this->task_status === TaskStatus::Accepted;
    }

    public function isProductionUpdatable(): bool
    {
        return $this->status === OrderStatus::Accepted
            && $this->task_status === TaskStatus::Accepted
            && $this->production_status !== ProductionStatus::ReadyToPickup;
    }

    public function isValidProductionTransition(?ProductionStatus $newStatus): bool
    {
        if ($newStatus === null) {
            return false;
        }

        if ($this->production_status === null) {
            return $newStatus === ProductionStatus::ProductionConfirm;
        }

        return $this->production_status->next() === $newStatus;
    }

    /** @return array<string, string> */
    public function availableProductionStatusOptions(): array
    {
        if ($this->production_status === null) {
            return [
                ProductionStatus::ProductionConfirm->value => ProductionStatus::ProductionConfirm->label(),
            ];
        }

        if ($this->production_status === ProductionStatus::ReadyToPickup) {
            return [
                ProductionStatus::ReadyToPickup->value => ProductionStatus::ReadyToPickup->label(),
            ];
        }

        $options = [
            $this->production_status->value => $this->production_status->label(),
        ];

        $next = $this->production_status->next();
        if ($next) {
            $options[$next->value] = $next->label();
        }

        return $options;
    }

    /** @return array<string, string> */
    public function adminAvailableProductionStatusOptions(): array
    {
        $options = [];

        foreach (ProductionStatus::orderedSteps() as $step) {
            $options[$step->value] = $step->label();
        }

        return $options;
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

    public function isActiveForDeliveryTracking(): bool
    {
        return $this->status === OrderStatus::Accepted
            && $this->production_status !== ProductionStatus::ReadyToPickup;
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
        return $query->where('status', OrderStatus::Accepted->value)
            ->where(function ($q) {
                $q->whereNull('production_status')
                    ->orWhere('production_status', '!=', ProductionStatus::ReadyToPickup->value);
            });
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

    public function scopeAssignedToTechnician($query, int $technicianId)
    {
        return $query->where('assigned_technician_id', $technicianId);
    }

    public function scopeOpenTechnicianJobs($query)
    {
        return $query->where('status', OrderStatus::Accepted->value)
            ->where('task_status', '!=', TaskStatus::Rejected->value);
    }

    public function scopeInProductionQueue($query)
    {
        return $query->where('status', OrderStatus::Accepted->value);
    }

    public function scopeNeedsTechnicianAssignment($query)
    {
        return $query->where('status', OrderStatus::Accepted->value)
            ->whereNull('assigned_technician_id');
    }

    public function scopeActiveProduction($query)
    {
        return $query->where('status', OrderStatus::Accepted->value)
            ->where('task_status', TaskStatus::Accepted->value)
            ->whereNotNull('production_status')
            ->where('production_status', '!=', ProductionStatus::ReadyToPickup->value);
    }
}
