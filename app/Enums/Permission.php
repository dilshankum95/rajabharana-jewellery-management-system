<?php

namespace App\Enums;

enum Permission: string
{
    case DashboardView = 'dashboard.view';
    case OrdersView = 'orders.view';
    case OrdersManage = 'orders.manage';
    case CustomersView = 'customers.view';
    case CatalogView = 'catalog.view';
    case CatalogManage = 'catalog.manage';
    case MetalPricesManage = 'metal-prices.manage';
    case UsersManage = 'users.manage';
    case ProductionView = 'production.view';
    case ProductionAssign = 'production.assign';
    case ProductionManage = 'production.manage';

    public function label(): string
    {
        return match ($this) {
            self::DashboardView => 'View dashboard',
            self::OrdersView => 'View orders',
            self::OrdersManage => 'Manage orders',
            self::CustomersView => 'View customers',
            self::CatalogView => 'View catalog',
            self::CatalogManage => 'Manage catalog',
            self::MetalPricesManage => 'Manage metal prices',
            self::UsersManage => 'Manage staff accounts',
            self::ProductionView => 'View production queue',
            self::ProductionAssign => 'Assign technicians to orders',
            self::ProductionManage => 'Manage assigned production jobs',
        };
    }
}
