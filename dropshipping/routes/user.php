<?php

use Illuminate\Support\Facades\Route;
use Plugin\Dropshipping\Http\Controllers\Tenant\DropshippingTenantController;
use Plugin\Dropshipping\Http\Controllers\Tenant\ProductImportController;

Route::group(['prefix' => 'user', 'as' => 'dropshipping.', 'middleware' => ['auth', 'tenant']], function () {

    // Dashboard
    Route::get('/dropshipping', [DropshippingTenantController::class, 'dashboard'])->name('dashboard');
    Route::get('/dropshipping/products', [DropshippingTenantController::class, 'products'])->name('products');

    // Product Import
    Route::group(['prefix' => 'dropshipping/import', 'as' => 'import.'], function () {
        Route::get('/products', [ProductImportController::class, 'index'])->name('products');
        Route::post('/product/{productId}', [ProductImportController::class, 'importSingle'])->name('single');
        Route::post('/products/bulk', [ProductImportController::class, 'importBulk'])->name('bulk');
        Route::get('/history', [ProductImportController::class, 'history'])->name('history');
        Route::get('/limits', [ProductImportController::class, 'limits'])->name('limits');
    });

    // Product Management
    Route::group(['prefix' => 'dropshipping/manage', 'as' => 'manage.'], function () {
        Route::get('/imported', [DropshippingTenantController::class, 'importedProducts'])->name('imported');
        Route::post('/update-pricing/{id}', [DropshippingTenantController::class, 'updatePricing'])->name('update-pricing');
        Route::post('/sync/{id}', [DropshippingTenantController::class, 'syncProduct'])->name('sync');
        Route::delete('/remove/{id}', [DropshippingTenantController::class, 'removeProduct'])->name('remove');
    });

    // AJAX Routes
    Route::group(['prefix' => 'dropshipping/ajax', 'as' => 'ajax.'], function () {
        Route::get('/product-details/{id}', [DropshippingTenantController::class, 'getProductDetails'])->name('product-details');
        Route::get('/check-import-limit', [ProductImportController::class, 'checkImportLimit'])->name('check-limit');
        Route::post('/preview-import', [ProductImportController::class, 'previewImport'])->name('preview');
    });
});

// Tenant-specific routes (accessible within tenant context)
Route::group(['as' => 'dropshipping.tenant.', 'middleware' => ['tenant']], function () {
    Route::get('/dropshipping/dashboard', [DropshippingTenantController::class, 'dashboard'])->name('dashboard');
});
