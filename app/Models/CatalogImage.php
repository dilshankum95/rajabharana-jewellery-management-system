<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogImage extends Model
{
    protected $fillable = [
        'catalog_design_id',
        'image_path',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function catalogDesign(): BelongsTo
    {
        return $this->belongsTo(CatalogDesign::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->image_path);
    }
}
