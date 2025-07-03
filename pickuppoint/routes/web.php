<?php

use Illuminate\Support\Facades\Route;
use Plugin\PickupPoint\Http\Controllers\OrderController;
use Plugin\PickupPoint\Http\Controllers\PickupPointController;

Route::group(['prefix' => getAdminPrefix()], function () {
    //shipping routes
    Route::group(['prefix' => '/shipping'], function () {
        Route::group(['middleware' => 'can:Manage Pickup Points'], function () {
            //Create & Store Pickup Points
            Route::get('/create-pickup-points', [PickupPointController::class, 'createPickupPoints'])->name('plugin.pickuppoint.create.pickup.points');
            Route::post('/store-pickup-point', [PickupPointController::class, 'storePickupPoint'])->name('plugin.pickuppoint.store.pickup.point');

            //Pickup Point List
            Route::get('/pickup-points', [PickupPointController::class, 'pickupPoints'])->name('plugin.pickuppoint.pickup.points');
            Route::get('/pickup-point-list', [PickupPointController::class, 'pickupPointList'])->name('plugin.pickuppoint.pickup.point.list');

            //Delete Pickup Points
            Route::post('/delete-pickup-point', [PickupPointController::class, 'deletePickupPoint'])->name('plugin.pickuppoint.delete.pickup.point')->middleware('demo');
            Route::post('/delete-bulk-pickup-point', [PickupPointController::class, 'deleteBulkPickupPoint'])->name('plugin.pickuppoint.delete.bulk.pickup.point')->middleware('demo');

            //Edit & Update Pickup Points
            Route::get('/edit-pickup-point', [PickupPointController::class, 'editPickupPoint'])->name('plugin.pickuppoint.edit.pickup.point');
            Route::post('/update-pickup-point', [PickupPointController::class, 'updatePickupPoint'])->name('plugin.pickuppoint.update.pickup.point');
            Route::post('/update-pickup-point-status', [PickupPointController::class, 'updatePickupPointStatus'])->name('plugin.pickuppoint.update.pickup.point.status');
        });
    });
    //Pickup point order route
    Route::group(['prefix' => '/orders'], function () {
        Route::group(['middleware' => 'can:Manage Pickup Point Order'], function () {
            Route::get('/pickup-points-orders', [OrderController::class, 'orderList'])->name('plugin.pickuppoint.orders');
        });
    });
});

Route::get('/pick-up-home', function () {
    return view('plugin/pickuppoint::index');
});
