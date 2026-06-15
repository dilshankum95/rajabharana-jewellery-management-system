<?php

use App\Enums\Permission;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Admin\MetalPriceController;
use App\Http\Controllers\Admin\CatalogDesignController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderAssignmentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\StaffUserController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Customer\DashboardController;
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
});

Route::middleware(['auth', 'verified', 'technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/', TechnicianDashboardController::class)->name('dashboard');
    Route::get('/jobs/{order}', [TechnicianJobController::class, 'show'])->name('jobs.show');
    Route::patch('/jobs/{order}', [TechnicianJobController::class, 'update'])->name('jobs.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
