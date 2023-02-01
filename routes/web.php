<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RolesPermissionController;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Http\Controllers\CGBundleController;
use App\Http\Controllers\FAQsController;
use App\Http\Controllers\GatewayControl;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Reseller\BlockReseller;
use App\Http\Controllers\ResellerServiceController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Auth;

//Auth::routes(['register' => false]);

Route::get('/', function () {
//    return view('welcome');
    return redirect()->route('login');
})->name('welcome');

Route::get('/reringo/{id}', function ($id) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.api.ringo.ng/api/b2brequery',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
"request_id" : "'.$id.'"
}',
        CURLOPT_HTTPHEADER => array(
            'email: '.env('RINGO_EMAIL'),
            'password: '.env('RINGO_PASSWORD'),
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    echo $response;


})->name('rrrinu');

Route::get('/balringo', function ($id) {

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.api.ringo.ng/api/agent/p2',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "serviceCode": "INFO"
}',
    CURLOPT_HTTPHEADER => array(
        'email: Holarlekano@gmail.com',
        'password: Abayomioye',
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

return "hello";

})->name('balrin');

Route::get('/ogdamsdata', function ($id) {
    Artisan::queue('samji:ogdams --command=data');
})->name('ogdams');



Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/logout', function () {
        DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'logout', 'auditable_id' => auth()->user()->id, 'auditable_type' => 'App\Models\User', 'tags' => 'Logout Successfully',  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );
        Auth::logout();
        return redirect('/login')->with('success', 'You have successfully logout');
    });
    Route::view('/change-password', 'change_password')->name('change_password');
    Route::post('/change-password', [UsersController::class, 'change_password'])->name('change_password');
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::get('/agents', [UsersController::class, 'agents'])->name('agents');
    Route::get('/resellers', [UsersController::class, 'resellers'])->name('resellers');

    Route::get('/regenerateKey/{id}', [UsersController::class, 'regenerateKey'])->name('regenerateKey');

    Route::get('/gmblocked', [UsersController::class, 'gmblocked'])->name('gmblocked');
    Route::get('/dormantusers', [UsersController::class, 'dormant'])->name('dormant');
    Route::get('/loginattempts', [UsersController::class, 'loginattempt'])->name('loginattempt');
    Route::get('/pending_request', [UsersController::class, 'pending'])->name('pendingrequest');
    Route::post('/request_approve', [UsersController::class, 'approve'])->name('user approval');
    Route::get('/profile/{id}',  [UsersController::class, 'profile'])->name('profile');
    Route::post('/update-profile', [UsersController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/user-password-reset', [UsersController::class, 'passwordReset'])->name('userPasswordReset');
    Route::post('/user-pin-reset', [UsersController::class, 'pinReset'])->name('userPinReset');
    Route::get('/admin-password-reset/{id}', [UsersController::class, 'passwordResetAdmin'])->name('adminPasswordReset');
    Route::get('/admin-bann-user/{id}', [UsersController::class, 'bannUnbann'])->name('adminBannUnbann');
    Route::any('/wallet', [WalletController::class, 'index'])->name('wallet');

    Route::get('/virtual-accounts', [UsersController::class, 'vaccounts'])->name('virtual-accounts');
    Route::get('/block/{id}', [BlockReseller::class, 'updatereseller'])->name('block');
    Route::get('/apikey/{id}', [BlockReseller::class, 'apireseller'])->name('apikey');
    Route::get('/payment-links', [UsersController::class, 'paymentLinks'])->name('payment-links');
    Route::get('/seller', [BlockReseller::class, 'listreseller'])->name('seller');

    Route::get('/withdrawal', [WalletController::class, 'withdrawal_list'])->name('withdrawal_list');
    Route::post('/withdrawal', [WalletController::class, 'withdrawal_submit'])->name('withdrawal_submit');
    Route::post('/reject-withdrawal', [WalletController::class, 'withdrawal_reject'])->name('withdrawal_reject');

    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
    Route::get('/transaction_data', [TransactionController::class, 'trans_data'])->name('transaction_data');
    Route::get('/transaction_airtime', [TransactionController::class, 'trans_airtime'])->name('transaction_airtime');
    Route::get('/transaction_electricity', [TransactionController::class, 'trans_electricity'])->name('transaction_electricity');
    Route::get('/transaction_tv', [TransactionController::class, 'trans_tv'])->name('transaction_tv');
    Route::get('/transaction_resultchecker', [TransactionController::class, 'trans_resultchecker'])->name('transaction_resultchecker');
    Route::get('/transaction_funding', [TransactionController::class, 'trans_funding'])->name('transaction_funding');
    Route::get('/transaction_server8', [TransactionController::class, 'server8'])->name('transaction8');

    Route::get('/transactions-pending', [TransactionController::class, 'pending'])->name('trans_pending');
    Route::post('/trans-resubmit', [TransactionController::class, 'trans_resubmit'])->name('trans_resubmit');
    Route::post('/trans-resubmitAll', [TransactionController::class, 'trans_resubmitAll'])->name('trans_resubmitAll');
    Route::get('/trans_delivered/{id}', [TransactionController::class, 'trans_delivered'])->name('trans_delivered');
    Route::get('/reverse-transaction2/{id}', [TransactionController::class, 'reverse2'])->name('reverse2');

    Route::get('/payment-gateway', [GatewayControl::class, 'gateway'])->name('paymentgateway');
    Route::get('/editpayment/{id}', [GatewayControl::class, 'editgateway'])->name('paymentgateway_edit');
    Route::post('/payment-gateway', [GatewayControl::class, 'updategateway'])->name('paymentgateway_update');

    Route::get('/roles', [RolesPermissionController::class, 'roles'])->name('roles.list');
    Route::get('/roles/{id}', [RolesPermissionController::class, 'role'])->name('roles.edit');
    Route::post('/roles-update/{id}', [RolesPermissionController::class, 'updateRole'])->name('roles.update');
    Route::get('/roles-delete/{id}', [RolesPermissionController::class, 'deleteRole'])->name('roles.delete');
    Route::get('/role-create', [RolesPermissionController::class, 'createRoleget'])->name('roles.create');
    Route::post('/role-create', [RolesPermissionController::class, 'createRole'])->name('roles.create');

    Route::get('/admin-role', [RolesPermissionController::class, 'userole'])->name('admin.role');
    Route::post('/update-admin-role', [RolesPermissionController::class, 'updateuserole'])->name('admin.updaterole');
    Route::get('/revoke-admin-role/{id}', [RolesPermissionController::class, 'revokeUserole'])->name('admin.rovoke');

    Route::get('/generalmarket',  [TransactionController::class, 'gmhistory'])->name('generalmarket');
    Route::get('/plcharges',  [TransactionController::class, 'plcharges'])->name('plcharges');
    Route::post('/rechargecard', [TransactionController::class, 'rechargecard'])->name('rechargecard');
    Route::get('/rechargecards', [TransactionController::class, 'rechargemanual'])->name('manualrechargecard');
    Route::post('/monnify',  [TransactionController::class, 'monnify'])->name('monnify');
//    Route::get('/addfund', [WalletController::class, 'addfund'])->name('addfund');
    Route::view('/profile', 'email_agent');
    Route::view('/cc', 'mail.passwordreset');
    Route::view('/finduser', 'find_user');
    Route::POST('/finduser', 'UsersController@finduser')->name('finduser');

    Route::view('/findtransaction', 'find_transaction')->name('findtransaction');
    Route::post('/findtransaction', [TransactionController::class, 'finduser'])->name('findtransactionsubmit');

    Route::view('/gnews', 'addgnews');
    Route::post('/gnews', [UsersController::class, 'addgnews'])->name('addgnews');
    Route::post('/user-sms', [UsersController::class, 'sendsms'])->name('user.sms');
    Route::post('/user-email', [UsersController::class, 'sendemail'])->name('user.email');
    Route::post('/user-pushnotif', [UsersController::class, 'sendpushnotif'])->name('user.pushnotif');
    Route::get('/agentpayment', [UsersController::class, 'agent_list'])->name('agent.payment.list');
    Route::get('/audits', [UsersController::class, 'audits'])->name('audits');
    Route::post('/agentpayment-confirm', [UsersController::class, 'agent_confirm'])->name('agent.payment.confirmation');
    Route::post('/agentpayment', [UsersController::class, 'agent_payment'])->name('agent.payment');

    Route::view('/verification_server10', 'verification_s10')->name('verification_s10');
    Route::view('/verification_server6', 'verification_s6')->name('verification_s6');
    Route::view('/verification_server5', 'verification_s5');
    Route::view('/verification_server4', 'verification_s4');
    Route::view('/verification_server3', 'verification_s3')->name('verification_s3');
    Route::view('/verification_server2', 'verification_s2')->name('verification_s2');
    Route::view('/verification_server1b', 'verification_s1b');
    Route::view('/verification_server1', 'verification_s1')->name('verification_s1');
    Route::view('/verification_server1dt', 'verification_s1dt');
    Route::post('/verification_server3', [VerificationController::class, 'server3'])->name('verification_server3');
    Route::post('/verification_server2', [VerificationController::class, 'server2'])->name('verification_server2');
    Route::post('/verification_server1b', [VerificationController::class, 'server1b'])->name('verification_server1b');
    Route::post('/verification_server1', [VerificationController::class, 'server1'])->name('verification_server1');
    Route::post('/verification_server1dt', [VerificationController::class, 'server1dt'])->name('verification_server1dt');
    Route::post('/verification_server4', [VerificationController::class, 'server4'])->name('verification_server4');
    Route::post('/verification_server5',  [VerificationController::class, 'server5'])->name('verification_server5');
    Route::post('/verification_server6', [VerificationController::class, 'server6'])->name('verification_server6');
    Route::post('/verification_server10', [VerificationController::class, 'server10'])->name('verification_server10');

    Route::middleware(['authCheck'])->group(function () {
        Route::POST('/referral_upgrade', [UsersController::class, 'referral_upgrade'])->name('referral.upgrade');
        Route::view('/referral_upgrade', 'referral_upgrade');

        Route::get('/airtime2cash', [TransactionController::class, 'airtime2cash'])->name('transaction.airtime2cash');
        Route::post('/airtime2cash', [TransactionController::class, 'airtime2cashpayment'])->name('transaction.airtime2cash.payment');
        Route::get('/airtime2cash-success/{id}', [TransactionController::class, 'airtime2cashpaymentSuccess'])->name('transaction.airtime2cash.success');
        Route::get('/airtime2cash-cancel/{id}', [TransactionController::class, 'airtime2cashpaymentCancel'])->name('transaction.airtime2cash.cancel');

        Route::get('/airtime2cash-settings', [TransactionController::class, 'airtime2cashSettings'])->name('transaction.airtime2cashSettings');
        Route::get('/airtime2cash-settings-edit/{id}', [TransactionController::class, 'airtime2cashSettingsEdit'])->name('transaction.airtime2cashSettings.edit');
        Route::post('/airtime2cash-settings-modify', [TransactionController::class, 'airtime2cashSettingsModify'])->name('transaction.airtime2cashSettings.modify');
        Route::get('/airtime2cash-settings-ed/{id}', [TransactionController::class, 'airtime2cashSettingsED'])->name('transaction.airtime2cashSettings.ed');

        Route::get('/otherservices', [ServerController::class, 'others'])->name('otherservices');
        Route::get('/otherservices_add', [ServerController::class, 'others_add'])->name('otherservices_add');
        Route::post('/otherservices_add', [ServerController::class, 'others_addPost'])->name('otherservices_add');
        Route::get('/otherservices/{id}', [ServerController::class, 'othersedit'])->name('otherservicesEdit');
        Route::get('/otherservices-delete/{id}', [ServerController::class, 'Servicedestroy'])->name('otherservicesDelete');
        Route::post('/otherservices-update', [ServerController::class, 'othersUpdate'])->name('otherservicesUpdate');

        Route::get('/datalist/{network}', [ServerController::class, 'dataserve2'])->name('dataplans');
        Route::get('/datalist/{network}/{server}', [ServerController::class, 'dataserve3'])->name('server_dataplans');
        Route::get('/datacontrol/{id}', [ServerController::class, 'dataserveedit'])->name('datacontrolEdit');
        Route::get('/datacontrol-multiple/{network}/{type}/{status}/{server}', [ServerController::class, 'dataserveMultipleedit'])->name('dataserveMultipleedit');
        Route::get('/dataserveED/{id}', [ServerController::class, 'dataserveED'])->name('dataserveED');
        Route::post('/datacontrol', [ServerController::class, 'dataserveUpdate'])->name('datacontrolUpdate');
        Route::view('/datanew', 'datacontrol_new')->name('datanew');
        Route::post('/datanew', [ServerController::class, 'datanew'])->name('datanew');

        Route::get('/airtimecontrol', [ServerController::class, 'airtime'])->name('airtimecontrol');
        Route::get('/airtimecontrol/{id}', [ServerController::class, 'airtimeEdit'])->name('airtimecontrolEdit');
        Route::get('/airtimecontrolED/{id}', [ServerController::class, 'airtimecontrolED'])->name('airtimecontrolED');
        Route::post('/airtimecontrol', [ServerController::class, 'airtimeUpdate'])->name('airtimecontrolUpdate');

        Route::get('/tvcontrol', [ServerController::class, 'tvserver'])->name('tvcontrol');
        Route::get('/tvcontrol/{id}', [ServerController::class, 'tvEdit'])->name('tvcontrolEdit');
        Route::get('/tvcontrolED/{id}', [ServerController::class, 'tvcontrolED'])->name('tvcontrolED');
        Route::post('/tvcontrol', [ServerController::class, 'tvUpdate'])->name('tvcontrolUpdate');

        Route::get('/electricitycontrol', [ServerController::class, 'electricityserver'])->name('electricitycontrol');
        Route::get('/electricitycontrol/{id}', [ServerController::class, 'electricityEdit'])->name('electricitycontrolEdit');
        Route::get('/electricitycontrolED/{id}', [ServerController::class, 'electricityED'])->name('electricitycontrolED');
        Route::post('/electricitycontrol', [ServerController::class, 'electricityUpdate'])->name('electricitycontrolUpdate');

        Route::prefix('reseller')->name('reseller.')->group(function () {
            Route::get('/datalist/{network}', [ResellerServiceController::class, 'dataPlans'])->name('dataList');
            Route::get('/datalist/{network}/{server}', [ResellerServiceController::class, 'dataPlans2'])->name('server_dataList');
            Route::get('/datacontrol-multiple/{network}/{type}/{status}/{server}', [ResellerServiceController::class, 'dataserveMultipleedit'])->name('dataserveMultipleedit');
            Route::get('/datacontrol', [ResellerServiceController::class, 'dataserve2'])->name('dataplans');
            Route::get('/datacontrol/{id}', [ResellerServiceController::class, 'dataserveedit'])->name('datacontrolEdit');
            Route::get('/datacontrolED/{id}', [ResellerServiceController::class, 'datacontrolED'])->name('datacontrolED');
            Route::post('/datacontrol', [ResellerServiceController::class, 'dataserveUpdate'])->name('datacontrolUpdate');

            Route::get('/airtimecontrol', [ResellerServiceController::class, 'airtime'])->name('airtimecontrol');
            Route::get('/airtimecontrol/{id}', [ResellerServiceController::class, 'airtimeEdit'])->name('airtimecontrolEdit');
            Route::get('/airtimecontrolED/{id}', [ResellerServiceController::class, 'airtimecontrolED'])->name('airtimecontrolED');
            Route::post('/airtimecontrol', [ResellerServiceController::class, 'airtimeUpdate'])->name('airtimecontrolUpdate');

            Route::get('/tvcontrol', [ResellerServiceController::class, 'tvserver'])->name('tvcontrol');
            Route::get('/tvcontrol/{id}', [ResellerServiceController::class, 'tvEdit'])->name('tvcontrolEdit');
            Route::get('/tvcontrolED/{id}', [ResellerServiceController::class, 'tvcontrolED'])->name('tvcontrolED');
            Route::post('/tvcontrol', [ResellerServiceController::class, 'tvUpdate'])->name('tvcontrolUpdate');

            Route::get('/electricitycontrol', [ResellerServiceController::class, 'electricityserver'])->name('electricitycontrol');
            Route::get('/electricitycontrol/{id}', [ResellerServiceController::class, 'electricityEdit'])->name('electricitycontrolEdit');
            Route::post('/electricitycontrol', [ResellerServiceController::class, 'electricityUpdate'])->name('electricitycontrolUpdate');
        });

        Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::get('addsliders', [SliderController::class, 'create'])->name('sliders.create');
        Route::post('addsliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::get('modify-slider/{id}', [SliderController::class, 'update'])->name('sliders.update');
        Route::get('remove-slider/{id}', [SliderController::class, 'destroy'])->name('sliders.delete');

        Route::get('cg-bundle', [CGBundleController::class, 'index'])->name('cgbundle.index');
        Route::post('cg-bundle', [CGBundleController::class, 'create'])->name('cgbundle.create');
        Route::get('cg-bundle-list', [CGBundleController::class, 'lists'])->name('cgbundle.list');
        Route::get('cg-transactions-list', [CGBundleController::class, 'cgtrans'])->name('cgbundle.trans');
        Route::get('cg-bundle-modify/{id}', [CGBundleController::class, 'modify'])->name('cgbundle.modify');
        Route::get('cg-bundle-edit/{id}', [CGBundleController::class, 'edit'])->name('cgbundle.edit');
        Route::post('cg-bundle-update}', [CGBundleController::class, 'update'])->name('cgbundle.update');
        Route::get('cg-bundle-apply-credit/{id}', [CGBundleController::class, 'apply_credit'])->name('cgbundle.apply_credit');
        Route::view('cg-bundle-debit', 'cg_bundle_debit')->name('cgbundle.debit');
        Route::post('cg-bundle-debit', [CGBundleController::class, 'debit'])->name('cgbundle.debit');

        Route::get('cg-bundle-apply', [CGBundleController::class, 'applyView'])->name('cgbundle.apply');
        Route::post('cg-bundle-apply', [CGBundleController::class, 'apply'])->name('cgbundle.apply');

        Route::get('faqs', [FAQsController::class, 'index'])->name('faqs.index');
        Route::post('faqs', [FAQsController::class, 'store'])->name('faqs.store');
        Route::view('faq/create', 'faq_add')->name('faqs.create');
        Route::get('edit-faq/{id}', [FAQsController::class, 'edit'])->name('faqs.edit');
        Route::post('update-faq', [FAQsController::class, 'update'])->name('faqs.update');
        Route::get('modify-faq/{id}', [FAQsController::class, 'modify'])->name('faqs.modify');
        Route::get('remove-faq/{id}', [FAQsController::class, 'destroy'])->name('faqs.delete');

        Route::get('plansRefresh/{type}', [HomeController::class, 'plansRefresh'])->name('plansRefresh');
        Route::get('allsettings', [HomeController::class, 'allsettings'])->name('allsettings');
        Route::get('allsettings-edit/{id}', [HomeController::class, 'allsettingsEdit'])->name('allsettingsEdit');
        Route::post('allsettings-update', [HomeController::class, 'allsettingsUpdate'])->name('allsettingsUpdate');

        Route::post('/updateLevel', [UsersController::class, 'updateLevel'])->name('updateLevel');
        Route::post('/datacontrol1', [ServerController::class, 'updatedataserve'])->name('datacontrol1');

        Route::view('/addfund', 'addfund')->name("addfund");
        Route::post('/addfund', [WalletController::class, 'addfund'])->name('addfund')->middleware('authCheck');
        Route::view('/servercontrol', 'servercontrol');
        Route::view('/rechargecard', 'rechargecard');
        Route::view('/addtransaction', 'addtransaction');
        Route::post('/addtransaction', [TransactionController::class, 'addtransaction'])->name('addtransaction');
        Route::view('/adddatatransaction', 'addtransaction_data');
        Route::post('/adddatatransaction', [TransactionController::class, 'addtransaction_data'])->name('adddatatransaction');
        Route::view('/reversal', 'reversal')->name('reversal');
        Route::post('/reversal-confirm', [TransactionController::class, 'reversal_confirm'])->name('reversal.confirm');
        Route::post('/updateairtimeserver', [ServerController::class, 'changeserver'])->name('updateairtimeserver');
        Route::get('/reverse-transaction/{id}', [TransactionController::class, 'reverse'])->name('reverse');
        Route::any('/report_pnl', [ReportsController::class, 'pnl'])->name('report_pnl');
        Route::any('/report_yearly', [ReportsController::class, 'yearly'])->name('report_yearly');
        Route::any('/report_monthly', [ReportsController::class, 'monthly'])->name('report_monthly');
        Route::any('/report_daily', [ReportsController::class, 'daily'])->name('report_daily');
        Route::get('/cryptorequest', [TransactionController::class, 'cryptos'])->name('cryptos');
    });

});

require __DIR__ . '/storages.php';


