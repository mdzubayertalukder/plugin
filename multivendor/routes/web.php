<?php

use Illuminate\Support\Facades\Route;
use Plugin\Multivendor\Http\Controllers\Seller\AuthController;
use Plugin\Multivendor\Http\Controllers\Seller\ShopController;
use Plugin\Multivendor\Http\Controllers\Seller\OrderController;
use Plugin\Multivendor\Http\Controllers\Admin\PaymentController;
use Plugin\Multivendor\Http\Controllers\Seller\RefundController;
use Plugin\Multivendor\Http\Controllers\Seller\ReviewController;
use Plugin\Multivendor\Http\Controllers\Admin\SettingsController;
use Plugin\Multivendor\Http\Controllers\Seller\EarningController;
use Plugin\Multivendor\Http\Controllers\Seller\ProductController;
use Plugin\Multivendor\Http\Controllers\Seller\DashboardController;
use Plugin\Multivendor\Http\Controllers\Admin\OrderController as AdminOrderController;
use Plugin\Multivendor\Http\Controllers\Admin\SellerController as AdminSellerController;
use Plugin\Multivendor\Http\Controllers\Admin\ProductController as AdminProductController;

/**
 * Admin  panel Routes
 * 
 */
Route::group(['prefix' => getAdminPrefix(), 'middleware' => 'auth'], function () {

    //Seller List
    Route::middleware(['can:Manage Sellers'])->group(function () {
        Route::get('/sellers', [AdminSellerController::class, 'sellerList'])->name('plugin.multivendor.admin.seller.list');
        Route::get('/seller-details/{id}', [AdminSellerController::class, 'sellerDetails'])->name('plugin.multivendor.admin.seller.details');
        Route::post('/delete-seller', [AdminSellerController::class, 'deleteSeller'])->name('plugin.multivendor.admin.seller.delete');
        Route::post('/update-seller', [AdminSellerController::class, 'updateSeller'])->name('plugin.multivendor.admin.seller.update');
        Route::post('/update-seller-shop', [AdminSellerController::class, 'updateSellerShop'])->name('plugin.multivendor.admin.seller.shop.update');
        Route::get('/seller-dropdown-options', [AdminSellerController::class, 'sellerDropdownList'])->name('plugin.multivendor.admin.seller.dropdown.list');
        Route::post('/update-seller-status', [AdminSellerController::class, 'updateSellerStatus'])->name('plugin.multivendor.admin.seller.list.change.status');
        Route::post('/update-seller-shop-status', [AdminSellerController::class, 'updateSellerShopStatus'])->name('plugin.multivendor.admin.seller.list.change.shop.status');
    });
    /**
     * Seller Products
     */
    Route::middleware(['can:Manage Seller Products'])->group(function () {
        Route::get('/seller-products', [AdminProductController::class, 'sellerProducts'])->name('plugin.multivendor.admin.seller.products.list');
    });

    /**
     * Seller Order
     */
    Route::middleware(['can:Manage Seller Orders'])->group(function () {
        Route::get('/seller-orders', [AdminOrderController::class, 'sellerOrders'])->name('plugin.multivendor.admin.seller.order.list');
    });
    /**
     * Seller Payouts
     */
    Route::middleware(['can:Manage Payouts'])->group(function () {
        Route::get('/seller-payouts', [PaymentController::class, 'payouts'])->name('plugin.multivendor.admin.seller.payouts.list');
    });
    Route::middleware(['can:Manage Earning History'])->group(function () {
        Route::get('/seller-earnings', [PaymentController::class, 'sellerEarnings'])->name('plugin.multivendor.admin.seller.earning.list');
    });
    Route::middleware(['can:Manage Payouts Requests'])->group(function () {
        Route::get('/seller-payout-requests', [PaymentController::class, 'payoutRequest'])->name('plugin.multivendor.admin.seller.payout.requests.list');
        Route::post('/seller-payout-requests-details', [PaymentController::class, 'payoutRequestDetails'])->name('plugin.multivendor.admin.seller.payout.requests.details');
        Route::post('/update-seller-payout-requests', [PaymentController::class, 'updatePayoutRequestStatus'])->name('plugin.multivendor.admin.seller.payout.requests.status.update');
    });
    /**
     * Seller Settings
     */
    Route::middleware(['can:Manage Seller Settings'])->group(function () {
        Route::get('/seller-settings', [SettingsController::class, 'sellerSettings'])->name('plugin.multivendor.admin.seller.settings');
        Route::post('/update-seller-settings', [SettingsController::class, 'sellerSettingsUpdate'])->name('plugin.multivendor.admin.seller.settings.update');
    });
});


