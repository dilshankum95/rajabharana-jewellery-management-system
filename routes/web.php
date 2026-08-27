<?php

use App\Enums\Permission;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Admin\BillingSettingsController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\MetalPriceController;
use App\Http\Controllers\Admin\CatalogDesignController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderAssignmentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StaffUserController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboardController;
use App\Http\Controllers\Technician\JobController as TechnicianJobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{catalog}/purchase', [CatalogController::class, 'purchase'])->name('purchase');
Route::get('/catalog/{catalog}/purchase/login', [CatalogController::class, 'purchaseLogin'])->name('purchase.login');
Route::get('/catalog/{catalog}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/invoice', [CustomerInvoiceController::class, 'show'])->name('invoice.show');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
    });
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function (Request $request) {
        $user = $request->user();

        if ($user->hasPermission(Permission::DashboardView->value)) {
            return app(AdminDashboardController::class)($request);
        }

        if ($user->hasPermission(Permission::CatalogView->value)) {
            return redirect()->route('admin.catalog.index');
        }

        abort(403, 'You do not have permission to access the admin panel.');
    })->name('dashboard');

    Route::middleware('permission:'.Permission::OrdersView->value)->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    });

    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])
        ->middleware('permission:'.Permission::OrdersManage->value)
        ->name('orders.update');

    Route::patch('/orders/{order}/production', [AdminOrderController::class, 'updateProduction'])
        ->name('orders.update-production');

    Route::patch('/orders/{order}/assign-technician', [OrderAssignmentController::class, 'update'])
        ->middleware('permission:'.Permission::ProductionAssign->value)
        ->name('orders.assign-technician');

    Route::middleware('permission:'.Permission::ProductionView->value)->prefix('workshop')->name('workshop.')->group(function () {
        Route::get('/', [WorkshopController::class, 'index'])->name('index');
        Route::get('/technicians', [WorkshopController::class, 'technicians'])->name('technicians');
        Route::get('/technicians/{technician}', [WorkshopController::class, 'showTechnician'])->name('technicians.show');
    });

    Route::middleware('permission:'.Permission::CustomersView->value)->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });

    Route::middleware('permission:'.Permission::CatalogView->value)->group(function () {
        Route::get('/catalog', [CatalogDesignController::class, 'index'])->name('catalog.index');
    });

    Route::middleware('permission:'.Permission::CatalogManage->value)->group(function () {
        Route::get('/catalog/create', [CatalogDesignController::class, 'create'])->name('catalog.create');
        Route::post('/catalog', [CatalogDesignController::class, 'store'])->name('catalog.store');
        Route::get('/catalog/{catalog}/edit', [CatalogDesignController::class, 'edit'])->name('catalog.edit');
        Route::patch('/catalog/{catalog}', [CatalogDesignController::class, 'update'])->name('catalog.update');
        Route::delete('/catalog/{catalog}', [CatalogDesignController::class, 'destroy'])->name('catalog.destroy');
        Route::delete('/catalog/{catalog}/images/{image}', [CatalogDesignController::class, 'destroyImage'])->name('catalog.images.destroy');
        Route::patch('/catalog/{catalog}/images/{image}/primary', [CatalogDesignController::class, 'setPrimaryImage'])->name('catalog.images.primary');
    });

    Route::middleware('permission:'.Permission::RawMaterialsView->value)->prefix('raw-materials')->name('raw-materials.')->group(function () {
        Route::get('/', [RawMaterialController::class, 'index'])->name('index');
    });

    Route::middleware('permission:'.Permission::RawMaterialsManage->value)->prefix('raw-materials')->name('raw-materials.')->group(function () {
        Route::get('/create', [RawMaterialController::class, 'create'])->name('create');
        Route::post('/', [RawMaterialController::class, 'store'])->name('store');
        Route::get('/{rawMaterial}/edit', [RawMaterialController::class, 'edit'])->name('edit');
        Route::patch('/{rawMaterial}', [RawMaterialController::class, 'update'])->name('update');
        Route::delete('/{rawMaterial}', [RawMaterialController::class, 'destroy'])->name('destroy');
        Route::post('/{rawMaterial}/adjust-stock', [RawMaterialController::class, 'adjustStock'])->name('adjust-stock');
    });

    Route::middleware('permission:'.Permission::MetalPricesManage->value)->group(function () {
        Route::get('/metal-prices', [MetalPriceController::class, 'edit'])->name('metal-prices.edit');
        Route::patch('/metal-prices', [MetalPriceController::class, 'update'])->name('metal-prices.update');
    });

    Route::middleware('permission:'.Permission::UsersManage->value)->prefix('users')->name('users.')->group(function () {
        Route::get('/', [StaffUserController::class, 'index'])->name('index');
        Route::get('/create', [StaffUserController::class, 'create'])->name('create');
        Route::post('/', [StaffUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [StaffUserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [StaffUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [StaffUserController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:'.Permission::BillingView->value)->prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])
            ->middleware('permission:'.Permission::BillingManage->value)
            ->name('store');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    });

    Route::middleware('permission:'.Permission::BillingManage->value)->group(function () {
        Route::get('/orders/{order}/invoice/create', [InvoiceController::class, 'create'])->name('orders.invoice.create');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
    });

    Route::middleware('permission:'.Permission::BillingSettings->value)->group(function () {
        Route::get('/billing/settings', [BillingSettingsController::class, 'edit'])->name('billing.settings');
        Route::patch('/billing/settings', [BillingSettingsController::class, 'update'])->name('billing.settings.update');
    });

    Route::middleware('permission:'.Permission::ReportsView->value)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{type}/print', [ReportController::class, 'print'])->name('print');
        Route::get('/{type}/export', [ReportController::class, 'exportCsv'])
            ->middleware('permission:'.Permission::ReportsExport->value)
            ->name('export');
        Route::get('/{type}', [ReportController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'verified', 'technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/', TechnicianDashboardController::class)->name('dashboard');
    Route::get('/jobs/{order}', [TechnicianJobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{order}/task', [TechnicianJobController::class, 'respondToTask'])->name('jobs.task');
    Route::patch('/jobs/{order}/production', [TechnicianJobController::class, 'updateProduction'])->name('jobs.production');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
