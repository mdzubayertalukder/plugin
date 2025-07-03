<?php

use Illuminate\Support\Facades\Route;
use Plugin\Wallet\Http\Controllers\WalletController;

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => getAdminPrefix()], function () {
        Route::group(['prefix' => 'wallet'], function () {

            Route::get('/wallet-transactions', [WalletController::class, 'walletRecharges'])
                ->name('plugin.wallet.transaction.list')->middleware(['can:Manage Wallet Transactions']);

            Route::get('/offline-payment-methods', [WalletController::class, 'offlinePaymentMethods'])
                ->name('plugin.wallet.recharge.offline.payment.methods')
                ->middleware(['can:Manage Offline Payment Methods']);

            Route::post('/store-offline-payment-method', [WalletController::class, 'storeOfflinePaymentMethod'])->name('plugin.wallet.recharge.offline.payment.methods.store');
            Route::post('/delete-offline-payment-method', [WalletController::class, 'deleteOfflinePaymentMethod'])->name('plugin.wallet.recharge.offline.payment.methods.delete')->middleware('demo');
            Route::post('/update-offline-payment-method', [WalletController::class, 'updateOfflinePaymentMethod'])->name('plugin.wallet.recharge.offline.payment.methods.update');
            Route::post('/add-deduct-customer-wallet', [WalletController::class, 'addDeductCustomerWallet'])->name('plugin.wallet.customer.add.deduct');
            Route::post('/wallet-transaction-bulk-action', [WalletController::class, 'walletBulkAction'])->name('plugin.wallet.bulk.action');
        });
    });
});
