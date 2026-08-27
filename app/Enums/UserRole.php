<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';
    case Technician = 'technician';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Admin => 'Administrator',
            self::Manager => 'Inventory Manager',
            self::Staff => 'Sales Staff',
            self::Technician => 'Workshop Technician',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Customer => 'Places and tracks jewellery orders online.',
            self::Admin => 'Full system access including staff account management.',
            self::Manager => 'Manages the jewellery catalog (items, stock status, and product images).',
            self::Staff => 'Processes orders and views customer and catalog information.',
            self::Technician => 'Works on assigned production jobs and updates workshop progress.',
        };
    }

    /** @return list<UserRole> */
    public static function panelRoles(): array
    {
        return array_map(
            fn (string $value) => self::from($value),
            config('rbac.panel_roles', [])
        );
    }

    /** @return list<UserRole> */
    public static function assignableRoles(): array
    {
        return array_map(
            fn (string $value) => self::from($value),
            config('rbac.assignable_roles', [])
        );
    }

    public function isPanelRole(): bool
    {
        return in_array($this->value, config('rbac.panel_roles', []), true);
    }

    public function isTechnician(): bool
    {
        return $this === self::Technician;
    }

    public function isManagedStaffAccount(): bool
    {
        return $this->isPanelRole() || $this === self::Technician;
    }
}
