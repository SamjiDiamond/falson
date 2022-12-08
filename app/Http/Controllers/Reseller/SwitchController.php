<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Models\PndL;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SwitchController extends Controller
{

    public function junction(Request $request)
    {
        $input = $request->all();


        if (!isset($input['service'])) {
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        switch ($input['service']) {
            case "balance":
                return $this->myBalance($request);
            case "withdraw_commission":
                return $this->withdrawCommision($request);
            case "transactions":
                return $this->myTransactions($request);
            case "withdrawals":
                return $this->myWithdrawals($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }

    public function validateService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        if (!isset($input['phone'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add phone to your request']);
        }

        if (!isset($input['coded'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add coded to your request']);
        }


        $s=new ValidateController();

        switch ($input['service']) {
            case "electricity":
                return $s->electricity_server6($input['phone'], $input['coded']);
            case "tv":
                return $s->tv_server1($input['phone'], $input['coded']);
            case "betting":
                return $s->betting_server7($input['phone'], strtoupper($input['coded']));
            case "smile":
                return $s->tv_server1($input['phone'], strtolower($input['coded']));
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }
    }


    public function payService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        if (!isset($input['phone'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add phone to your request']);
        }

        if (!isset($input['coded'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add coded to your request']);
        }


        $s=new PayController();

        switch ($input['service']) {
            case "airtime":
                return $s->buyAirtime($request);
            case "data":
                return $s->buyData($request);
            case "tv":
                return $s->buyTV($request);
            case "electricity":
                return $s->buyElectricity($request);
            case "betting":
                return $s->buyBetting($request);
            case "airtime2wallet":
                return $s->a2cash($request);
            case "airtime2bank":
                return $s->a2bank($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }


    public function listService(Request $request)
    {
        $input = $request->all();

        if (!isset($input['service'])) {
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        $lc = new ListController();

        switch ($input['service']) {
            case "all":
                return $lc->all();
            case "electricity":
                return $lc->electricity();
            case "tv":
                return $lc->tv();
            case "airtime":
                return $lc->airtime();
            case "data":
                return $lc->data($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }

    public function myBalance(Request $request)
    {

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact support']);
        }

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['wallet' => $user->wallet, 'commission' => $user->bonus]]);
    }

    public function myTransactions(Request $request)
    {

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact support']);
        }

        $transaction = Transaction::where("user_name", $user->user_name)->latest()->get();

        return response()->json(['success' => 1, 'message' => 'Transactions Fetched successfully', 'data' => $transaction->makeHidden(['server_response', 'server_ref'])]);
    }

    public function myWithdrawals(Request $request)
    {

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact support']);
        }

        $transaction = Withdraw::where("user_name", $user->user_name)->latest()->get();

        return response()->json(['success' => 1, 'message' => 'Withdrawals Fetched successfully', 'data' => $transaction->makeHidden(['server_response', 'server_ref'])]);
    }

    public function withdrawCommision(Request $request)
    {
        $input = $request->all();

        $rules = array(
            'account_number' => 'required|max:10',
            'bank' => 'required',
            'bank_code' => 'required',
            'wallet' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact support']);
        }

        $valid_wallets = ['commission', 'wallet'];
        $valid = false;

        foreach ($valid_wallets as $valid) {
            if (strtolower($input['wallet']) == $valid) {
                $valid = true;
            }
        }

        if (!$valid) {
            return response()->json(['success' => 0, 'message' => 'Invalid wallet type supplied. Valid wallets are Commission, Wallet']);
        }

        $input['user_name'] = $user->user_name;
        $input['ref'] = "RW" . Carbon::now()->timestamp . rand();
        $input['device_details'] = "api";
        $input['version'] = "2.0";
        $fee = env('WITHDRAWAL_FEE');
        $total = $input['amount'] + $fee;

        $wallet_bal = 0;

        if (strtolower($input['wallet']) == "commission") {
            if ($user->agent_commision < $total) {
                return response()->json(['success' => 0, 'message' => 'Low commission balance']);
            }
            $wallet_bal = $user->agent_commision;
            $user->agent_commision -= $total;
        }

        if (strtolower($input['wallet']) == "wallet") {
            if ($user->wallet < $total) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $wallet_bal = $user->wallet;
            $user->wallet -= $total;
        }

        if ($input['amount'] > env('WITHDRAWAL_LIMIT')) {
            return response()->json(['success' => 0, 'message' => 'Withdrawal limit exceeded. Max is ' . env('WITHDRAWAL_LIMIT')]);
        }

        $user->save();

        Withdraw::create($input);

        $tr['name'] = "Withdrawal";
        $tr['description'] = $input['wallet'] . " withdrawal on " . $input['account_number'] . " (" . $input['bank'] . ")";
        $tr['amount'] = $input['amount'];
        $tr['date'] = Carbon::now();
        $tr['device_details'] = "api";
        $tr['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $tr['i_wallet'] = $wallet_bal;
        $tr['f_wallet'] = $tr['i_wallet'] - $tr['amount'];
        $tr['user_name'] = $user->user_name;
        $tr['ref'] = $input['ref'];
        $tr['code'] = "withdraw";
        $tr['server'] = "auto";
        $tr['server_response'] = "";
        $tr['payment_method'] = "wallet";
        $tr['transid'] = $input['ref'];
        $tr['status'] = "submitted";
        $tr['extra'] = "";
        Transaction::create($tr);

        $tr['description'] = "Fee on " . $tr['description'];
        $tr['code'] = "withdraw_fee";
        $tr['i_wallet'] = $tr['f_wallet'];
        $tr['f_wallet'] = $tr['i_wallet'] - $fee;
        $tr['amount'] = $fee;
        Transaction::create($tr);

        $input["type"] = "income";
        $input["gl"] = "Withdrawal Fee";
        $input["amount"] = $fee;
        $input['date'] = Carbon::now();
        $input["narration"] = "Being withdrawal payout fee on " . $input['ref'] . " via reseller " . $input['wallet'];

        PndL::create($input);


        $noti = new PushNotificationController();
        $noti->PushNoti('Izormor2019', "There is a pending withdrawal request, kindly approve on the dashboard.", "Withdrawal Request");

        return response()->json(['success' => 1, 'message' => 'Withdrawal logged successfully', 'ref' => $input['ref']]);
    }

}
