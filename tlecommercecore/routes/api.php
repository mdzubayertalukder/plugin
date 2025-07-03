<?php

use Illuminate\Support\Facades\Route;
use Plugin\TlcommerceCore\Http\Controllers\Api\CartController;
use Plugin\TlcommerceCore\Http\Controllers\Api\OrderController;
use Plugin\TlcommerceCore\Http\Controllers\Api\ProductController;
use Plugin\TlcommerceCore\Http\Controllers\Api\CustomerController;
use Plugin\TlcommerceCore\Http\Controllers\Api\SettingsController;
use Plugin\TlcommerceCore\Http\Controllers\Api\NotificationController;
use Plugin\TlcommerceCore\Http\Controllers\Api\CustomerAddressController;
use Plugin\TlcommerceCore\Http\Controllers\Api\CustomerWishlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Ecommerce v1
Route::group(['prefix' => 'v1/ecommerce-core'], function () {
    /**
     * Site properties
     * 
     * /api/v1/ecommerce-core
     */
    Route::get('site-properties', [SettingsController::class, 'siteProperties']);
    Route::get('phone-codes', [SettingsController::class, 'phoneCodes']);
    /**
     * Product routes
     * 
     * api/v1/ecommerce-core
     */
    Route::get('product-configuration', [ProductController::class, 'productConfiguration']);
    Route::post('products', [ProductController::class, 'products']);
    Route::post('product-details', [ProductController::class, 'productDetails']);
    Route::post('single-variant-info', [ProductController::class, 'singleVariantInfo']);
    Route::post('color-variant-images', [ProductController::class, 'colorVariantImages']);
    Route::post('related-products', [ProductController::class, 'relatedProducts']);
    Route::post('top-selling-products', [ProductController::class, 'topSellingProducts']);
    Route::post('get-product-reviews', [ProductController::class, 'productReviews']);
    Route::get('brands', [ProductController::class, 'brands']);
    Route::get('categories', [ProductController::class, 'categories']);
    Route::get('parent-categories', [ProductController::class, 'parentCategories']);
    Route::get('mega-categories', [ProductController::class, 'megaCategories']);



    Route::post('category-details', [ProductController::class, 'categoryDetails']);
    Route::post('deals-details', [ProductController::class, 'dealsDetails']);
    Route::post('deals-products', [ProductController::class, 'dealsProducts']);
    Route::post('search-suggestions', [ProductController::class, 'searchSuggestions']);
    Route::post('search-products', [ProductController::class, 'searchProducts']);
    Route::post('compare-items-details', [ProductController::class, 'compareItems']);
    /**
     * Shipping Locations
     * 
     */
    Route::get("get-countries", [OrderController::class, 'countryList']);
    Route::post("get-states-of-countries", [OrderController::class, 'countryStates']);
    Route::post("get-cities-of-state", [OrderController::class, 'stateCities']);
    /**
     * Customer auth routes 
     * 
     * /api/v1/ecommerce-core/auth
     */
    Route::group(['prefix' => 'auth'], function () {
        Route::post('customer-registration', [CustomerController::class, 'customerRegistration']);
        Route::post('verify-customer-email', [CustomerController::class, 'verifyCustomerEmail']);
        Route::post('customer-forgot-password', [CustomerController::class, 'customerForgotPassword']);
        Route::post('verify-customer-reset-password-token', [CustomerController::class, 'VerifyCustomerResetPasswordToken']);
        Route::post('customer-reset-password', [CustomerController::class, 'customerResetPassword']);
        Route::post('customer-reset-email', [CustomerController::class, 'customerResetEmail']);
        Route::post('customer-login', [CustomerController::class, 'customerLogin']);
        Route::get('customer-refresh-auth', [CustomerController::class, 'refresh']);
        Route::get('customer-logout', [CustomerController::class, 'customerLogout']);
    });

    /**
     * Customer authenticated routes
     * 
     * /api/v1/ecommerce-core/customer
     */
    Route::group(['prefix' => 'customer', 'middleware' => 'auth:jwt-customer'], function () {
        /**
         * Customer information
         * 
         * /api/v1/ecommerce-core/customer
         * 
         */
        Route::get('customer-basic-info', [CustomerController::class, 'customerBasicInfo']);
        Route::post('update-customer-basic-info', [CustomerController::class, 'updateCustomerBasicInfo']);
        Route::get('customer-email-reset-link', [CustomerController::class, 'customerEmailResetLink']);
        Route::get('customer-dashboard', [CustomerController::class, 'customerDashboardDetails']);
        Route::get('customer-summary', [CustomerController::class, 'customerSummary']);
        /**
         * Customer address
         * 
         * /api/v1/ecommerce-core/customer
         */
        Route::get('get-customer-all-address', [CustomerAddressController::class, 'customerAllAddress']);
        Route::post('get-customer-address-details', [CustomerAddressController::class, 'customerAddressDetails']);
        Route::post('store-customer-address', [CustomerAddressController::class, 'storeCustomerAddress']);
        Route::post('update-customer-address', [CustomerAddressController::class, 'updateCustomerAddress']);

        /**
         * Customer wishlist
         * 
         * /api/v1/ecommerce-core/customer
         */
        Route::post('store-product-to-wishlist', [CustomerWishlistController::class, 'storeProductToWishlist']);
        Route::post('get-customer-wishlist-product', [CustomerWishlistController::class, 'getCustomerWishlistProducts']);
        Route::post('product-remove-from-wishlist', [CustomerWishlistController::class, 'removeProductFromWishlist']);
        /**
         * Customer cart
         * 
         * /api/v1/ecommerce-core/customer/cart
         */
        Route::post('cart/store-cart-item', [CartController::class, 'storeCartProduct']);
        Route::get('cart/cart-items-list', [CartController::class, 'getCartItems']);
        Route::post('cart/remove-item', [CartController::class, 'removeCartItem']);
        Route::post('cart/update-cart-item', [CartController::class, 'updateCartItem']);
        /**
         * Customer order
         * 
         * /api/v1/ecommerce-core/customer
         */
        Route::post('order/create', [OrderController::class, 'createCustomerOrder']);
        Route::post('cancel-order', [OrderController::class, 'cancelOrder']);
        Route::post('order/details', [OrderController::class, 'customerOrderDetails']);
        Route::post('order/return', [OrderController::class, 'customerOrderReturn']);
        Route::post('return-requests', [OrderController::class, 'customerReturnRequests']);
        Route::post('make-order-payment', [OrderController::class, 'makeOrderPayment']);
        Route::post('orders', [OrderController::class, 'customerOrders']);
        Route::post('review-product', [OrderController::class, 'reviewProduct']);

        /**
         * Customer notification
         * 
         * /api/v1/ecommerce-core/customer
         */
        Route::post('notification/list', [NotificationController::class, 'customerNotifications']);
        Route::post('mark-as-read-single-notification', [NotificationController::class, 'markAsRead']);
        Route::get('mark-as-read-all-notification', [NotificationController::class, 'markAsReadAllNotification']);
    });
    /**
     * Customer checkout
     * 
     * /api/v1/ecommerce-core
     */
    //Attachment
    Route::post('upload-attachment-in-order', [OrderController::class, 'uploadOrderAttachment']);
    Route::post('remove-attachment-in-order', [OrderController::class, 'removeOrderAttachment']);

    Route::post('cart/validate-cart-items', [OrderController::class, 'validateCartItems']);
    Route::post('apply-coupon', [OrderController::class, 'applyCoupon']);
    Route::post('get-shipping-options', [OrderController::class, 'shippingOptions']);
    Route::post('active-payment-methods', [OrderController::class, 'activePaymentMethods']);
    Route::post('guest/checkout', [OrderController::class, 'guestCheckout']);
    Route::post('guest/order/details', [OrderController::class, 'guestCustomerOrderDetails']);
});
