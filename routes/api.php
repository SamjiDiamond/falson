<?php

use App\Http\Controllers\Api\V2\ValidationController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('kycUpdate', [ValidationController::class, 'kycUpdate']);
Route::post('kycUpdateInfo', [\App\Http\Controllers\Api\V2\UserController::class, 'kycUpdateInfo']);

require __DIR__ . '/reseller.php';
require __DIR__ . '/v2.php';
require __DIR__ . '/v3.php';
require __DIR__ . '/hooks.php';
