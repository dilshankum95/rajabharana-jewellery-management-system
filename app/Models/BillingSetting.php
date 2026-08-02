<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingSetting extends Model
{
    protected $fillable = [
        'tax_rate_percent',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tax_rate_percent' => 'decimal:2',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        return static::query()->latest('updated_at')->first()
            ?? static::create(['tax_rate_percent' => 0]);
    }

    public static function currentTaxRate(): float
    {
        return (float) static::current()->tax_rate_percent;
    }

    public static function updateTaxRate(float $rate, int $userId): self
    {
        $setting = static::current();
        $setting->update([
            'tax_rate_percent' => $rate,
            'updated_by' => $userId,
        ]);

        return $setting->fresh();
    }
}
