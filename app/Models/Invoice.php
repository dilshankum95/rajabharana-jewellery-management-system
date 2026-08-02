<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'order_id',
        'customer_id',
        'subtotal',
        'making_charge',
        'discount',
        'tax',
        'grand_total',
        'invoice_status',
        'issue_date',
        'due_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'making_charge' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd').'-';
        $latest = self::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d{4})$/', $latest, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        do {
            $number = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (self::where('invoice_number', $number)->exists());

        return $number;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isEditable(): bool
    {
        return $this->invoice_status->isEditable();
    }

    public function isIssued(): bool
    {
        return ! in_array($this->invoice_status, [InvoiceStatus::Draft, InvoiceStatus::Cancelled], true);
    }

    public function recalculateGrandTotal(): void
    {
        $this->grand_total = max(
            0,
            round(
                (float) $this->subtotal
                + (float) $this->making_charge
                + (float) $this->tax
                - (float) $this->discount,
                2
            )
        );
    }

    /** Amount paid — placeholder until Payment module (M9). */
    public function getAmountPaidAttribute(): float
    {
        return 0.0;
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, round((float) $this->grand_total - $this->amount_paid, 2));
    }

    public function buildLineDescriptionForOrder(Order $order): string
    {
        $parts = [$order->item_type_label];

        if ($order->item_name) {
            $parts[] = $order->item_name;
        }

        if ($order->catalogDesign) {
            $parts[] = $order->catalogDesign->name.' ('.$order->catalogDesign->code.')';
        } elseif ($order->design_type->value === 'custom') {
            $parts[] = 'Custom design';
        }

        if ($order->gold_quality) {
            $parts[] = $order->gold_quality_label;
        }

        if ($order->weight_grams) {
            $parts[] = number_format((float) $order->weight_grams, 2).'g';
        }

        return Str::limit(implode(' · ', $parts), 255, '');
    }
}
