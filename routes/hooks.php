<?php

use App\Http\Controllers\Api\MCDAssistantController;
use App\Http\Controllers\Api\UltilityController;
use App\Http\Controllers\HW_WebhookController;
use App\Http\Controllers\Payment\BudpayController;
use App\Http\Controllers\Payment\KorapayHookController;
use App\Http\Controllers\Payment\MonnifyHookController;
use App\Http\Controllers\Payment\PayantHookController;
use App\Http\Controllers\Payment\PaystackHookController;
use App\Http\Controllers\Payment\RaveHookController;
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

Route::prefix('hook')->group(function () {
    Route::post('budpay', [BudpayController::class, 'index']);
    Route::post('monnify', [MonnifyHookController::class, 'index']);
    Route::post('paystack', [PaystackHookController::class, 'index']);
    Route::post('korapay', [KorapayHookController::class, 'index']);
    Route::post('rave', [RaveHookController::class, 'index']);
    Route::post('payant', [PayantHookController::class, 'index']);
    Route::get('payant', [PayantHookController::class, 'verify']);
    Route::post('hw', [HW_WebhookController::class, 'index']);

});
