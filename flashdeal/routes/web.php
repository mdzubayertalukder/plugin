<?php

use Illuminate\Support\Facades\Route;
use Plugin\Flashdeal\Http\Controllers\FlashDealController;

Route::group(['prefix' => getAdminPrefix()], function () {
    Route::middleware(['can:Manage Flash Deals'])->group(function () {
        Route::get('/flash-deals', [FlashDealController::class, 'deals'])->name('plugin.flashdeal.list');
        Route::get('/new-flash-deal', [FlashDealController::class, 'newDeal'])->name('plugin.flashdeal.add.new');
        Route::post('/store-new-flash-deal', [FlashDealController::class, 'storeNewDeal'])->name('plugin.flashdeal.store.new');
        Route::get('/edit-flash-deal/{id}', [FlashDealController::class, 'editDeal'])->name('plugin.flashdeal.edit');
        Route::post('/update-flash-deal', [FlashDealController::class, 'updateDeal'])->name('plugin.flashdeal.update');
        Route::post('/delete-flash-deal', [FlashDealController::class, 'deleteDeal'])->name('plugin.flashdeal.delete')->middleware('demo');
        Route::post('/update-flash-deal-status', [FlashDealController::class, 'updateDealStatus'])->name('plugin.flashdeal.update.status');
        Route::post('/delete-bulk-flash-deal', [FlashDealController::class, 'deleteBulkDeal'])->name('plugin.flashdeal.delete.bulk')->middleware('demo');

        Route::get('/flash-deal-product/{id}', [FlashDealController::class, 'dealProducts'])->name('plugin.flashdeal.products');
        Route::post('/store-flash-deal-product', [FlashDealController::class, 'storeDealProducts'])->name('plugin.flashdeal.products.store');
        Route::post('/remove-deal-product', [FlashDealController::class, 'removeDealProduct'])->name('plugin.flashdeal.products.remove')->middleware('demo');
        Route::post('/bulk-remove-deal-product', [FlashDealController::class, 'removeDealProductBulk'])->name('plugin.flashdeal.products.remove.bulk')->middleware('demo');
        Route::post('/update-deal-product', [FlashDealController::class, 'updateDealProduct'])->name('plugin.flashdeal.products.update');
    });
});
