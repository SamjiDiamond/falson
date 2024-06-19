<?php

use App\Http\Controllers\Api\V2\UserController;
use App\Http\Controllers\Api\V3\AuthenticationController;
use App\Http\Controllers\Api\V3\ListController;
use App\Http\Controllers\Api\V3\ProfileController;
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

Route::prefix('v3')->group(function () {

    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('login-2fa', [AuthenticationController::class, 'login2fa'])->name('api_2falogin');

    Route::get('support', [UserController::class, 'supportv3']);
    Route::get('data/{network}', [ListController::class, 'dataCategory']);
    Route::get('data/{network}/{category}', [ListController::class, 'dataList']);

    Route::post('changepassword', [UserController::class, 'change_password']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('securitySettings', [ProfileController::class, 'securitySettings']);
    });

});
