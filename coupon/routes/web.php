<?php

use Illuminate\Support\Facades\Route;
use Plugin\Coupon\Http\Controllers\CouponController;

Route::group(['prefix' => getAdminPrefix()], function () {
    /**
     * Marketings Modules
     */
    Route::group(['prefix' => 'marketing'], function () {
        Route::middleware(['can:Manage Coupons'])->group(function () {
            //coupon
            Route::get('/coupons', [CouponController::class, 'coupons'])->name('plugin.tlcommercecore.marketing.coupon.list');
            Route::get('/create-new-coupon', [CouponController::class, 'createNewCoupon'])->name('plugin.tlcommercecore.marketing.coupon.create.new');
            Route::post('/store-new-coupon', [CouponController::class, 'storeNewCoupon'])->name('plugin.tlcommercecore.marketing.coupon.store.new');
            Route::get('/edit-coupon/{id}', [CouponController::class, 'editCoupon'])->name('plugin.tlcommercecore.marketing.coupon.edit');
            Route::post('/update-coupon', [CouponController::class, 'updateCoupon'])->name('plugin.tlcommercecore.marketing.coupon.update');
            Route::post('/update-coupon-status', [CouponController::class, 'updateCouponStatus'])->name('plugin.tlcommercecore.marketing.coupon.update.status');
            Route::post('/delete-coupon', [CouponController::class, 'deleteCoupon'])->name('plugin.tlcommercecore.marketing.coupon.delete');
            Route::post('/delete-bulk-coupon', [CouponController::class, 'deleteBulkCoupon'])->name('plugin.tlcommercecore.marketing.coupon.bulk.delete');
        });
    });
});
