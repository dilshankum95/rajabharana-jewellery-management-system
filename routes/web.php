<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Admin\CatalogDesignController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('/catalog', [CatalogDesignController::class, 'index'])->name('catalog.index');
    Route::get('/catalog/create', [CatalogDesignController::class, 'create'])->name('catalog.create');
    Route::post('/catalog', [CatalogDesignController::class, 'store'])->name('catalog.store');
    Route::get('/catalog/{catalog}/edit', [CatalogDesignController::class, 'edit'])->name('catalog.edit');
    Route::patch('/catalog/{catalog}', [CatalogDesignController::class, 'update'])->name('catalog.update');
    Route::delete('/catalog/{catalog}', [CatalogDesignController::class, 'destroy'])->name('catalog.destroy');
    Route::delete('/catalog/{catalog}/images/{image}', [CatalogDesignController::class, 'destroyImage'])->name('catalog.images.destroy');
    Route::patch('/catalog/{catalog}/images/{image}/primary', [CatalogDesignController::class, 'setPrimaryImage'])->name('catalog.images.primary');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
