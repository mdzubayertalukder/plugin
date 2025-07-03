<?php

use Illuminate\Support\Facades\Route;
use Plugin\Multivendor\Http\Controllers\Api\ShopController;
use Plugin\Multivendor\Http\Controllers\Api\SellerAuthenticationController;

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

Route::group(['prefix' => 'v1/multivendor'], function () {
    //Seller auth
    Route::post('seller-registration', [SellerAuthenticationController::class, 'sellerRegistration']);
    Route::post('seller-shop-availability-checking', [SellerAuthenticationController::class, 'shopAvailabilityCheck']);

    //Seller Shop
    Route::post('active-shop-list', [ShopController::class, 'activeShopList']);
    Route::post('seller-shop-details', [ShopController::class, 'shopDetails']);
    Route::post('shop-products', [ShopController::class, 'shopProducts']);
    Route::post('shop-all-products', [ShopController::class, 'shopAllProducts']);
    Route::post('shop-all-reviews', [ShopController::class, 'shopAllReviews']);
    Route::post('top-seller-list', [ShopController::class, 'topSellerList']);

    //Followers
    Route::post('store-shop-follower', [ShopController::class, 'storeShopFollower'])->middleware('auth:jwt-customer');
});
