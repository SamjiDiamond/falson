<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotificationJob;
use App\Models\CGBundle;
use App\Models\CGTransaction;
use App\Models\CGWallets;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CGWalletController extends Controller
{


    public function cgWallets()
    {
        $cgs = CGWallets::where([['user_id', Auth::id()], ['status', 1]])->get();

        return response()->json(['success' => 1, 'message' => 'Wallets fetched successfully', 'data' => $cgs]);
    }

    public function cgBundles()
    {
        $cgs = CGBundle::where([['status', 1]])->get();
        $set = Settings::where('name', 'cg_wallet_bank_details')->first();

        return response()->json(['success' => 1, 'message' => 'Bundles fetched successfully', 'data' => ['bundles' => $cgs, 'bank' => $set->value]]);
    }

    public function cgBundleBuy(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'bundle_id' => 'required',
            'paywith' => 'required',
            'receipt' => 'nullable'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode($validator->errors()->all()), 'error' => $validator->errors()]);
        }

        if ($input['paywith'] == "wallet") {
            $input['charge'] = "yes";
        } else {
            $input['charge'] = "no";

            if (!isset($input['receipt'])) {
                return response()->json(['success' => 0, 'message' => 'Receipt is required']);
            }
        }

        $data = CGBundle::find($input['bundle_id']);
        if (!$data) {
            return response()->json(['success' => 0, 'message' => "Bundle not found"]);
        }

        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['success' => 0, 'message' => "User not found"]);
        }

        if ($input['charge'] == "yes") {
            $bal = $user->wallet;

            if ($data->price > $bal) {
                return response()->json(['success' => 0, 'message' => "Insufficient balance on customer wallet"]);
            }
        }

        $cw = $data->network . " " . $data->type;

        $cgwallet = CGWallets::where(["name" => $cw, "user_id" => Auth::id()])->first();

        if (!$cgwallet) {
            return response()->json(['success' => 0, 'message' => "Customer does not have this data wallet"]);
        }

        $cgtrans = CGTransaction::create([
            "bundle_id" => $input['bundle_id'],
            "value" => $data->value,
            "price" => $data->price,
            "type" => $cw,
            "user_name" => $user->user_name,
            "charge" => $input['charge'],
            "created_by" => Auth::user()->user_name,
            "status" => $input['charge'] == "yes" ? 1 : 0
        ]);

        if ($input['charge'] == "yes") {
            $bal = $user->wallet;

            $newBal = $bal - $data->price;

            $tr['name'] = "CG Bundle";
            $tr['user_name'] = $user->user_name;
            $tr['description'] = $data->value . "GB NGN" . $data->price . " - " . $data->network . " " . $data->type;
            $tr['code'] = "cgbundle";
            $tr['amount'] = $data->price;
            $tr['status'] = "successful";
            $tr['i_wallet'] = $bal;
            $tr['f_wallet'] = $newBal;
            $tr['extra'] = Auth::user()->user_name;
            Transaction::create($tr);

            $user->wallet = $newBal;
            $user->save();

            $cgwallet->balance += $data->value;
            $cgwallet->save();
        } else {
            $image = $input["receipt"];
            $photo = "cgtransaction_" . $cgtrans->id . ".jpg";

            $decodedImage = base64_decode("$image");
            file_put_contents(storage_path("app/public/" . $photo), $decodedImage);

            $input["image"] = "cgtransaction/" . $photo;
        }

        $message = "User: " . $user->user_name . " Bundle Name: " . $data->display_name . " Bundle Price" . $data->price . " Bundle Type" . $data->type;
        PushNotificationJob::dispatch("Holarmie", $message, "CG Bundle Notice");
        PushNotificationJob::dispatch("Softconnet", $message, "CG Bundle Notice");

        if ($input['charge'] == "yes") {
            return response()->json(['success' => 1, 'message' => "Bundle bought successfully"]);
        } else {
            return response()->json(['success' => 1, 'message' => "Submitted successfully. Kindly wait for admin approval"]);
        }
    }

    public function cgBundleTransfer(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'cgwallet_id' => 'required',
            'user_name' => 'required',
            'amount' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode($validator->errors()->all()), 'error' => $validator->errors()]);
        }


        if ($input['amount'] < 1) {
            return response()->json(['success' => 0, 'message' => "Invalid Amount"]);
        }

        $user = User::find(Auth::id());

        $r_user = User::where("user_name", $input['user_name'])->orwhere("email", $input['user_name'])->orwhere("phoneno", $input['user_name'])->first();

        if (!$r_user) {
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }

        if ($r_user->user_name == $user->user_name) {
            return response()->json(['success' => 0, 'message' => 'You can not transfer to yourself']);
        }

        $oCGwallet = CGWallets::where(['id' => $input['cgwallet_id'], 'user_id' => Auth::id()])->first();
        if (!$oCGwallet) {
            return response()->json(['success' => 0, 'message' => "CGWallet not found"]);
        }

        $cgwallet = CGWallets::where(["name" => $oCGwallet->name, "user_id" => $r_user->id])->first();

        if (!$cgwallet) {
            return response()->json(['success' => 0, 'message' => "Receiver does not have this data wallet"]);
        }

        if ($oCGwallet->balance < $input['amount']) {
            return response()->json(['success' => 0, 'message' => "Insufficient Balance"]);
        }

        $obal = $oCGwallet->balance;
        $bal = $cgwallet->balance;

        $onewBal = $obal - $input['amount'];
        $newBal = $bal + $input['amount'];

        $tr['name'] = "CG Bundle Transfer";
        $tr['user_name'] = $user->user_name;
        $tr['description'] = $cgwallet->name . " " . $input['amount'] . "GB to " . $r_user->user_name;
        $tr['code'] = "cgbundle_transfer";
        $tr['amount'] = $input['amount'];
        $tr['status'] = "successful";
        $tr['i_wallet'] = $obal;
        $tr['f_wallet'] = $onewBal;
        $tr['extra'] = Auth::user()->user_name;
        Transaction::create($tr);


        $tr['user_name'] = $r_user->user_name;
        $tr['description'] = $cgwallet->name . " " . $input['amount'] . "GB from " . $user->user_name;
        $tr['i_wallet'] = $bal;
        $tr['f_wallet'] = $newBal;
        $tr['extra'] = Auth::user()->user_name;
        Transaction::create($tr);

        $cgwallet->balance += $input['amount'];
        $cgwallet->save();

        $oCGwallet->balance -= $input['amount'];
        $oCGwallet->save();

        $message = "From: " . Auth::user()->user_name . " To: " . $input['user_name'] . " Amount" . $input['amount'] . " Bundle Type" . $oCGwallet->name;
        PushNotificationJob::dispatch("Holarmie", $message, "CG Bundle Transfer Notice");
        PushNotificationJob::dispatch("Softconnet", $message, "CG Bundle Transfer Notice");

        return response()->json(['success' => 1, 'message' => "Bundle transferred successfully"]);
    }

    public function cgPurchaseHistory()
    {
        $cgs = CGTransaction::where([['user_name', Auth::user()->user_name]])->with('cgbundle')->get();

        return response()->json(['success' => 1, 'message' => 'Purchase history fetched successfully', 'data' => $cgs]);
    }

    public function cgUsageHistory()
    {
        $cgs = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'data_%'], ['description', 'NOT LIKE', '%wallet%']])->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Usage history fetched successfully', 'data' => $cgs]);
    }

    public function cgTransferHistory()
    {
        $cgs = Transaction::where([['user_name', Auth::user()->user_name], ['name', 'CG Bundle Transfer']])->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Transfer history fetched successfully', 'data' => $cgs]);
    }


}
