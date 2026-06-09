<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Admin = 'admin';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Admin => 'Administrator',
            self::Staff => 'Staff',
        };
    }
}
