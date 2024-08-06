<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Models\AirtimeCountry;
use App\Models\AppOtherServices;
use App\Models\FAQs;
use App\Models\GeneralMarket;
use App\Models\PromoCode;
use App\Models\ReferralPlans;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{
    public function paymentcheckout()
    {
        $settings = Settings::all();
        foreach ($settings as $setting) {
            $sett[$setting->name] = $setting->value;
        }

        $data['rave'] = $sett['fund_rave'];
        $data['paystack'] = $sett['fund_paystack'];
        $data['payant'] = $sett['fund_payant'];
        $data['bank'] = $sett['fund_bank'];
        $data['monnify'] = $sett['fund_monnify'];
        $data['korapay'] = $sett['fund_korapay'];
        $data['wallet'] = $sett['fund_bank'];
        $data['budpay'] = $sett['fund_budpay'];

        $d['paystack_public'] = $sett['fund_paystack_details'];
        $d['paystack_secret'] = $sett['secret_paystack_details'];
        $d['rave_public'] = $sett['fund_rave_details'];
        $d['rave_enckey'] = $sett['fund_rave_key'];
        $d['monnify_apikey'] = $sett['fund_monnify_apikey'];
        $d['monnify_contractcode'] = $sett['fund_monnify_contractcode'];
        $d['budpay_secret'] = $sett['fund_budpay_secret'];
        $d['budpay_public'] = $sett['fund_budpay_public'];

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => ['status' => $data, 'details' => $d]]);
    }

    public function referralPlans()
    {
        $data = ReferralPlans::get();

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => $data]);
    }

    public function fundWallet(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'amount' => 'required',
            'medium' => 'required',
            'ref' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['deviceid'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $input['user_name'] = Auth::user()->user_name;

        $input['o_wallet'] = Auth::user()->wallet;

        $input['n_wallet'] = $input['o_wallet'] + $input['amount'];

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $input['date'] = Carbon::now();
        $input['status'] = "pending";

        Wallet::create($input);

        return response()->json(['success' => 1, 'message' => 'Fund Logged for further processing']);
    }

    public function withdraw(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'wallet' => 'required',
            'amount' => 'required',
            'ref' => 'required',
            'account_number' => 'required',
            'bank' => 'required',
            'bank_code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['user_name'] = Auth::user()->user_name;

        $input['version'] = $request->header('version');

        $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $u = User::where("user_name", $input['user_name'])->first();


        if ($input['wallet'] == "Mega Bonus") {
            if ($u->bonus < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $u->bonus -= $input['amount'];
        }

        if ($input['wallet'] == "Commission") {
            if ($u->agent_commision < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $u->agent_commision -= $input['amount'];
        }

        $u->save();

        Withdraw::create($input);

        $noti = new PushNotificationController();
        $noti->PushNoti('Izormor2019', "There is a pending withdrawal request, kindly approve on the dashboard.", "Withdrawal Request");

        return response()->json(['success' => 1, 'message' => 'Withdrawal logged successfully']);

    }

    function banklist()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.paystack.co/bank',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
                "Cache-Control: no-cache",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);


        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $rep['data']]);
    }

    function verifyBank(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'accountnumber' => 'required',
            'code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.paystack.co/bank/resolve?account_number=' . $input['accountnumber'] . '&bank_code=' . $input['code'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
                "Cache-Control: no-cache",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);

        if ($rep['status']) {
            return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $rep['data']['account_name']]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to resolve account details']);
        }


    }

    public function getGmTrans(Request $request)
    {
        $set = Settings::where('name', 'general_market')->first();

        $trans = GeneralMarket::OrderBy('id', 'desc')->limit(300)->get();
        if ($trans->isEmpty()) {
            return response()->json(['success' => 1, 'message' => 'No transactions found', 'wallet' => $set->value]);
        }
        return response()->json(['success' => 1, 'message' => 'General Market Transactions Fetched', 'data' => $trans, 'wallet' => $set->value]);
    }

    public function getPoints()
    {
        $us = User::orderBy('points', 'desc')->limit(10)->get(['full_name', 'user_name', 'points', 'photo']);
        $use = User::orderBy('points', 'desc')->get(['full_name', 'user_name', 'points', 'photo']);
        $settings = Settings::where('name', 'leaderboard_banner')->first();
        $rank = 1;

        foreach ($use as $item) {
            if ($item->user_name == Auth::user()->user_name) {
                break;
            } else {
                $rank++;
            }
        }

        return response()->json(['success' => 1, 'message' => 'Leaderboard Fetched successfully', 'rank' => $rank, 'data' => $us, 'banner' => $settings->value]);
    }

    public function getEqv(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'from' => 'required',
            'to' => 'required',
            'amount' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $fc = AirtimeCountry::where("currencyCode", $input['from'])->first();
        if (!$fc) {
            return response()->json(['status' => 0, 'message' => 'Kindly provide a valid country code for From']);
        }

        if ($fc->status != 1) {
            return response()->json(['status' => 0, 'message' => $input['from'] . " is currently not active"]);
        }

        if ($fc->USDrate == 0) {
            return response()->json(['status' => 0, 'message' => $input['from'] . " does not have rate"]);
        }

        $tc = AirtimeCountry::where("currencyCode", $input['to'])->first();
        if (!$tc) {
            return response()->json(['status' => 0, 'message' => 'Kindly provide a valid country code for To']);
        }

        if ($tc->status != 1) {
            return response()->json(['status' => 0, 'message' => $input['to'] . " is currently not active"]);
        }

        if ($tc->USDrate == 0) {
            return response()->json(['status' => 0, 'message' => $input['to'] . " does not have rate"]);
        }

        $usd = $input['amount'] / $fc->USDrate;

        $frency = $tc->USDrate * $usd;

        $docto = env('COINRENCY_DOCTOR', 20) * $input['amount'];

        $doctor = $frency + $docto;

        return response()->json(['success' => 1, 'message' => 'Converted successfully', 'data' => ['rate' => $doctor]]);
    }

    public function flutterwavePayment(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'ref' => 'required',
            'desc' => 'required',
            'amount' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $sett=Settings::where('name', 'fund_rave_key')->first();

        $data = '{
   "tx_ref":"' . $input['ref'] . '",
   "amount":"' . $input['amount'] . '",
   "currency":"NGN",
   "redirect_url":"https://example.com/success",
   "payment_options":"card,barter,mobilemoneyghana,qr,ussd,credit,payattitude",
   "meta":{
      "desc":"' . $input['desc'] . '"
   },
   "customer":{
      "email":"' . Auth::user()->email . '",
      "phonenumber":"' . Auth::user()->phoneno . '",
      "name":"' . Auth::user()->user_name . ' PlanetF"
   },
   "customizations":{
      "title":"PLANETF",
      "description":"...",
      "logo":"https://softconnet.com.ng/img/PlanetfLogo.png"
   }
}';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $sett->value,
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

