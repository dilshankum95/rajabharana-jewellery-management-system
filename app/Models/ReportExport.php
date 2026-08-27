<?php

namespace App\Models;

use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExport extends Model
{
    protected $fillable = [
        'report_type',
        'date_from',
        'date_to',
        'generated_by',
        'file_path',
        'format',
        'parameters',
    ];

    protected function casts(): array
    {
        return [
            'report_type' => ReportType::class,
            'date_from' => 'date',
            'date_to' => 'date',
            'parameters' => 'array',
        ];
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
