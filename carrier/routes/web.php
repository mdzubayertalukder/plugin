<?php

use Illuminate\Support\Facades\Route;
use Plugin\Carrier\Http\Controllers\CarrierController;

Route::group(['prefix' => getAdminPrefix() . '/shipping'], function () {
    Route::group(['middleware' => 'can:Manage Carriers'], function () {
        Route::get('/carriers', [CarrierController::class, 'carriers'])->name('plugin.carrier.list');
        // Shipping Courier
        Route::post('/store-new-courier', [CarrierController::class, 'storeNewCourier'])->name('plugin.carrier.shipping.courier.store');
        Route::post('/update-courier-status', [CarrierController::class, 'updateCourierStatus'])->name('plugin.carrier.shipping.courier.status.update');
        Route::post('/delete-courier', [CarrierController::class, 'deleteCourier'])->name('plugin.carrier.shipping.courier.delete')->middleware('demo');
        Route::post('/enable-disable-courier', [CarrierController::class, 'courierModuleUpdateStatus'])->name('plugin.carrier.shipping.courier.module.status.update');
        Route::post('/edit-courier', [CarrierController::class, 'editCourier'])->name('plugin.carrier.shipping.courier.edit');
        Route::post('/update-courier', [CarrierController::class, 'updateCourier'])->name('plugin.carrier.shipping.courier.update');
    });
});
