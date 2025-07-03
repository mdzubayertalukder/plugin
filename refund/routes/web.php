<?php

use Illuminate\Support\Facades\Route;
use Plugin\Refund\Http\Controllers\RefundController;

Route::group(['prefix' => getAdminPrefix()], function () {
    /**
     * Refunds Module
     */
    Route::group(['prefix' => 'refunds'], function () {
        //Configuration
        Route::get('/configuration', [RefundController::class, 'configuration'])->name('plugin.tlcommercecore.refunds.configuration');
        Route::post('/update-configuration', [RefundController::class, 'updateConfiguration'])->name('plugin.tlcommercecore.refunds.configuration.update');
        
        //Refund reasons
        Route::middleware(['can:Manage Refund reasons'])->group(function () {
            Route::get('/reasons', [RefundController::class, 'reasons'])->name('plugin.refund.reasons.list');
            Route::post('/store-new-reason', [RefundController::class, 'storeReason'])->name('plugin.refund.reason.store');
            Route::post('/delete-reason', [RefundController::class, 'deleteReason'])->name('plugin.refund.reason.delete')->middleware('demo');
            Route::post('/delete-bulk-reason', [RefundController::class, 'deleteBulkReason'])->name('plugin.refund.reason.delete.bulk')->middleware('demo');
            Route::post('/change-reason-status', [RefundController::class, 'changeReasonStatus'])->name('plugin.refund.reason.status.change');
            Route::get('/reason-edit/{id}', [RefundController::class, 'editReason'])->name('plugin.refund.reason.edit');
            Route::post('/update-reason', [RefundController::class, 'updateReason'])->name('plugin.refund.reason.update');
        });
        
        //Refund requests management
        Route::middleware(['can:Manage Refund Requests'])->group(function () {
            Route::get('/requests', [RefundController::class, 'refundRequests'])->name('plugin.refund.requests');
            Route::get('/refund-request-details/{id}', [RefundController::class, 'refundRequestDetails'])->name('plugin.refund.request.details');
            Route::post('/refund-request-quick-view', [RefundController::class, 'refundRequestQuickView'])->name('plugin.refund.request.quick.view');
            Route::post('/update-refund-request-status', [RefundController::class, 'updateRefundRequestStatus'])->name('plugin.refund.requests.status.update');
        });
    });
});
