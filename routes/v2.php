<?php

use App\Http\Controllers\Api\OthersController;
use App\Http\Controllers\Api\V2\AuthenticationController;
use App\Http\Controllers\Api\V2\AutobuyController;
use App\Http\Controllers\Api\V2\CGWalletController;
use App\Http\Controllers\Api\V2\ListController;
use App\Http\Controllers\Api\V2\NotificationController;
use App\Http\Controllers\Api\V2\OtherController;
use App\Http\Controllers\Api\V2\PayController;
use App\Http\Controllers\Api\V2\PinManagementController;
use App\Http\Controllers\Api\V2\RechargeCardController;
use App\Http\Controllers\Api\V2\ReportController;
use App\Http\Controllers\Api\V2\RewardController;
use App\Http\Controllers\Api\V2\TransactionsController;
use App\Http\Controllers\Api\V2\UserController;
use App\Http\Controllers\Api\V2\ValidationController;
use App\Http\Controllers\Api\V2\WalletTransferController;
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

Route::prefix('v2')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::get('gmma/{username}', [AuthenticationController::class, 'gmma']);
    Route::post('newdevice', [AuthenticationController::class, 'newdeviceLogin'])->name('api_newdevice');
    Route::post('sociallogin', [AuthenticationController::class, 'sociallogin']);
    Route::post('resetpassword', [AuthenticationController::class, 'resetpassword']);
    Route::post('signup', [AuthenticationController::class, 'signup']);

    Route::post('validate', [ValidationController::class, 'index']);

    Route::get('referralPlans', [OtherController::class, 'referralPlans']);

    Route::get('support', [UserController::class, 'support']);

    Route::get('airtime-converter', [ListController::class, 'airtimeConverter']);
    Route::get('airtime', [ListController::class, 'airtime']);
    Route::get('electricity', [ListController::class, 'electricity']);
    Route::get('airtime/countries', [ListController::class, 'airtimeInt']);
    Route::get('data/{network}', [ListController::class, 'data']);
    Route::get('tv/{network}', [ListController::class, 'cabletv']);
    Route::get('jamb', [ListController::class, 'jamb']);
    Route::get('education', [ListController::class, 'education']);
    Route::get('betting', [ListController::class, 'betting']);
    Route::get('availableCommissions', [ListController::class, 'availableCommissions']);

    Route::post('email-verification', [AuthenticationController::class, 'email_verify']);
    Route::post('email-verification-continue', [AuthenticationController::class, 'email_verify_continue']);

    Route::post('set-passcode', [UserController::class, 'set_passcode']);

    Route::post('reset-pin', [PinManagementController::class, 'resetPin']);
    Route::put('reset-pin', [PinManagementController::class, 'completeResetPin']);

    Route::post('reset-passcode', [PinManagementController::class, 'resetPasscode']);
    Route::put('reset-passcode', [PinManagementController::class, 'completeResetPasscode']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('biometriclogin', [AuthenticationController::class, 'biometricLogin']);
        Route::post('login-pin', [AuthenticationController::class, 'loginpin']);

        Route::get('dashboard', [UserController::class, 'dashboard']);
        Route::post('changepin', [PinManagementController::class, 'change_pin']);
        Route::get('toggle-pin', [PinManagementController::class, 'togglePin']);

        Route::get('referrals', [UserController::class, 'referrals']);
        Route::post('addreferral', [UserController::class, 'add_referral']);

        Route::get('transactions', [TransactionsController::class, 'transactions']);
        Route::get('transactions-summary', [TransactionsController::class, 'transactionsSummary']);
        Route::get('transactions-recent', [TransactionsController::class, 'transactionsRecent']);
        Route::get('transactions-pending', [TransactionsController::class, 'transactionsPending']);
        Route::get('transactions-success', [TransactionsController::class, 'transactionsSuccess']);
        Route::get('transactions-reversed', [TransactionsController::class, 'transactionsReversed']);
        Route::get('transactions-data', [TransactionsController::class, 'transactionsData']);
        Route::get('transactions-airtime', [TransactionsController::class, 'transactionsAirtime']);
        Route::get('transactions-tv', [TransactionsController::class, 'transactionsTv']);
        Route::get('transactions-electricity', [TransactionsController::class, 'transactionsElectricity']);
        Route::get('transactions-education', [TransactionsController::class, 'transactionsEducation']);
        Route::get('transactions-funding', [TransactionsController::class, 'transactionsFunding']);
        Route::get('transactions-epin', [TransactionsController::class, 'transactionsEpin']);
        Route::get('commissions', [TransactionsController::class, 'commissions']);
        Route::get('bonus', [TransactionsController::class, 'bonus']);
        Route::get('gmtransactions', [OtherController::class, 'getGmTrans']);

        Route::post('changepassword', [UserController::class, 'change_password']);
        Route::get('paymentcheckout', [OtherController::class, 'paymentcheckout']);

        Route::get('all-notifications', [NotificationController::class, 'notifications']);
        Route::get('unread-notifications', [NotificationController::class, 'unreadnotifications']);
        Route::put('read-all-notifications', [NotificationController::class, 'markAsRead']);
        Route::post('generate-notifications', [NotificationController::class, 'generate']);
        Route::post('account-statement', [NotificationController::class, 'generateAccountStatement']);

        Route::get('rc-plans', [RechargeCardController::class, 'rcplans']);
        Route::post('rc-plan-purchase', [RechargeCardController::class, 'rcpurchase']);


        Route::get('airtime/operators/{country}', [ListController::class, 'airtimeCountry']);

        Route::middleware(['general_middleware'])->group(function () {
            Route::post('airtime', [PayController::class, 'buyairtime'])->middleware('check_UDS_middleware:airtime');
            Route::post('data', [PayController::class, 'buydata'])->middleware('check_UDS_middleware:data');
            Route::post('tv', [PayController::class, 'buytv'])->middleware('check_UDS_middleware:tv');
            Route::post('electricity', [PayController::class, 'buyelectricity'])->middleware('check_UDS_middleware:electricity');
            Route::post('betting', [PayController::class, 'buybetting']);
            Route::post('jamb', [PayController::class, 'buyJamb']);
            Route::post('bizverification', [PayController::class, 'bizverification']);
            Route::post('ninvalidation', [PayController::class, 'ninvalidation']);
            Route::get('ninvalidation-price', [PayController::class, 'ninvalidationPrice']);

            Route::post('bulkairtime', [UserController::class, 'bulkAirtime']);

            Route::post('airtimeconverter', [PayController::class, 'a2ca2b'])->middleware('check_UDS_middleware:airtime2cash');
            Route::post('resultchecker', [PayController::class, 'resultchecker'])->middleware('check_UDS_middleware:education');

            Route::post('bulk-sms', [PayController::class, 'bulkSMS']);

            Route::post('epins', [PayController::class, 'epins']);

            Route::post('username/validate', [WalletTransferController::class, 'validateUsername']);
            Route::post('w2w/transfer', [WalletTransferController::class, 'transfer'])->middleware('check_UDS_middleware:wallet_transfer');
        });

        Route::get('profile', [UserController::class, 'profile']);
        Route::get('agentstatus', [UserController::class, 'agentStatus']);
        Route::post('agent', [UserController::class, 'requestAgent']);
        Route::post('requestReseller', [UserController::class, 'requestReseller']);
        Route::get('request-agentdoc', [UserController::class, 'requestAgentDocument']);
        Route::post('agentdocument', [UserController::class, 'agentDocumentation']);
        Route::post('uploaddp', [UserController::class, 'uploaddp']);
        Route::post('uploaddpURL', [UserController::class, 'uploaddpURL']);
        Route::get('vaccounts', [UserController::class, 'vaccounts']);
        Route::get('vaccounts2', [UserController::class, 'vaccounts2']);

        Route::post('user-upgrade', [UserController::class, 'referral_upgrade']);

        Route::get('get-other-service', [OtherController::class, 'getOtherService']);

        Route::get('banklist', [OtherController::class, 'banklist']);
        Route::post('verifyBank', [OtherController::class, 'verifyBank']);
        Route::post('withdrawfund', [OtherController::class, 'withdraw']);


        Route::post('fundwallet', [OtherController::class, 'fundwallet']);

        Route::get('freemoney', [UserController::class, 'freemoney']);

        Route::get('leaderboard', [OtherController::class, 'getPoints']);

        Route::post('get-equivalent', [OtherController::class, 'getEqv']);
        Route::post('payment/flutterwave', [OtherController::class, 'flutterwavePayment']);

        Route::get('sliders', [OthersController::class, 'sliders']);

        Route::get('apikey/regenerate', [UserController::class, 'requestAPIkey']);
        Route::get('getfaqs', [OtherController::class, 'getFAQs']);
        Route::get('allforu', [OtherController::class, 'allforu']);

        Route::get('cg-wallets', [CGWalletController::class, 'cgWallets']);
        Route::get('cg-bundles', [CGWalletController::class, 'cgBundles']);
        Route::get('cg-purchase-history', [CGWalletController::class, 'cgPurchaseHistory']);
        Route::get('cg-usage-history', [CGWalletController::class, 'cgUsageHistory']);
        Route::get('cg-transfer-history', [CGWalletController::class, 'cgTransferHistory']);
        Route::post('cg-bundles-buy', [CGWalletController::class, 'cgBundleBuy']);
        Route::post('cg-bundles-transfer', [CGWalletController::class, 'cgBundleTransfer']);

        Route::post('report_yearly', [ReportController::class, 'yearly']);
        Route::post('report_monthly', [ReportController::class, 'monthly']);
        Route::post('report_daily', [ReportController::class, 'daily']);

        Route::get('getPromoCode', [OtherController::class, 'getPromoCode']);

        Route::post('moveFunds', [OtherController::class, 'moveFunds']);

        Route::get('beneficiary/{type}', [OtherController::class, 'beneficiary']);


        Route::prefix('autobuy')->group(function () {
            Route::post('/', [AutobuyController::class, 'store']); // Add Autobuy
            Route::get('/', [AutobuyController::class, 'index']); // View all Autobuys
            Route::get('/past', [AutobuyController::class, 'pastAutobuys']); // View past Autobuys
            Route::get('/recent', [AutobuyController::class, 'recentAutobuys']); // View recent Autobuys

            Route::patch('/cancel/{id}', [AutobuyController::class, 'cancel']); // Cancel Autobuy
            Route::put('/update/{id}', [AutobuyController::class, 'update']); // Update Autobuy
        });

        Route::post('/rewards/check-in', [RewardController::class, 'checkIn']);
        Route::get('/rewards/check-ins', [RewardController::class, 'getCheckIns']);

        Route::get('/rewards/spinwin', [RewardController::class, 'fetch']);
        Route::post('/rewards/spinwin', [RewardController::class, 'continue']);

    });

});
