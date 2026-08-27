<?php

namespace App\Http\Requests\Admin;

use App\Support\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class FilterReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('reports.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }

    public function messages(): array
    {
        return ValidationRules::messages();
    }

    /** @return array{from: \Carbon\Carbon|null, to: \Carbon\Carbon|null} */
    public function dateRange(): array
    {
        $validated = $this->validated();

        $from = isset($validated['date_from'])
            ? \Carbon\Carbon::parse($validated['date_from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['date_to'])
            ? \Carbon\Carbon::parse($validated['date_to'])->endOfDay()
            : now()->endOfDay();

        return ['from' => $from, 'to' => $to];
    }
}
