<?php

namespace App\Http\Controllers;

use App\Jobs\PushNotificationJob;
use App\Jobs\WithdrawalPayoutJob;
use App\Models\PndL;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdraw;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $input = $request->all();

        if($input) {
            $user_name = $input['user_name'];
            $status = $input['status'];
            $medium = $input['medium'];
            $reference = $input['ref'];
            $amount = $input['amount'];
            $date = $input['date'];

            // Instantiates a Query object
            $wallet = Wallet::OrderBy('id', 'desc')
                ->when(isset($user_name), function ($query) use ($user_name) {
                    $query->where('user_name', 'LIKE', "%$user_name%");
                })
                ->when(isset($status), function ($query) use ($status) {
                    $query->where('status', 'LIKE', "%$status%");
                })
                ->when(isset($medium), function ($query) use ($medium) {
                    $query->where('medium', 'LIKE', "%$medium%");
                })
                ->when(isset($reference), function ($query) use ($reference) {
                    $query->where('ref', 'LIKE', "%$reference%");
                })
                ->when(isset($amount), function ($query) use ($amount) {
                    $query->where('amount', "$amount");
                })
                ->when(isset($date), function ($query) use ($date) {
                    $query->where('date', 'LIKE', "%$date%");
                })
                ->paginate(25);
        }else{
            $wallet = Wallet::orderBy('id', 'desc')->paginate(25);
        }

        return view('wallets', ['data' => $wallet]);
    }

    public function addfund(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'type'      => 'required',
            'amount' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $amount=$input["amount"];
            $user= User::where('user_name', trim($input["user_name"]))->orwhere('email', trim($input["user_name"]))->orwhere('phoneno', trim($input["user_name"]))->first();
            $notify_description="Dear ".$user->user_name.", your wallet balance has been ".$input['type']."ed with ".$amount.". Thanks for choosing PLANETF.";

            if($user){
                $input["description"]=$input["user_name"] . " wallet ".$input["type"]. " with the sum of #".$input["amount"]." ". $input["odescription"];
                $input["i_wallet"]=$user->wallet;

                if($input['type'] == "credit"){
                    $input["f_wallet"]=$input["i_wallet"] + $input["amount"];
                }else{
                    $input["f_wallet"]=$input["i_wallet"] - $input["amount"];
                }

                $input["ip_address"]="127.0.0.1";
                $input["code"]="admin_".$input["type"];
                $input["status"]="successful";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]="wallet ".$input["type"];
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                $user->wallet=$input["f_wallet"];
                $user->save();

                PushNotificationJob::dispatch($input['user_name'], $notify_description, "Wallet Funded");

                return redirect('/addfund')->with('success', $input["user_name"]. ' wallet funded successfully!');
            }else{
                $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/addfund')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        } else {

            return redirect('/addfund')
                ->withErrors($validator)
                ->withInput($input);
        }
    }

    public function withdrawal_list()
    {
        $with = Withdraw::latest()->paginate();
        return view('withdrawal', ['data' => $with]);
    }

    public function withdrawal_submit(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required|int');

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return back()->with('error', 'Missing required key');
        }

        $with = Withdraw::find($input['id']);

        if (!$with) {
            return back()->with('error', 'Kindly provide a valid data');
        }

        if ($with->status == 1) {
            return back()->with('error', 'Transaction has been completed earlier');
        }

        $with->status = 2;
        $with->save();

        WithdrawalPayoutJob::dispatch($with)->delay(Carbon::now()->addSeconds(1));

        return back()->with('success', 'Withdrawal process has been initiated in background');
    }

    public function withdrawal_reject(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required|int');

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return back()->with('error', 'Missing required key');
        }

        $with = Withdraw::find($input['id']);

        if (!$with) {
            return back()->with('error', 'Kindly provide a valid data');
        }

        if ($with->status == 1) {
            return back()->with('error', 'Transaction has been completed earlier');
        }

        $with->status = 4;
        $with->save();

        return back()->with('success', 'Withdrawal has been rejected');
    }
}
