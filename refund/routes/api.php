<?php

use Illuminate\Support\Facades\Route;
use Plugin\Refund\Http\Controllers\RefundApiController;

Route::group(['prefix' => 'refund/v1'], function () {

    Route::get('get-refund-reasons', [RefundApiController::class, 'refundsReasons']);

    Route::group(['middleware' => 'auth:jwt-customer'], function () {
        Route::post('refund-request-details', [RefundApiController::class, 'refundRequestDetails']);
    });
});
