<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Serverlog;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaylonyHookController extends Controller
{
    function index(Request $request){
        $input = $request->all();

        $data2 = json_encode($input);

        try {
            DB::table('tbl_webhook_paylony')->insert(['payment_reference' => $input['reference'], 'payment_id' => $input['trx'], 'status' => $input['status'], 'amount' => $input['amount'], 'fees' => $input['fee'], 'receiving_account' => $input['receiving_account'], 'paid_at' => Carbon::now(), 'channel' => $input['channel'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);
        } catch (\Exception $e) {
            Log::info("Paylony crashed. - " . $data2);
        }


        $status = $input['status'];
        $reference = $input['reference'];
        $amount = $input['amount'];
        $fee = $input['fee'];

        if ($status != "00") {
            return "Success status expected";
        }

        $accountNumber=$input['receiving_account'];
        $originatorname=$input['sender_account_name'];


        $fv=VirtualAccount::where('account_number', $input['receiving_account'])->latest()->first();

        $u=User::find($fv->user_id);

        if(!$u){
            return "User not found";
        }


        $atm=new ATMmanagerController();
        $atm->RAfundwallet($originatorname, $amount, $u->user_name, $reference, $fee, $input, $accountNumber,"Paylony");

        return "success";

    }
}
