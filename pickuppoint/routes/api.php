<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugin\PickupPoint\Http\Controllers\Api\PickupPointController;

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

/**
 * Pickup points apis
 * 
 * @version v1
 * /api/v1/pickup-points
 */
Route::group(['prefix' => '/v1/pickup-points'], function () {
    Route::post('/active-list', [PickupPointController::class, 'activePickupPoints']);
});
