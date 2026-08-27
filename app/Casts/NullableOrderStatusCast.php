<?php

namespace App\Casts;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<?OrderStatus, ?string> */
class NullableOrderStatusCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?OrderStatus
    {
        if ($value === null || $value === '') {
            return null;
        }

        return OrderStatus::tryFrom((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof OrderStatus) {
            return $value->value;
        }

        return OrderStatus::tryFrom((string) $value)?->value;
    }
}
