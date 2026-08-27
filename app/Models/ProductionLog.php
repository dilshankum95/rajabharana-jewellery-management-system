<?php

namespace App\Models;

use App\Casts\NullableOrderStatusCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionLog extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'from_status',
        'to_status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => NullableOrderStatusCast::class,
            'to_status' => NullableOrderStatusCast::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