//        echo $response;

        curl_close($curl);

//        $response='/{ "status":"success", "message":"Hosted Link", "data":{ "link":"https://api.flutterwave.com/v3/hosted/pay/f524c1196ffda5556341" } }';

        $rep = json_decode($response, true);

        if ($rep['status'] != "success") {
            return response()->json(['success' => 0, 'message' => 'An error occurred when processing your action.']);
        }

        return response()->json(['success' => 1, 'message' => 'Payment link generated successfully', 'data' => ['link' => $rep['data']['link']], 'link' => $rep['data']['link']]);
    }

    public function getFAQs(Request $request)
    {
        $faqs=FAQs::where('status', 1)->get();

        return response()->json(['success' => 1, 'message' => 'FAQ fetched successfully', 'data' => $faqs]);
    }

    public function getPromoCode(Request $request)
    {
        $faqs=PromoCode::where('used', 0)->select('code','amount','used','reuseable','usedby','generated_for','created_at')->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $faqs]);
    }

    public function getOtherService(Request $request)
    {
        $faqs=AppOtherServices::where('status', 1)->get();

        return response()->json(['success' => 1, 'message' => 'Other service fetched successfully', 'data' => $faqs]);
    }

    public function insertRechargecard(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'network' => 'required',
            'amount' => 'required',
            'quantity' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if ($validator->passes()) {

            $user=Auth::user();
            $input["user_name"] = $user->user_name;

            $uid = $input['user_name'];
            $net = $input['network'];
            $qty = $input['quantity'];
            $price = $input['amount'];
            $p = $price * $qty;
            $ref = $input["ref"];

            if ($p > $user->wallet) {
                return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
            }

            $input["i_wallet"] = $user->wallet;
            $GLOBALS['email'] = $user->email;
            $input['f_wallet'] = $input["i_wallet"] - $p;
            $input['amount'] = $p;

            $input['description'] = $uid . " order " . $net . "(#" . $price . ") recharge card of " . $qty . " quantity";
            $input['extra'] = "qty-" . $qty . ", net-" . $net . ", amount-" . $price . ", ref-" . $ref;
            $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $input['date'] = Carbon::now();
            $input['name'] = 'Recharge Card';
            $input['status'] = 'pending';
            $input['code'] = 'rcc';

            Transaction::create($input);

            $user->wallet = $input['f_wallet'];
            $user->save();

//            $data = array('name' => $user->user_name, 'date' => date("D, d M Y"));
//            Mail::send('email_rechargecard_notice', $data, function ($message) {
//                $message->to($GLOBALS['email'], 'MCD Customer')->subject('MCD Rechargecard');
//                $message->from('info@5starcompany.com.ng', '5Star Inn Company');
//            });

            return response()->json(['success' => 1, 'message' => 'Transactions submitted Successfully']);

        } else {
            // required field is missing
            // echoing JSON response
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }
    }

    public function moveFunds(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'wallet' => 'required',
            'amount' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['user_name'] = Auth::user()->user_name;

        $input['version'] = $request->header('version');

        $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $u = User::where("user_name", $input['user_name'])->first();

        $amount = $input['amount'];
        $ref = "PLF_MV_" . time();
        $prevb = 0;
        $method = "";


        if ($input['wallet'] == "bonus") {
            $prevb = $u->bonus;
            $method = "bonus";
            if ($u->bonus < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Insufficient balance on your bonus wallet']);
            }
            $u->bonus -= $input['amount'];
        }

        if ($input['wallet'] == "commission") {
            $prevb = $u->agent_commision;
            $method = "commission";
            if ($u->agent_commision < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Insufficient balance on your commision wallet']);
            }
            $u->agent_commision -= $input['amount'];
        }

        if ($method == "") {
            return response()->json(['success' => 0, 'message' => 'Kindly provide correct wallet type']);
        }

        $prevw = $u->wallet;
        $u->wallet += $amount;
        $u->save();


        $tr['name'] = "wallet funding";
        $tr['description'] = "Moved " . $amount . " " . $method . " to wallet";
        $tr['amount'] = $amount;
        $tr['date'] = Carbon::now();
        $tr['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $tr['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $tr['user_name'] = Auth::user()->user_name;
        $tr['ref'] = $ref;
        $tr['server'] = "";
        $tr['server_response'] = "";
        $tr['code'] = "mfunds";
        $tr['status'] = "successful";
        $tr['extra'] = $method;
        $tr['i_wallet'] = $prevw;
        $tr['f_wallet'] = $u->wallet;

        $t = Transaction::create($tr);

        $tr['name'] = ucfirst($method);
        $tr['i_wallet'] = $prevb;
        $tr['f_wallet'] = $tr['i_wallet'] - $amount;

        $t = Transaction::create($tr);

        return response()->json(['success' => 1, 'message' => 'Funds moved to your wallet successfully']);

    }

}