Route::group(['prefix' => 'seller'], function () {

    //Seller Authentication
    Route::get('/login', [AuthController::class, 'login'])->name('plugin.multivendor.seller.login.page');
    Route::post('/login-attempt', [AuthController::class, 'loginAttempt'])->name('plugin.multivendor.seller.login.attempt');
    Route::get('/logout', [AuthController::class, 'logout'])->name('plugin.multivendor.seller.logout');
    Route::get('/generate-password-reset-link', [AuthController::class, 'passwordResetLinkForm'])->name('plugin.multivendor.seller.password.reset.link.page');
    Route::post('/send-password-reset-link', [AuthController::class, 'sendPasswordResetLink'])->name('plugin.multivendor.seller.password.reset.link.send');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('plugin.multivendor.seller.password.reset');
    Route::post('/password-reset', [AuthController::class, 'storeNewPassword'])->name('plugin.multivendor.seller.password.reset.update');

    Route::group(['middleware' => 'auth.seller'], function () {

        Route::get('/profile', [AuthController::class, 'profile'])->name('plugin.multivendor.seller.profile.page');
        Route::post('/update-profile', [AuthController::class, 'profileUpdate'])->name('plugin.multivendor.seller.profile.update');
        //Seller Dashboard
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('plugin.multivendor.seller.dashboard');
        //Products
        Route::get('/products', [ProductController::class, 'products'])->name('plugin.multivendor.seller.dashboard.products.list');
        Route::post('/view-product-quick-action-modal', [ProductController::class, 'viewProductQuickActionForm'])->name('plugin.multivendor.seller.dashboard.product.quick.action.modal.view');
        Route::post('/product-quick-discount-update', [ProductController::class, 'updateProductDiscount'])->name('plugin.multivendor.seller.dashboard.product.quick.update.discount');
        Route::post('/product-quick-price-update', [ProductController::class, 'updateProductPrice'])->name('plugin.multivendor.seller.dashboard.product.quick.update.price');
        Route::post('/product-quick-stock-update', [ProductController::class, 'updateProductStock'])->name('plugin.multivendor.seller.dashboard.product.quick.update.stock');
        Route::post('/product-bulk-action', [ProductController::class, 'productBulkAction'])->name('plugin.multivendor.seller.dashboard.product.bulk.action');
        Route::get('/add-new-product', [ProductController::class, 'addNewProduct'])->name('plugin.multivendor.seller.dashboard.products.add')->middleware('check.subscription');
        Route::post('/store-new-product', [ProductController::class, 'storeNewProduct'])->name('plugin.multivendor.seller.dashboard.products.store');
        Route::get('/product-edit/{id}', [ProductController::class, 'editProduct'])->name('plugin.multivendor.seller.dashboard.products.edit');
        Route::post('/update-product', [ProductController::class, 'updateProduct'])->name('plugin.multivendor.seller.dashboard.products.update');
        Route::post('/delete-product', [ProductController::class, 'deleteProduct'])->name('plugin.multivendor.seller.dashboard.product.delete');
        Route::post('/update-product-status', [ProductController::class, 'updateProductStatus'])->name('plugin.multivendor.seller.dashboard.product.status.update');
        Route::post('/get-shipping-zone-list', [ProductController::class, 'shippingZoneLists'])->name('plugin.multivendor.seller.dashboard.product.shipping.zones');
        /**
         * Orders
         */
        Route::get('orders', [OrderController::class, 'ordersList'])->name('plugin.multivendor.seller.dashboard.order.list');
        Route::post('/order-status-details', [OrderController::class, 'orderStatusDetails'])->name('plugin.multivendor.seller.dashboard.order.status.details');
        Route::get('order-details/{id}', [OrderController::class, 'orderDetails'])->name('plugin.multivendor.seller.dashboard.order.details');
        Route::post('/update-order-status', [OrderController::class, 'updateOrderStatus'])->name('plugin.multivendor.seller.dashboard.order.status.update');
        Route::post('/accept-order', [OrderController::class, 'acceptOrder'])->name('plugin.multivendor.seller.dashboard.order.accept');
        Route::post('/cancel-order', [OrderController::class, 'cancelOrder'])->name('plugin.multivendor.seller.dashboard.order.cancel');
        Route::post('/cancel-order-item', [OrderController::class, 'cancelOrderItem'])->name('plugin.multivendor.seller.dashboard.order.item.cancel');
        Route::post('/sales-chart-report', [OrderController::class, 'salesChartReport'])->name('plugin.multivendor..seller.reports.sales.chart');
        /**
         * Order refund
         */

        Route::get('refunds', [RefundController::class, 'refundList'])->name('plugin.multivendor.seller.dashboard.order.refund.list');
        Route::get('/refund-request-details/{id}', [RefundController::class, 'refundRequestDetails'])->name('plugin.multivendor.seller.dashboard.refund.request.details');
        Route::post('/refund-request-quick-view', [RefundController::class, 'refundRequestQuickView'])->name('plugin.multivendor.seller.dashboard.refund.request.quick.view');
        Route::post('/update-refund-request-status', [RefundController::class, 'updateRefundRequestStatus'])->name('plugin.multivendor.seller.dashboard.refund.request.status.update');

        /**
         * Shop settings
         */
        Route::get('shop-settings', [ShopController::class, 'shopSettings'])->name('plugin.multivendor.seller.dashboard.shop.settings');
        Route::post('update-shop-settings', [ShopController::class, 'updateShopSettings'])->name('plugin.multivendor.seller.dashboard.shop.settings.update');
        Route::post('update-shop-seo-settings', [ShopController::class, 'updateShopSeoSettings'])->name('plugin.multivendor.seller.dashboard.shop.seo.settings.update');
        /**
         * Reviews
         */
        Route::get('reviews', [ReviewController::class, 'reviews'])->name('plugin.multivendor.seller.dashboard.reviews.list');

        /**
         * Pickup location
         */
        Route::get('pickup-location', [ReviewController::class, 'reviews'])->name('plugin.multivendor.seller.dashboard.reviews.list');

        /**
         * Earning
         */
        Route::get('payout-requests', [EarningController::class, 'payoutRequests'])->name('plugin.multivendor.seller.dashboard.earning.payout.requests');
        Route::get('payouts', [EarningController::class, 'payouts'])->name('plugin.multivendor.seller.dashboard.earning.payouts');
        Route::post('payout-request-send', [EarningController::class, 'payoutRequestsSend'])->name('plugin.multivendor.seller.dashboard.earning.payout.requests.send');
        Route::get('payout-settings', [EarningController::class, 'payoutSettings'])->name('plugin.multivendor.seller.dashboard.earning.payout.settings');
        Route::post('update-payout-settings', [EarningController::class, 'updatePayoutSettings'])->name('plugin.multivendor.seller.dashboard.earning.payout.settings.update');
        Route::get('earning', [EarningController::class, 'sellerEarnings'])->name('plugin.multivendor.seller.dashboard.earning.history');
    });
});
