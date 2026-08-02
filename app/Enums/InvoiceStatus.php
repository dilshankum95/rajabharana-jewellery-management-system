<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case Partial = 'partial';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Overdue = 'overdue';

    public function label(): string
    {
        return config('jewellery.invoice_statuses.'.$this->value, $this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-600',
            self::Issued => 'bg-sky-50 text-sky-700/90',
            self::Partial => 'bg-amber-50 text-amber-700/90',
            self::Paid => 'bg-emerald-50 text-emerald-700/90',
            self::Cancelled => 'bg-rose-50 text-rose-700/90',
            self::Overdue => 'bg-orange-50 text-orange-800',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }
}
