<?php

use App\Http\Controllers\Reseller\OthersController;
use App\Http\Controllers\Reseller\SwitchController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('reseller')->middleware("reseller_auth")->group(function () {
    Route::post('pay', [SwitchController::class, 'payService']);
    Route::post('validate', [SwitchController::class, 'validateService']);
    Route::post('list', [SwitchController::class, 'listService']);
    Route::post('me', [SwitchController::class, 'junction']);
    Route::post('virtual-account', [OthersController::class, 'reserveAccount']);
    Route::post('payment-link', [OthersController::class, 'generatePaymentLink']);
});
