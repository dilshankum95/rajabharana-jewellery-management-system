<?php

use App\Enums\Permission;
use App\Enums\UserRole;

return [

    /*
    |--------------------------------------------------------------------------
    | Role → Permission map
    |--------------------------------------------------------------------------
    | Use '*' for full access (Administrator only).
    */
    'roles' => [
        UserRole::Admin->value => ['*'],

        UserRole::Manager->value => [
            Permission::CatalogView->value,
            Permission::CatalogManage->value,
            Permission::RawMaterialsView->value,
            Permission::RawMaterialsManage->value,
            Permission::ReportsView->value,
            Permission::ReportsExport->value,
        ],

        UserRole::Staff->value => [
            Permission::DashboardView->value,
            Permission::OrdersView->value,
            Permission::OrdersManage->value,
            Permission::CustomersView->value,
            Permission::CatalogView->value,
            Permission::BillingView->value,
            Permission::BillingManage->value,
            Permission::ReportsView->value,
            Permission::ProductionView->value,
        ],

        UserRole::Technician->value => [
            Permission::ProductionManage->value,
        ],
    ],

    /** Roles that may access the admin panel */
    'panel_roles' => [
        UserRole::Admin->value,
        UserRole::Manager->value,
        UserRole::Staff->value,
    ],

    /** Roles an administrator may assign when creating staff accounts */
    'assignable_roles' => [
        UserRole::Admin->value,
        UserRole::Manager->value,
        UserRole::Staff->value,
        UserRole::Technician->value,
    ],

];
