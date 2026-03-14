<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellBettingTopup;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Api\SellEducationalController;
use App\Http\Controllers\Api\SellElectricityController;
use App\Http\Controllers\Api\SellTVController;
use App\Http\Controllers\Controller;
use App\Jobs\ATMtransactionserveJob;
use App\Jobs\PushNotificationJob;
use App\Jobs\ReverseTransactionJob;
use App\Mail\AdminNotificationMail;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\AppElectricityControl;
use App\Models\CGWallets;
use App\Models\PndL;
use App\Models\RCPricing;
use App\Models\ResellerBetting;
use App\Models\Serverlog;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PayController extends Controller
{
    function buyairtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'country' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        if (strtoupper($input['country']) == "NG" || strtoupper($input['country']) == "NIGERIA") {
            $airtime = AppAirtimeControl::where("network", $input['provider'])->first();

            if (!$airtime) {
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are  MTN, 9MOBILE, GLO, AIRTEL.']);
            }

            if ($airtime->status == 0) {
                return response()->json(['success' => 0, 'message' => 'Network currently not available']);
            }


            $server = $airtime->server;
            $discount = $airtime->discount;

            if ($input['amount'] < 100) {
                return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
            }

            if ($input['amount'] > 5000) {
                return response()->json(['success' => 0, 'message' => 'Maximum amount is #5000']);
            }

            $dis = explode("%", $discount);
            $discount = $input['amount'] * ($dis[0] / 100);

        } else {
            $server = 9;
            $discount = 0;
        }

        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $server;
        $proceed['5'] = "airtime";

        return $this->handlePassage($request, $proceed);

//        return $this->debitUser($request, $input['provider'], $debitAmount, $discount, $server, "airtime");

//        return response()->json(['success' => 1, 'message' => 'Airtime Sent Successfully']);

    }

    function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $discount = 0;
        $debitAmount = $rac->pricing;

        $proceed['1'] = $rac->network;
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "data";
        $proceed['6'] = $rac->name;
        $proceed['7'] = $rac->dataplan;

        return $this->handlePassage($request, $proceed);
    }

    function buytv(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $sett=Settings::where('name', 'tv_server')->first();

        if ($sett->value == "RINGO") {
            $server = "2";
        } else {
            $server = $sett->value;
        }

        $rac = AppCableTVControl::where([["coded", strtolower($input['coded'])], ["server", $server]])->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $debitAmount = $rac->price;

        $dis = explode("%", $rac->discount);
        $discount = $debitAmount * ($dis[0] / 100);

        $proceed['1'] = $rac->type;
        $proceed['2'] = $debitAmount * 1;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "tv";

        return $this->handlePassage($request, $proceed);

//        return response()->json(['success' => 1, 'message' => 'TV Subscribe Successfully']);

    }

    function buyelectricity(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'phone' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $rac = AppElectricityControl::where("code", strtolower($input['provider']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        if ($input['amount'] < 1000) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is ₦1,000']);
        }
//
//        if ($input['amount'] > 20000) {
//            return response()->json(['success' => 0, 'message' => 'Maximum amount is #20,000']);
//        }


        $discount = 0;
        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "electricity";

        return $this->handlePassage($request, $proceed);

//        return response()->json(['success' => 1, 'message' => 'Electricity Token Generated Successfully', 'token' => 'hfhfwufwf743uewfj48ui']);

    }

    function buybetting(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $rac = ResellerBetting::where("code", strtoupper($input['provider']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 5000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #5,000']);
        }


        $discount = 0;
        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "betting";

        return $this->handlePassage($request, $proceed);
    }

    function buyJamb(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required',
            'coded' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $debitAmount = $input['amount'];
        $server = 9;
        $discount = 0;

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $server;
        $proceed['5'] = "jamb";

        return $this->handlePassage($request, $proceed);
    }

    function bulkSMS(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'sender' => 'required',
            'message' => 'required',
            'recipient' => 'required',
            'type' => 'required|in:dnd,normal'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        if ($input['type'] == "dnd") {
            $set = Settings::where('name', 'bulk_sms_price_dnd')->first();
        } else {
            $set = Settings::where('name', 'bulk_sms_price_normal')->first();
        }

        $rep_count = explode(",", $input['recipient']);


        $input["user_name"] = Auth::user()->user_name;
        $input['name'] = "BULKSMS";

        $input['ref'] = "bsms" . rand() . time();
        $input['amount'] = $set->value * count($rep_count);

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        if ($input['amount'] > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }

        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $input['amount'];

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = "BULK SMS on " . $input['sender'] . " to " . $input['recipient'];

        $input['status'] = 'sent';
        $input['code'] = 'bulksms';


        $payload = array('sender' => $input['sender'], 'message' => $input['message'], 'recipient' => $input['recipient'], 'responsetype' => 'json', 'dlr' => '1', 'clientbatchid' => $input['ref']);

        $input['extra'] = json_encode($payload);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('VTPASS_MSG_BASEURL') . '/sms/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . env('VTPASS_MSG_PK'),
                'X-Secret: ' . env('VTPASS_MSG_SK')
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        Log::info("VTPASS Bulk SMS - ");
        Log::info(json_encode($payload));
        Log::info($response);


        $rep = json_decode($response, true);

        if ($rep['responseCode'] != "TG00") {
            return response()->json(['success' => 0, 'message' => $rep['response']]);
        }

        $input['server_response'] = $response;

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Message Sent']);

    }

    function epins(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'network' => 'required|in:MTN,GLO,AIRTEL,9MOBILE',
            'amount' => 'required|in:100,200,500',
            'quantity' => 'required|numeric',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $price = $input['amount'];

        $input["user_name"] = Auth::user()->user_name;
        $input['name'] = $input['network'] . " Airtime Pin";

        if (Auth::user()->rc_price_plan_id == 0) {
            $input['amount'] = $input['amount'] * $input['quantity'];
        } else {

            $pricing = RCPricing::where([['id', Auth::user()->rc_price_plan_id], ['status', 1]])->first();

            if ($pricing) {
                switch ($input['network']) {
                    case 'MTN':
                        $input['amount'] = $pricing->mtn * $input['quantity'] * ($input['amount'] / 100);
                        break;
                    case 'GLO':
                        $input['amount'] = $pricing->glo * $input['quantity'] * ($input['amount'] / 100);
                        break;
                    case 'AIRTEL':
                        $input['amount'] = $pricing->airtel * $input['quantity'] * ($input['amount'] / 100);
                        break;
                    case '9MOBILE':
                        $input['amount'] = $pricing->ninemobile * $input['quantity'] * ($input['amount'] / 100);
                        break;
                }
            } else {
                $input['amount'] = $input['amount'] * $input['quantity'];
            }
        }

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        if ($input['amount'] > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }


        $trans_exist = Transaction::where('ref', $input['ref'])->exists();

        if ($trans_exist) {
            return response()->json(['success' => 0, 'message' => 'Transaction reference already exist']);
        }

        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $input['amount'];

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = "Airtime Pin of " . $input['network'] . $price . ",  " . $input['quantity'] . " cps";

        $input['status'] = 'delivered';
        $input['code'] = 'airtimepin';

        $payload = '{
    "network": "' . strtoupper($input['network']) . '",
    "amount" : "' . $price . '",
    "quantity" : "' . $input['quantity'] . '",
    "order" : "instant"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('5STARRC_URL') . 'generate-epins',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('5STARRC_AUTH'),
                    'Content-Type: application/json',
                    'User-Agent: FALSON'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);


            Log::info("5Star RC Transaction. - " . $input['ref']);
            Log::info($payload);
            Log::info($response);

        } else {
            $response = '{"success":true,"message":"Epins generated successfully!","data":{"reference":"12312b802e031F","epins":[{"pin":"7938855782","serial":"0891319068","amount":"100","expiry":"21\/09\/2035","id":113575,"network":"MTN"}]}}';
        }

        $rep = json_decode($response, true);

        if (!$rep['success']) {
            return response()->json(['success' => 0, 'message' => $rep['message']]);
        }

        $input['server_response'] = $response;
        $input['server_ref'] = $rep['data']['reference'];
        $input['token'] = $rep['data']['epins'];

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();

        return response()->json(['success' => 1, 'message' => $rep['message'], 'data' => $rep['data']['epins']]);

    }

    public function bizverification(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'biz' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $input["user_name"] = Auth::user()->user_name;
        $net = "BIZVERIFICATION";

        $set=Settings::where('name', 'biz_verification_price_customer')->first();

        $input['amount'] = $set->value ;

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $uid = $input['user_name'];
        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $input['amount'];

        if ($input['amount'] > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }

        $trans_exist = Transaction::where('ref', $input['ref'])->exists();

        if ($trans_exist) {
            return response()->json(['success' => 0, 'message' => 'Transaction reference already exist']);
        }

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = "biz verification on " . $input['biz'];
        $input['extra'] = "";
        $input['name'] = $net;
        $input['status'] = 'delivered';
        $input['code'] = 'bizv';

            $curl = curl_init();

            $payload=array('ref' => $input['ref'],'biz' => $input['biz']);


            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/biz-verification',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('MCD_KEY')
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        Log::info("MCD Business Verification - ");
        Log::info(json_encode($payload));
        Log::info($response);


        $rep = json_decode($response, true);

        if($rep['success'] != 1){
            return response()->json(['success' => 0, 'message' => $rep['message']]);
        }

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();


        $input["type"] = "income";
        $input["gl"] = $net;
        $input["amount"] = $input['amount'];
        $input['date'] = Carbon::now();
        $input["narration"] = "Being $net charges from " . $input['user_name'] . " on " . $input['ref'];

        PndL::create($input);

        return response()->json(['success' => 1, 'message' => 'Business validated successfully', 'data' => $rep['data']]);

    }

    public function ninvalidation(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'number' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $input["user_name"] = Auth::user()->user_name;
        $net = "NINVERIFICATION";

        $set = Settings::where('name', 'biz_verification_price_customer')->first();

        $input['amount'] = $set->value;

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $uid = $input['user_name'];
        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $input['amount'];

        if ($input['amount'] > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }

        $trans_exist = Transaction::where('ref', $input['ref'])->exists();

        if ($trans_exist) {
            return response()->json(['success' => 0, 'message' => 'Transaction reference already exist']);
        }

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = "NIN verification on " . $input['number'];
        $input['extra'] = "";
        $input['name'] = $net;
        $input['status'] = 'delivered';
        $input['code'] = 'ninv';

        $curl = curl_init();

        $payload = array('ref' => $input['ref'], 'number' => $input['number']);


        curl_setopt_array($curl, array(
            CURLOPT_URL => env('MCD_BASEURL') . '/ninvalidation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('MCD_KEY')
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        Log::info("NIN Verification - ");
        Log::info(json_encode($payload));
        Log::info($response);


        $rep = json_decode($response, true);

        if ($rep['success'] != 1) {
            return response()->json(['success' => 0, 'message' => $rep['message']]);
        }

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();


        $input["type"] = "income";
        $input["gl"] = $net;
        $input["amount"] = $input['amount'];
        $input['date'] = Carbon::now();
        $input["narration"] = "Being $net charges from " . $input['user_name'] . " on " . $input['ref'];

        PndL::create($input);

        return response()->json(['success' => 1, 'message' => 'NIN validated successfully', 'data' => $rep['data']]);

    }

    public function ninvalidationPrice(Request $request)
    {
        $set = Settings::where('name', 'biz_verification_price_customer')->first();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $set->value]);
    }

    public function a2ca2b(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'network' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'receiver' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        try {
            $number = Airtime2CashSettings::where('network', '=', $input['network'])->first();

            if(!$number){
                return response()->json(['success' => 0, 'message' => 'Selected network is currently unavailable']);
            }

            if(!$number){
                return response()->json(['success' => 0, 'message' => 'Selected network is currently unavailable']);
            }

            $input['ip'] = $_SERVER['REMOTE_ADDR'];

            $input['version'] = $request->header('version');

            $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

            $input['phoneno'] = $input['number'];

            $input['user_name'] = Auth::user()->user_name;

            Airtime2Cash::create($input);

            $am2r = $input['amount'] - (($number->discount / 100) * $input['amount']);

            $message = "User: " . $input['user_name'] . ", Network: " . $input['network'] . ", Amount: " . $input['amount'] . ", Number: " . $input['number'] . ", Reference: " . $input['ref'] . ", Receiver: " . $input['receiver'];
            PushNotificationJob::dispatch("Holarmie", $message, "Airtime2Cash Notice");
            $mm = "There is a new Airtime Converter request with the details below. <br />" . $message;
            Mail::send(new AdminNotificationMail($mm));

            return response()->json(['success' => 1, 'message' => 'Transfer #' . $input['amount'] . ' to ' . $number->number . ' and get your #' . $am2r . ' instantly. Reference: ' . $input['ref'] . '. By doing so, you acknowledge that you are the legitimate owner of this airtime and you have permission to send it to us and to take possession of the airtime.']);
        } catch (Exception $e) {
            return response()->json(['success' => 0, 'message' => 'An error occured', 'error' => $e]);
        }


    }

    public function resultchecker(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'type' => 'required',
            'quantity' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input["user_name"] = Auth::user()->user_name;
        $net = $input['type'];

        if (strtoupper($net)== "WAEC") {
            $input['price'] = 1900;
        } else {
            $input['price'] = 800;
        }

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $uid = $input['user_name'];
        $qty = $input['quantity'];
        $price = $input['price'];
        $p = $price * $qty;
        $ref = $input["ref"];
        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $p;
        $input['amount'] = $p;

        if ($p > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = $uid . " order " . $net . " Education (".strtoupper($net).") of " . $qty . " quantity with ref " . $ref;
        $input['extra'] = "qty-" . $qty . ", net-" . $net . ", ref-" . $ref;
        $input['name'] = 'Education ('.strtoupper($net).')';
        $input['status'] = 'submitted';
        $input['code'] = 'rch';

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();

        $edu = new SellEducationalController();
        $edu->server1($ref, $input, 'mcd');


//            $at = new PushNotificationController();
//            $at->PushNoti($input['user_name'], "Hi " . $input['user_name'] . ", you will receive your " . $net . " request in your mail soon. Thanks", "Result Checker");

        return response()->json(['success' => 1, 'message' => 'You will receive your request soon']);

    }


    public function buyAirtimeCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellAirtimeController();

        switch (strtolower($server)) {
            case "9":
                return $air->server9($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "7":
                return $air->server7($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "3":
                return $air->server3($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "2":
                return $air->server2($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "1":
                return $air->server1($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "0":
                return response()->json(['success' => 1, 'message' => 'Transaction inprogress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyDataCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellDataController();

        switch (strtolower($server)) {
            case "7":
                return $air->server7($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "5":
                return $air->server5($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "4":
                return $air->server4($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "3":
                return $air->server3($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "2":
                return $air->server2($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "1":
                return $air->server1($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "0":
                return response()->json(['success' => 1, 'message' => 'Transaction inprogress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyTvCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellTVController();

        switch (strtolower($server)) {
            case "1":
                return $air->server1($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "2":
                return $air->server2($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "6":
                return $air->server6($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "7":
                return $air->server7($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "0":
                return response()->json(['success' => 1, 'message' => 'Transaction inprogress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyElectricityCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellElectricityController();

        switch (strtolower($server)) {
            case "1":
                return $air->server1($request, $input['provider'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "2":
                return $air->server2($request, $input['provider'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "7":
                return $air->server7($request, $input['provider'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "0":
                return response()->json(['success' => 1, 'message' => 'Transaction inprogress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyBettingCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellBettingTopup();

        switch (strtolower($server)) {
            case "8":
                return $air->server8($request, $input['provider'], $input['number'], $ref, $input['amount'], $request, $dada, "mcd");
            case "0":
                return $air->server0($request, $input['provider'], $input['number'], $ref, $input['amount'], $request, $dada, "mcd");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }

    }


    public function buyJambCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellEducationalController();

        return $air->server9($request, $input['coded'], $input['number'], $ref, $request, $dada, "mcd");

    }


    public function debitUser(Request $request, $proceed, $provider, $amount, $discount, $server, $requester, $codedName, $ref)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid API key. Kindly contact support'
            ]);
        }

        $payment = $request->payment;

        try {

            $transaction = DB::transaction(function () use ($request, $user, $proceed, $provider, $amount, $discount, $server, $requester, $codedName, $ref, $payment) {

                // Lock user wallet
                $user = User::where('id', $user->id)->lockForUpdate()->first();

                $cg = null;

                if ($payment !== "wallet") {

                    $cg = CGWallets::where([
                        ['user_id', $user->id],
                        ['name', $payment]
                    ])->lockForUpdate()->first();

                    if (!$cg) {
                        throw new \Exception('Invalid payment selected');
                    }
                }

                /**
                 * Balance Validation
                 */
                if ($payment == "wallet") {

                    if ($amount < 1) {
                        throw new \Exception('Transaction cannot be processed');
                    }

                    if ($user->wallet < 1) {
                        throw new \Exception('Insufficient Fund');
                    }

                    if ($user->wallet < $amount) {
                        throw new \Exception('Insufficient balance to handle request');
                    }

                } else {

                    if ($cg->balance < $proceed[7]) {
                        throw new \Exception('Insufficient CG wallet balance');
                    }
                }

                /**
                 * Build Transaction Details
                 */
                $transactionName = $this->buildTransactionName($requester, $provider);

                $description = $this->buildTransactionDescription(
                    $request,
                    $requester,
                    $provider,
                    $codedName
                );

                $initialWallet = $user->wallet;
                $finalWallet = $initialWallet;

                /**
                 * Wallet Deduction
                 */
                if ($payment == "wallet") {

                    $finalWallet = $initialWallet - $amount;

                    $user->wallet = $finalWallet;
                    $user->save();

                } else {

                    $cg->balance -= $proceed[7];
                    $cg->save();
                }

                /**
                 * Create Transaction
                 */
                $tr = Transaction::create([
                    'name' => $transactionName,
                    'description' => $description,
                    'code' => $requester,
                    'amount' => $amount,
                    'date' => now(),
                    'device_details' => $request->header('device') ?? $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'user_name' => $user->user_name,
                    'ref' => $ref,
                    'server' => "server".$server,
                    'server_response' => '',
                    'payment_method' => $payment,
                    'transid' => $ref,
                    'status' => 'pending',
                    'extra' => $discount,
                    'commission' => $discount,
                    'paid_with' => $payment,
                    'i_wallet' => $initialWallet,
                    'f_wallet' => $finalWallet
                ]);

                /**
                 * Commission Handling
                 */
                if ($payment == "wallet" && $discount > 0) {

                    $settings = Settings::where('name','enable_commission')->first();

                    if ($settings && $settings->value == "1") {

                        $commissionInitial = $user->agent_commision;
                        $commissionFinal = $commissionInitial + $discount;

                        Transaction::create([
                            'name' => 'Commission',
                            'description' => "Commission earned on {$description}",
                            'code' => 'tcommission',
                            'amount' => $discount,
                            'status' => 'successful',
                            'i_wallet' => $commissionInitial,
                            'f_wallet' => $commissionFinal,
                            'user_name' => $user->user_name
                        ]);

                        $user->agent_commision = $commissionFinal;
                        $user->save();
                    }
                }

                return $tr;
            });

        } catch (\Exception $e) {

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        /**
         * Call Provider
         */
        $payload = [
            'tid' => $transaction->id,
            'amount' => $amount,
            'discount' => $discount
        ];

        switch ($requester) {

            case "airtime":
                return $this->buyAirtimeCTD($request,$ref,$provider,$payload,$server);

            case "data":
                return $this->buyDataCTD($request,$ref,$provider,$payload,$server);

            case "tv":
                return $this->buyTvCTD($request,$ref,$provider,$payload,$server);

            case "electricity":
                return $this->buyElectricityCTD($request,$ref,$provider,$payload,$server);

            case "betting":
                return $this->buyBettingCTD($request,$ref,$provider,$payload,$server);

            case "jamb":
                return $this->buyJambCTD($request,$ref,$provider,$payload,$server);
        }
    }

    public function handlePassage($request, $proceed)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Unauthorized user'
            ]);
        }

        $ref = $request->ref;
        $amount = $proceed[2];
        $service = $proceed[5];

        $input = [
            'ip_address' => $request->ip(),
            'date' => now(),
            'phone' => $request->number,
            'user_name' => $user->user_name,
            'payment_method' => $request->payment,
            'transid' => $ref,
            'version' => $request->header('version'),
            'device_details' => $request->header('device') ?? $request->userAgent(),
            'wallet' => $user->wallet,
            'amount' => $amount,
            'service' => $service
        ];

        if ($request->provider ?? false) {
            $input['network'] = $request->provider;
        }

        /**
         * Prevent Duplicate Transactions
         */
        if (Serverlog::where('transid', $ref)->exists()) {

            $input['status'] = 'Duplicate reference';
            $input['transid'] = $ref . '_dup';

            Serverlog::create($input);

            return response()->json([
                'success' => 0,
                'message' => 'Duplicate transaction detected'
            ]);
        }

        /**
         * Wallet Balance Check
         */
        if ($request->payment === "wallet") {

            if ($user->wallet <= 0 || $amount > $user->wallet) {

                $input['status'] = 'Balance too low';
                Serverlog::create($input);

                return response()->json([
                    'success' => 0,
                    'message' => 'Insufficient wallet balance'
                ]);
            }
        }

        /**
         * Handle Multiple Numbers
         */
        if (str_contains($request->number, ',')) {
            return $this->processMultiplePhones($request, $proceed);
        }

        /**
         * Data Network Override
         */
        if ($service === "data") {
            $input['network'] = $proceed[1];
        }

        /**
         * Log Request
         */
        Serverlog::create($input);

        /**
         * Continue Transaction
         */
        return $this->debitUser(
            $request,
            $proceed,
            $proceed[1],
            $proceed[2],
            $proceed[3],
            $proceed[4],
            $service,
            $proceed[6] ?? '',
            $ref
        );
    }

    public function outputResp(Request $request, $ref, $status, $dada)
    {

        if ($status == 1) {
            $t = Transaction::find($dada['tid']);
            $t->status = "delivered";
            $t->server_response = $dada['server_response'];
            $t->server_ref = $dada['server_ref'] ?? '';
            $t->save();

            if (isset($dada['token'])) {
                $t->description .= " - " . $dada['token'];
                $t->token = $dada['token'];
                $t->save();
                return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
            }
            return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
        }

        if ($status == 4) {
            $t = Transaction::find($dada['tid']);
            $t->status = "pending";
            $t->server_response = $dada['server_response'];
            $t->server_ref = $dada['server_ref'] ?? '';
            $t->save();

            if (isset($dada['token'])) {
                $t->description .= " - " . $dada['token'];
                $t->token = $dada['token'];
                $t->save();
                return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
            }
            return response()->json(['success' => 0, 'message' => 'Oops! Transaction is pending. Kindly check if you have been debited before retry.', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
        }

        $t = Transaction::find($dada['tid']);
        $t->status = "pending";
        $t->server_response = $dada['server_response'];
        $t->save();

        if (isset($dada['message'])) {
            $message = $dada['message'];
        } else {
            $message = 'Your transaction failed';
        }

        ReverseTransactionJob::dispatch($t, "api");

        if (isset($dada['token'])) {
            return response()->json(['success' => 0, 'message' => 'Your transaction failed', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
        }

        return response()->json(['success' => 0, 'message' => $message, 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
    }


    public function processMultiplePhones($request, $proceed)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Unauthorized user'
            ]);
        }

        $numbers = array_filter(array_map('trim', explode(',', $request->number)));
        $count = count($numbers);

        if ($count == 0) {
            return response()->json([
                'success' => 0,
                'message' => 'No valid phone numbers supplied'
            ]);
        }


        $walletAmount = $proceed[2];

        if($proceed[5] == "data") {
            $cgAmount = $proceed[7];
        }else{
            $cgAmount = 0;
        }

        $payment = $request->payment;

        try {

            DB::transaction(function () use ($request, $user, $numbers, $count, $walletAmount, $cgAmount, $payment, $proceed) {

                // Lock user row
                $user = User::where('id', $user->id)->lockForUpdate()->first();

                $charge = 0;

                if ($payment === "wallet") {

                    $charge = $count * $walletAmount;

                    if ($charge > $user->wallet) {
                        throw new \Exception('Wallet balance too low for all numbers');
                    }

                    $user->wallet -= $charge;
                    $user->save();

                } else {

                    $cg = CGWallets::where([
                        ['user_id', $user->id],
                        ['name', $payment]
                    ])->lockForUpdate()->first();

                    if (!$cg) {
                        throw new \Exception('Invalid payment selected');
                    }

                    $charge = $count * $cgAmount;

                    if ($charge > $cg->balance) {
                        throw new \Exception('Insufficient CG wallet balance');
                    }

                    $cg->balance -= $charge;
                    $cg->save();
                }

                /**
                 * Create Server Logs + Dispatch Jobs
                 */
                foreach ($numbers as $index => $num) {

                    $log = Serverlog::create([
                        'ip_address' => $request->ip(),
                        'date' => now(),
                        'phone' => $num,
                        'user_name' => $user->user_name,
                        'payment_method' => $payment,
                        'transid' => $request->ref . "_" . ($index + 1),
                        'version' => $request->header('version'),
                        'device_details' => $request->header('device') ?? $request->userAgent(),
                        'wallet' => $user->wallet,
                        'amount' => $walletAmount,
                        'service' => $proceed[5],
                        'network' => $request->provider ?? null
                    ]);

                    dispatch(
                        (new ATMtransactionserveJob($log->id))
                            ->delay(now()->addSeconds(2))
                    );
                }

            });

        } catch (\Exception $e) {

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => 1,
            'message' => 'Transactions queued successfully. You will receive them within 2 minutes',
            'ref' => $request->ref,
            'debitAmount' => $count * $walletAmount,
            'discountAmount' => 0
        ]);
    }

    private function buildTransactionName($requester, $provider)
    {
        if ($requester == "airtime") {
            return strtoupper($provider)." airtime";
        }

        return strtoupper($provider);
    }

    private function buildTransactionDescription($request, $requester, $provider, $codedName)
    {
        switch ($requester) {

            case "airtime":
                return "{$provider} {$request->amount} airtime on {$request->number}";

            case "data":
                return "{$codedName} on {$request->number}";

            case "electricity":
            case "betting":
                return "{$provider} {$request->amount} on {$request->number}";

            default:
                return "{$request->coded} on {$request->number}";
        }
    }

}
