<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\AgentPdfGeneratorJob;
use App\Jobs\PushNotificationJob;
use App\Models\AppAirtimeControl;
use App\Models\CGBundle;
use App\Models\CGTransaction;
use App\Models\CGWallets;
use App\Models\PndL;
use App\Models\PromoCode;
use App\Models\ReferralPlans;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\VirtualAccount;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $settings = Settings::all();
        foreach ($settings as $setting) {
            $sett[$setting->name] = $setting->value;
        }

        $me['user_name'] = $user->user_name;
        $me['account_details'] = $user->account_number;
        $me['referral_plan'] = $user->referral_plan;
        $me['photo'] = $user->photo;
        $me['email'] = $user->email;
        $me['phoneno'] = $user->phoneno;
        $me['target'] = $user->target ?? " ";
        $me['level'] = $user->level;
        $me['referral_plan'] = $user->referral_plan;
        $me['pin'] = $user->pin;
        $me['status'] = $user->status;
        $me['api_key'] = $user->api_key ?? " ";
        $me['bvn'] = $user->bvn != "";

        $balances['wallet'] = "$user->wallet";
        $balances['bonus'] = "$user->bonus";
        $balances['agent_commision'] = "$user->agent_commision";
        $balances['points'] = "$user->points";
        $balances['general_market'] = $sett['general_market'];


        $services['airtime'] = $sett['airtime'];
        $services['data'] = $sett['data'];
        $services['paytv'] = $sett['paytv'];
        $services['paytv'] = $sett['paytv'];
        $services['resultchecker'] = $sett['resultchecker'];
        $services['rechargecard'] = $sett['resultchecker'];
        $services['electricity'] = $sett['electricity'];
        $services['betting'] = $sett['betting'];
        $services['airtimeconverter'] = $sett['airtimeconverter'];
        $services['biz_verification'] = $sett['biz_verification'];
        $services['foreign_airtime'] = " ";


        $others['min_funding'] = $sett['min_funding'];
        $others['max_funding'] = $sett['max_funding'];
        $others['funding_charges'] = $sett['funding_charges'];
        $others['funding_message'] = $sett['funding_message'];
        $others['live_chat'] = $sett['live_chat'];
        $others['reseller_fee'] = $sett['reseller_fee'];
        $others['support_email'] = $sett['support_email'];
        $others['support_whatsapp'] = $sett['support_whatsapp'];
        $others['support_call'] = $sett['support_call'];
        $others['biz_verification_price_customer'] = $sett['biz_verification_price_customer'];
        $others['reseller_terms'] = $sett['reseller_terms'];
        $others['privacy_policy'] = $sett['privacy_policy'];
        $others['banner'] = $sett['banner'];
        $others['monnify_funding_message'] = $sett['monnify_funding_message'];
        $others['budpay_funding_message'] = $sett['budpay_funding_message'];

        $pay['fund_rave'] = $sett['fund_rave'];
        $pay['fund_paystack'] = $sett['fund_paystack'];
        $pay['fund_monnify'] = $sett['fund_monnify'];
        $pay['fund_budpay'] = $sett['fund_budpay'];
        $pay['fund_paystack_details'] = $sett['fund_paystack_details'];
        $pay['fund_paystack_secret'] = $sett['secret_paystack_details'];
        $pay['fund_rave_details'] = $sett['fund_rave_details'];
        $pay['fund_rave_key'] = $sett['fund_rave_key'];
        $pay['fund_monnify_apikey'] = $sett['fund_monnify_apikey'];
        $pay['fund_monnify_contractcode'] = $sett['fund_monnify_contractcode'];
        $pay['fund_budpay_secret'] = $sett['fund_budpay_secret'];
        $pay['fund_budpay_public'] = $sett['fund_budpay_public'];
        $pay['min_funding'] = $sett['min_funding'];
        $pay['max_funding'] = $sett['max_funding'];
        $pay['funding_charges'] = $sett['funding_charges'];
        $pay['funding_message'] = $sett['funding_message'];
        $pay['monnify_funding_message'] = $sett['monnify_funding_message'];
        $pay['budpay_funding_message'] = $sett['budpay_funding_message'];

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['user' => $me, 'balances' => $balances, 'services' => $services, 'news' => $user->gnews, 'others' => $others, 'payment' => $pay]]);
    }
    public function support()
    {
        $settE=Settings::where('name', 'support_email')->first();
        $settW=Settings::where('name', 'support_whatsapp')->first();
        $settC=Settings::where('name', 'support_call')->first();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['email' => $settE->value, 'whatsapp' => $settW->value, 'call' =>  $settC->value]]);
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'o_password' => 'required',
            'n_password' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = Auth::user();

        if (!Hash::check($input['o_password'], $user->mcdpassword)) {
            return response()->json(['success' => 0, 'message' => 'Wrong Old Password']);
        }

        $user->mcdpassword = Hash::make($input['n_password']);
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Password changed successfully']);

    }

    public function change_pin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'o_pin' => 'required',
            'n_pin' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = Auth::user();
        if ($user->pin != $input['o_pin']) {
            return response()->json(['success' => 0, 'message' => 'Wrong Old Pin']);
        }

        $user->pin = $input['n_pin'];
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Pin changed successfully']);
    }

    public function referrals()
    {
        $user = Auth::user();
        $referrals = User::where('referral', $user->user_name)->select('user_name', 'photo', 'referral_plan', 'reg_date')->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $referrals]);
    }

    public function profile()
    {
        $user = Auth::user();
        $referrals = User::where('referral', $user->user_name)->count();
        $transactions = Transaction::where([['user_name', $user->user_name], ['name', '!=', 'wallet funding'], ['status', '!=', 'delivered']])->count();
        $funds = Transaction::where([['user_name', $user->user_name], ['name', 'wallet funding']])->count();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['referrals' => $referrals, 'funds' => $funds, 'transactions' => $transactions]]);
    }

    public function transactions()
    {
        $user = Auth::user();
        $trans = Transaction::where('user_name', $user->user_name)->OrderBy('id', 'desc')->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function commissions()
    {
        $user = Auth::user();
        $trans = Transaction::where([['user_name', $user->user_name],['name', 'Commission']])->OrderBy('id', 'desc')->simplepaginate();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function freemoney()
    {
        $user = Auth::user();

        $pm = PromoCode::where("used", 0)->inRandomOrder()->first();

        if (!$pm) {
            return response()->json(['success' => 0, 'message' => 'Sorry you did not win, try again later']);
        }

        return response()->json(['success' => 1, 'message' => 'You have won #' . $pm->amount . ' discount. Copy and apply it in your next purchase.', 'data' => $pm->code]);
    }

    public function agentStatus()
    {
        $user = Auth::user();

        $step1 = false;
        $step2 = false;
        $step3 = false;

        if ($user->dob != "") {
            $step1 = true;
        }

        if ($user->document != 0) {
            $step2 = true;
        }

        if ($user->status == "agent") {
            $step3 = true;
        }

        return response()->json(['success' => 1, 'message' => 'Agent status fetched successfully', 'data' => ['step1' => $step1, 'step2' => $step2, 'step3' => $step3]]);
    }

    public function requestAgent(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'full_name' => 'required',
            'company_name' => 'required',
            'bvn' => 'required',
            'dob' => 'required',
            'street' => 'required',
            'state' => 'required',
            'country' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        if ($user->dob == "") {
            return response()->json(['success' => 0, 'message' => 'Data can only be submitted once']);
        }

        $user->full_name = $input['full_name'];
        $user->company_name = $input['company_name'];
        $user->dob = $input['dob'];
        $user->bvn = $input['bvn'];
        $user->address = $input['street'] . "; " . $input['state'] . "; " . $input['country'];
        $user->target = "Agent in progress...";
//            $user->note = $input["note"];
        $user->save();

        AgentPdfGeneratorJob::dispatch($input, $user);

        return response()->json(['success' => 1, 'message' => 'Data submitted successfully, kindly check your mail for progress']);
    }

    public function requestReseller(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'full_name' => 'required',
            'company_name' => 'required',
            'bvn' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $set=Settings::where('name', 'reseller_fee')->first();

        $charges = $set->value;

        if($charges > $user->wallet){
            return response()->json(['success' => 0, 'message' => 'Insufficient fund. Kindly fund wallet and try again']);
        }

        if ($user->status == "reseller") {
            return response()->json(['success' => 0, 'message' => 'You can only request once']);
        }

        $key="key_".uniqid().rand().Carbon::now()->timestamp;

        $user->full_name = $input['full_name'];
        $user->company_name = $input['company_name'];
        $user->bvn = $input['bvn'];
        $user->target = "";
        $user->api_key=$key;
        $user->target = "Reseller in Progress";
        $user->wallet -= $set->value;
        $user->save();

        $inputa["type"]="income";
        $inputa["gl"]="reseller_upgrade";
        $inputa["amount"]=$charges;
        $inputa["narration"]="Being amount charged for reseller upgrade from ".$user->user_name;
        $inputa['date']=Carbon::now();

        PndL::create($inputa);

        $message = "Reseller request from ".$user->user_name;
        PushNotificationJob::dispatch("Holarmie",$message,"Reseller Upgrade Notice");
        PushNotificationJob::dispatch("Softconnet",$message,"Reseller Upgrade Notice");

        return response()->json(['success' => 1, 'message' => 'Data submitted successfully, you can start integrating now.', 'data'=>$key]);
    }

    public function requestAgentDocument()
    {
        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        if ($user->dob == "") {
            return response()->json(['success' => 0, 'message' => 'You need to submit agent request first']);
        }

        $split_addr = explode(';', $user->address);

        $url = "https://mcd.5starcompany.com.ng/app/agent_pdf_generator.php?";
        $params = "full_name=" . urlencode($user->full_name);
        $params .= "&company_name=" . urlencode($user->company_name);
        $params .= "&street_no=" . urlencode($split_addr[0] ?? ' ');
        $params .= "&state=" . urlencode($split_addr[1] ?? '');
        $params .= "&country=" . urlencode($split_addr[2] ?? ' ');
        $params .= "&request=Agent";
        $params .= "&user_name=" . $user->user_name;
        $params .= "&email=" . urlencode($user->email);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resp = json_decode($response, true);

        $docbase = "https://mcd.5starcompany.com.ng/app/docs/";

        return response()->json(['success' => 1, 'message' => 'Document generated successfully', 'data' => $docbase . $resp['filename']]);
    }

    public function agentDocumentation(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'document' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        if ($user->document == 1) {
            return response()->json(['success' => 0, 'message' => 'Document uploaded already']);
        }

        $name = Auth::user()->user_name . ".pdf";
        $folder = "doc";

        if ($this->upload2FBS($input["document"], $folder, $name) == "success") {
            $user->document = 1;
            $user->save();
            return response()->json(['success' => 1, 'message' => 'Document submitted successfully, we are currently reviewing your request which might take days.']);
        } else {
            return response()->json(['success' => 0, 'message' => 'Document upload failed. Try again later']);
        }
    }

    public function bulkAirtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'document' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Document not found', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $ref = "MCD_" . Auth::user()->user_name . "_" . Carbon::now()->timestamp . rand();

        $name = $ref . ".xlsx";
        $folder = "bulkairtime";

        if ($this->upload2FBS($input["document"], $folder, $name) == "success") {
            $noti = new PushNotificationController();
            $noti->PushNotiAdmin("You have a new bulk airtime request with ref => " . $ref, "Bulk Airtime");

            return response()->json(['success' => 1, 'message' => 'File submitted successfully.']);
        } else {
            return response()->json(['success' => 0, 'message' => 'File upload failed. Try again later']);
        }
    }

    public function uploaddp(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'dp' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $name = Auth::user()->user_name . "_" . time() . ".jpg";

        $image=$input['dp'];
        $localfolder = storage_path('app/public/avatar') . '/';
        $base64 = base64_decode($image);

        if (file_put_contents($localfolder . $name, $base64)) {
            $user=User::find(Auth::id());
            $user->photo=$name;
            $user->save();
            return response()->json(['success' => 1, 'message' => 'Image uploaded successfully', 'data' => $name]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Upload failed. Try again later']);
        }

    }

    public function uploaddpURL(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'dp_url' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $user = User::where('user_name', Auth::user()->user_name)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $user=User::find(Auth::id());
        $user->photo=$input['dp_url'];
        $user->save();
        return response()->json(['success' => 1, 'message' => 'Image uploaded successfully', 'data' => $input['dp_url']]);

    }

    public function add_referral(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'referral' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['user_name'] = Auth::user()->user_name;

        $uid = User::where('user_name', $input['user_name'])->first();
        if (!$uid) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        $referral = User::where('user_name', $input['referral'])->first();
        if (!$referral) {
            return response()->json(['success' => 0, 'message' => 'Referral does not exist']);
        }

        if ($uid == $referral) {
            return response()->json(['success' => 0, 'message' => 'You can not add your self as a referral']);
        }

        if ($uid->referral != "") {
            return response()->json(['success' => 0, 'message' => 'Referral has already been added']);
        }

//        $r_referralplan = $referral->referral_plan;

//        $referral_count = User::where('referral', $input['referral'])->count();

//        $rpackage = ReferralPlans::where("name", $r_referralplan)->first();

//        $max = $rpackage->max_users;
//
//        if ($max == $referral_count) {
//            return response()->json(['success' => 0, 'message' => $referral->user_name . " has reached referral limit. Kindly inform the user to upgrade referral plan"]);
//        }

        $uid->referral = $input['referral'];
        $uid->save();

        $referral->points += 1;
        $referral->save();

        try {
            $noti = new PushNotificationController();
            $noti->PushNoti($input['referral'], "Hi " . $input['referral'] . ", " . $input['user_name'] . " has added you as a referral. You will start receiving atleast #5 on every data transaction, to earn more kindly upgrade. Thanks", "Referral");
        } catch (Exception $e) {

        }

        return response()->json(['success' => 1, 'message' => $referral->user_name . " has been added as your referral successfully", 'referral' => $input['referral']]);

    }

    public function referral_upgrade(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        $plan = ReferralPlans::find($input['id']);

        if (!$plan) {
            return response()->json(['success' => 0, 'message' => 'Referral plan does not exist.']);
        }

        $u = User::where('user_name', Auth::user()->user_name)->first();

        if (!$u) {
            return response()->json(['success' => 0, 'message' => $u->user_name . ' does not exist!']);
        }


        if ($u->wallet < $plan->price) {
            return response()->json(['success' => 0, 'message' => $u->user_name . ' wallet balance is currently low.']);
        }


        if ($u->referral_plan == $plan->name) {
            return response()->json(['success' => 0, 'message' => "You have already subscribed to this plan."]);
        }


        $input['name'] = "Referral Upgrade";
        $input['amount'] = $plan->price;
        $input['status'] = 'successful';
        $input['description'] = "Being amount charged for referral upgrade to " . strtoupper($plan->name) . " by " . $u->user_name;
        $input['user_name'] = $u->user_name;
        $input['code'] = 'aru';
        $input['i_wallet'] = $u->wallet;
        $input['f_wallet'] = $input['i_wallet'] - $plan->price;
        $input["ip_address"] = "127.0.0.1:A";
        $input["date"] = date("y-m-d H:i:s");
        $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

        Transaction::create($input);

        $input["type"] = "income";
        $input["gl"] = "referral upgrade";
        $input["narration"] = $input['description'];

        PndL::create($input);

        $u->wallet = $input['f_wallet'];
        $u->referral_plan = $plan->name;
        $u->bonus += $plan->user_earn_amount;
        $u->points += $plan->user_earn_points;
        $u->save();

        $GLOBALS['email'] = $u->email;

        try {
            $data = array('name' => $u->full_name, 'date' => date("D, d M Y"));
            Mail::send('email_referral_upgrade', $data, function ($message) {
                $message->to($GLOBALS['email'], 'MCD Customer')->subject('MCD Referral Upgrade');
                $message->from('info@5starcompany.com.ng', '5Star Inn Company');
            });
        } catch (Exception $e) {

        }

        return response()->json(['success' => 1, 'message' => $u->user_name . ' has been upgraded to ' . $plan->name . ' successfully!']);
    }

    public function requestAPIkey(Request $request)
    {
        $user = User::find(Auth::id());

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Error, User not found']);
        }

        $key="key_".uniqid().rand().Carbon::now();
        $user->api_key=$key;
        $user->save();

        $message = $user->user_name. " just regenerate api key";
        PushNotificationJob::dispatch("Holarmie",$message,"Reseller Key Notice");


        return response()->json(['success' => 1, 'message' => 'Key has been regenerated successfully. Kindly copy now and change it on your platform.', 'data'=>$key]);
    }


    public function cgWallets()
    {
        $cgs=CGWallets::where([['user_id', Auth::id()], ['status', 1]])->get();

        return response()->json(['success' => 1, 'message' => 'Wallets fetched successfully', 'data'=>$cgs]);
    }

    public function cgBundles()
    {
        $cgs=CGBundle::where([['status', 1]])->get();
        $set=Settings::where('name','cg_wallet_bank_details')->first();

        return response()->json(['success' => 1, 'message' => 'Bundles fetched successfully', 'data'=>['bundles'=>$cgs, 'bank'=>$set->value]]);
    }

    public function cgBundleBuy(Request $request){
        $input = $request->all();
        $rules = array(
            'bundle_id' => 'required',
            'paywith'      => 'required',
            'receipt'      => 'nullable'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode($validator->errors()->all()), 'error' => $validator->errors()]);
        }

        if($input['paywith'] == "wallet") {
            $input['charge'] = "yes";
        }else{
            $input['charge'] = "no";
        }

        $data=CGBundle::find($input['bundle_id']);
        if(!$data){
            return response()->json(['success' => 0, 'message' => "Bundle not found"]);
        }

        $user=User::find(Auth::id());
        if(!$user){
            return response()->json(['success' => 0, 'message' => "User not found"]);
        }

        if($input['charge'] == "yes"){
            $bal=$user->wallet;

            if($data->price > $bal){
                return response()->json(['success' => 0, 'message' => "Insufficient balance on customer wallet"]);
            }
        }

        $cw=$data->network." ".$data->type;

        $cgwallet=CGWallets::where(["name" => $cw, "user_id" => Auth::id()])->first();

        if(!$cgwallet){
            return response()->json(['success' => 0, 'message' => "Customer does not have this data wallet"]);
        }

        $cgtrans=CGTransaction::create([
            "bundle_id" => $input['bundle_id'],
            "value" => $data->value,
            "price" =>$data->price,
            "type" => $cw,
            "user_name" => $user->user_name,
            "charge" => $input['charge'],
            "created_by" => Auth::user()->user_name,
            "status" => $input['charge'] == "yes" ? 1 : 0
        ]);

        if($input['charge'] == "yes"){
            $bal=$user->wallet;

            $newBal= $bal - $data->price;

            $tr['name'] = "CG Bundle";
            $tr['user_name'] = $user->user_name;
            $tr['description'] =$data->value . "GB NGN".$data->price. " - ".$data->network. " ".$data->type;
            $tr['code'] = "cgbundle";
            $tr['amount'] = $data->price;
            $tr['status'] = "successful";
            $tr['i_wallet'] = $bal;
            $tr['f_wallet'] = $newBal;
            $tr['extra'] = Auth::user()->user_name;
            Transaction::create($tr);

            $user->wallet=$newBal;
            $user->save();

            $cgwallet->balance+=$data->value;
            $cgwallet->save();
        }else{
            $image = $input["receipt"];
            $photo = $cgtrans->id . ".jpg";

            $decodedImage = base64_decode("$image");
            file_put_contents(storage_path("app/public/cgtransaction/" . $photo), $decodedImage);

            $input["image"] = "cgtransaction/" . $photo;
        }

        $message="User: ".$user->user_name. " Bundle Name: ".$data->display_name. " Bundle Price".$data->price. " Bundle Type".$data->type;
        PushNotificationJob::dispatch("Holarmie",$message,"CG Bundle Notice");
        PushNotificationJob::dispatch("Softconnet",$message,"CG Bundle Notice");

        if($input['charge'] == "yes") {
            return response()->json(['success' => 1, 'message' => "Bundle bought successfully"]);
        }else{
            return response()->json(['success' => 1, 'message' => "Submitted successfully. Kindly wait for admin approval"]);
        }
    }

    public function cgBundleTransfer(Request $request){
        $input = $request->all();
        $rules = array(
            'cgwallet_id' => 'required',
            'user_name'      => 'required',
            'amount'      => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode($validator->errors()->all()), 'error' => $validator->errors()]);
        }


        if($input['amount'] < 1){
            return response()->json(['success' => 0, 'message' => "Invalid Amount"]);
        }

        $user=User::find(Auth::id());

        $r_user=User::where("user_name",$input['user_name'])->orwhere("email",$input['user_name'])->orwhere("phoneno",$input['user_name'])->first();

        if(!$r_user){
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }

        if($r_user->user_name == $user->user_name){
            return response()->json(['success' => 0, 'message' => 'You can not transfer to yourself']);
        }

        $oCGwallet=CGWallets::where(['id' =>$input['cgwallet_id'], 'user_id' => Auth::id() ])->first();
        if(!$oCGwallet){
            return response()->json(['success' => 0, 'message' => "CGWallet not found"]);
        }

        $cgwallet=CGWallets::where(["name" => $oCGwallet->name, "user_id" => $r_user->id])->first();

        if(!$cgwallet){
            return response()->json(['success' => 0, 'message' => "Receiver does not have this data wallet"]);
        }

        if($oCGwallet->balance < $input['amount']){
            return response()->json(['success' => 0, 'message' => "Insufficient Balance"]);
        }

        $obal=$oCGwallet->balance;
        $bal=$cgwallet->balance;

        $onewBal= $obal - $input['amount'];
        $newBal= $bal + $input['amount'];

        $tr['name'] = "CG Bundle Transfer";
        $tr['user_name'] = $user->user_name;
        $tr['description'] =$cgwallet->name ." ".$input['amount'] . "GB to ".$r_user->user_name;
        $tr['code'] = "cgbundle_transfer";
        $tr['amount'] = $input['amount'];
        $tr['status'] = "successful";
        $tr['i_wallet'] = $obal;
        $tr['f_wallet'] = $onewBal;
        $tr['extra'] = Auth::user()->user_name;
        Transaction::create($tr);


        $tr['user_name'] = $r_user->user_name;
        $tr['description'] =$cgwallet->name ." ".$input['amount'] . "GB from ".$user->user_name;
        $tr['i_wallet'] = $bal;
        $tr['f_wallet'] = $newBal;
        $tr['extra'] = Auth::user()->user_name;
        Transaction::create($tr);

        $cgwallet->balance+=$input['amount'];
        $cgwallet->save();

        $oCGwallet->balance-=$input['amount'];
        $oCGwallet->save();

        $message="From: ".Auth::user()->user_name. " To: ".$input['user_name']. " Amount".$input['amount']. " Bundle Type".$oCGwallet->name;
        PushNotificationJob::dispatch("Holarmie",$message,"CG Bundle Transfer Notice");
        PushNotificationJob::dispatch("Softconnet",$message,"CG Bundle Transfer Notice");

        return response()->json(['success' => 1, 'message' => "Bundle transferred successfully"]);
    }


    public function vaccounts()
    {
        $cgs=VirtualAccount::where([['user_id', Auth::id()], ['status', 1]])->get();

        return response()->json(['success' => 1, 'message' => 'Virtual Account fetched successfully', 'data'=>$cgs]);
    }


    public function vaccounts2()
    {
        $cgs=VirtualAccount::where([['user_id', Auth::id()], ['status', 1]])->get();
        $vad=VirtualAccount::where([['user_id', Auth::id()], ['status', 1]])->distinct('provider')->select('provider')->get();

        return response()->json(['success' => 1, 'message' => 'Virtual Account fetched successfully', 'data'=>['vaccts' =>$cgs, 'vtypes' => $vad]]);
    }


    public function index(Request $request)
    {
        $image = 'data:image/jpeg;base64,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';
        $student = app('firebase.firestore')->database()->collection('Student')->document('defT5uT7SDu9K5RFtIdlb.jpg');
        $firebase_storage_path = 'Students/';
        $name = $student->id();
        $localfolder = public_path('firebase-temp-uploads') . '/';
        if (!file_exists($localfolder)) {
            mkdir($localfolder, 0777, true);
        }
        $parts = explode(";base64,", $image);
        $type_aux = explode("image/", $parts[0]);
//        $type = $aux[1];
        $base64 = base64_decode($parts[1]);
        $file = $name . '.png';
        if (file_put_contents($localfolder . $file, $base64)) {
            $uploadedfile = fopen($localfolder . $file, 'r');
            app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path . $name]);
            //will remove from local laravel folder
            unlink($localfolder . $file);
            echo 'success';
        } else {
            echo 'error';
        }
    }


}
