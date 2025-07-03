<?php

use Illuminate\Support\Facades\Route;
use Plugin\Dropshipping\Http\Controllers\Admin\DropshippingAdminController;
use Plugin\Dropshipping\Http\Controllers\Admin\WooCommerceConfigController;
use Plugin\Dropshipping\Http\Controllers\Admin\PlanLimitController;

Route::group(['prefix' => 'admin', 'as' => 'core.dropshipping.admin.', 'middleware' => ['auth', 'admin']], function () {

    // Dashboard
    Route::get('/dropshipping', [DropshippingAdminController::class, 'dashboard'])->name('dashboard');

    // WooCommerce Configuration
    Route::group(['prefix' => 'woocommerce-config', 'as' => 'woocommerce.'], function () {
        Route::get('/', [WooCommerceConfigController::class, 'index'])->name('index');
        Route::get('/create', [WooCommerceConfigController::class, 'create'])->name('create');
        Route::post('/store', [WooCommerceConfigController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [WooCommerceConfigController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [WooCommerceConfigController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [WooCommerceConfigController::class, 'destroy'])->name('delete');
        Route::post('/test-connection', [WooCommerceConfigController::class, 'testConnection'])->name('test-connection');
        Route::post('/sync-products/{id}', [WooCommerceConfigController::class, 'syncProducts'])->name('sync-products');
    });

    // Plan Limits
    Route::group(['prefix' => 'plan-limits', 'as' => 'plan-limits.'], function () {
        Route::get('/', [PlanLimitController::class, 'index'])->name('index');
        Route::post('/update', [PlanLimitController::class, 'update'])->name('update');
    });

    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/imports', [DropshippingAdminController::class, 'importReports'])->name('imports');
        Route::get('/usage', [DropshippingAdminController::class, 'usageReports'])->name('usage');
    });

    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', [DropshippingAdminController::class, 'settings'])->name('index');
        Route::post('/update', [DropshippingAdminController::class, 'updateSettings'])->name('update');
    });
});
