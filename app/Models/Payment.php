<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_method',
        'payment_amount',
        'payment_status',
        'payment_date',
        'transaction_reference',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_status' => PaymentStatus::class,
            'payment_date' => 'date',
            'payment_amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method', 'code');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', PaymentStatus::Completed);
    }
}
