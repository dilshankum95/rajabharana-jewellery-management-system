<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetalPrice extends Model
{
    protected $fillable = [
        'gold_price_per_gram',
        'silver_price_per_gram',
        'price_date',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'gold_price_per_gram' => 'decimal:2',
            'silver_price_per_gram' => 'decimal:2',
            'price_date' => 'date',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): ?self
    {
        return static::query()->latest('price_date')->latest('updated_at')->first();
    }

    public static function upsertCurrent(float $gold, float $silver, int $userId): self
    {
        $existing = static::current();

        if ($existing && $existing->price_date->isToday()) {
            $existing->update([
                'gold_price_per_gram' => $gold,
                'silver_price_per_gram' => $silver,
                'updated_by' => $userId,
            ]);

            return $existing->fresh();
        }

        return static::create([
            'gold_price_per_gram' => $gold,
            'silver_price_per_gram' => $silver,
            'price_date' => today(),
            'updated_by' => $userId,
        ]);
    }
}
