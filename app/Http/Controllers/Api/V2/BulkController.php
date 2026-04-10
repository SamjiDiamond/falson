<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\AppAirtimeControl;
use App\Models\AppDataControl;
use App\Models\User;
use App\Models\Transaction;
use App\Mail\InsufficientBalanceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BulkController extends Controller
{

    function valUser(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required|email',
            'pin' => 'required|min:4|max:6'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

        if(!Hash::check($request->pin,$user->pin)){
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }

        return response()->json(['success' => 1, 'message' => 'Account Validated Successfully', 'balance' => $user->wallet]);
    }

    function buyairtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'email' => 'required|email',
            'pin' => 'required|min:4|max:6'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

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

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

        if(!Hash::check($request->pin,$user->pin)){
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }


        $ref="BLK_AIR_".rand().time();

        try {
            $transaction = DB::transaction(function () use ($request, $user, $airtime, $discount, $input, $ref) {
                // Lock user wallet
                $user = User::where('id', $user->id)->lockForUpdate()->first();

                $amountToDebit = $input['amount'] - $discount;

                if ($user->wallet < $amountToDebit) {
                    Mail::to($user->email)->send(new InsufficientBalanceMail('Insufficient balance for user ' . $user->email . ' for airtime purchase of ' . $input['amount']));
                    return response()->json(['success' => 0, 'message' => 'Insufficient balance']);
                }

                $initialWallet = $user->wallet;
                $user->wallet -= $amountToDebit;
                $user->save();
                $finalWallet = $user->wallet;

                Transaction::create([
                    'name' => strtoupper($input['provider']) . ' BULK Airtime',
                    'description' => $airtime->network. " " .$input['amount'] . ' Airtime on ' . $input['number'],
                    'code' => $airtime->network,
                    'amount' => $amountToDebit,
                    'status' => 'pending',
                    'i_wallet' => $initialWallet,
                    'f_wallet' => $finalWallet,
                    'user_id' => $user->id,
                    'ref' => $ref,
                    'extra' => $discount,
                    'commission' => $discount,
                    'paid_with' => 'wallet',
                    'date' => now(),
                    'ip_address' => $request->ip(),
                    'user_name' => $user->user_name,
                ]);

                return true;
            });

            if ($transaction) {
                return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref]);
            } else {
                return response()->json(['success' => 0, 'message' => 'Transaction failed']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

        function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'pin' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

//        if(!Hash::check($request->pin,$user->pin)){
//            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
//        }

        if ($request->get('pin') != $user->pin) {
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }

        $ref="BLK_DATA_".rand().time();

        try {
            $transaction = DB::transaction(function () use ($request, $user, $rac, $input, $ref) {
                // Lock user wallet
                $user = User::where('id', $user->id)->lockForUpdate()->first();

                $amountToDebit = $rac->amount;

                if ($user->wallet < $amountToDebit) {
                    Mail::to($user->email)->send(new InsufficientBalanceMail('Insufficient balance for user ' . $user->email . ' for data purchase of ' . $rac->name));
                    return response()->json(['success' => 0, 'message' => 'Insufficient balance']);
                }

                $discount = 0;

                $initialWallet = $user->wallet;
                $user->wallet -= $amountToDebit;
                $user->save();
                $finalWallet = $user->wallet;

                Transaction::create([
                    'name' => strtoupper($input['provider'])." BULK DATA",
                    'description' => $rac->name . ' Data on ' . $input['number'],
                    'code' => $rac->coded,
                    'amount' => $amountToDebit,
                    'status' => 'pending',
                    'i_wallet' => $initialWallet,
                    'f_wallet' => $finalWallet,
                    'user_id' => $user->id,
                    'ref' => $ref,
                    'extra' => $discount,
                    'commission' => $discount,
                    'paid_with' => 'wallet',
                    'date' => now(),
                    'ip_address' => $request->ip(),
                    'user_name' => $user->user_name,
                ]);

                return true;
            });

            if ($transaction) {
                return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref]);
            } else {
                return response()->json(['success' => 0, 'message' => 'Transaction failed']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

}
