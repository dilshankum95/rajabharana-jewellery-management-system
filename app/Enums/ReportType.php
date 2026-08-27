<?php

namespace App\Enums;

enum ReportType: string
{
    case OrderSummary = 'order_summary';
    case SalesRevenue = 'sales_revenue';
    case Customer = 'customer';
    case Production = 'production';
    case Delivery = 'delivery';
    case Inventory = 'inventory';
    case BillingCollection = 'billing_collection';

    public function label(): string
    {
        return match ($this) {
            self::OrderSummary => 'Order Summary',
            self::SalesRevenue => 'Sales & Revenue',
            self::Customer => 'Customer Report',
            self::Production => 'Production / Workshop',
            self::Delivery => 'Delivery Performance',
            self::Inventory => 'Catalog Report',
            self::BillingCollection => 'Billing & Collection',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OrderSummary => 'Orders placed in the selected period with status breakdown.',
            self::SalesRevenue => 'Invoiced amounts, payments received, and outstanding balances.',
            self::Customer => 'Customers and their order activity in the period.',
            self::Production => 'Workshop jobs by technician and production stage.',
            self::Delivery => 'Overdue, due soon, and completed deliveries.',
            self::Inventory => 'Category-wise catalog summary with item counts, stock value, and item-level detail.',
            self::BillingCollection => 'Invoice status and payment collection summary.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::OrderSummary => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            self::SalesRevenue => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            self::Customer => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            self::Production => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            self::Delivery => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            self::Inventory => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
            self::BillingCollection => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        };
    }

    public function usesDateRange(): bool
    {
        return $this !== self::Inventory;
    }

    /** @return list<UserRole> */
    public function allowedRoles(): array
    {
        return match ($this) {
            self::Inventory => [UserRole::Admin, UserRole::Manager, UserRole::Staff],
            default => [UserRole::Admin, UserRole::Staff],
        };
    }

    public function isAllowedFor(UserRole $role): bool
    {
        return in_array($role, $this->allowedRoles(), true);
    }

    /** @return list<self> */
    public static function forRole(UserRole $role): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $type) => $type->isAllowedFor($role)
        ));
    }
}
