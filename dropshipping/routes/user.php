<?php

use Illuminate\Support\Facades\Route;
use Plugin\Dropshipping\Http\Controllers\Tenant\DropshippingTenantController;
use Plugin\Dropshipping\Http\Controllers\Tenant\ProductImportController;

// Simple tenant routes for dropshipping
Route::group(['prefix' => 'user', 'as' => 'user.dropshipping.', 'middleware' => ['auth']], function () {

    // Main products page - shows all available products
    Route::get('/dropshipping-products', [DropshippingTenantController::class, 'allProducts'])->name('products');

    // Import single product
    Route::post('/dropshipping-products/import/{productId}', [ProductImportController::class, 'importSingle'])->name('import.single');

    // Import history
    Route::get('/dropshipping-history', [ProductImportController::class, 'history'])->name('history');

    // Dashboard (optional)
    Route::get('/dropshipping-dashboard', [DropshippingTenantController::class, 'dashboard'])->name('dashboard');
});

// Alternative routes without prefix for easier access
Route::group(['as' => 'dropshipping.', 'middleware' => ['auth']], function () {

    // Direct access routes
    Route::get('/dropshipping', [DropshippingTenantController::class, 'allProducts'])->name('products.all');
    Route::get('/dropshipping/all-products', [DropshippingTenantController::class, 'allProducts'])->name('products');
    Route::get('/dropshipping/my-products', [DropshippingTenantController::class, 'myProducts'])->name('my.products');
    Route::post('/dropshipping/import/{productId}', [ProductImportController::class, 'importSingle'])->name('import.product');
    Route::get('/dropshipping/history', [ProductImportController::class, 'history'])->name('import.history');
});
