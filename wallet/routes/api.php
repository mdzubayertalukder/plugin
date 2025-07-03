<?php

use Illuminate\Support\Facades\Route;
use Plugin\Wallet\Http\Controllers\WalletApiController;

Route::group(['prefix' => 'wallet/v1'], function () {

    Route::get('payment-methods', [WalletApiController::class, 'paymentMethods']);

    Route::group(['middleware' => 'auth:jwt-customer'], function () {

        Route::post('store-offline-payment', [WalletApiController::class, 'storeOfflinePayment']);
        Route::post('generate-online-wallet-recharge-link', [WalletApiController::class, 'onlineWalletRecharge']);
        Route::post('customer-wallet-transaction', [WalletApiController::class, 'customerWalletTransaction']);
        Route::post('customer-wallet-summary', [WalletApiController::class, 'customerWalletSummary']);
    });
});
