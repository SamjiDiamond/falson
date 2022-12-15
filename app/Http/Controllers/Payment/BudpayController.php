<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\KorapayHookJob;
use App\Jobs\NewAccountGiveaway;
use App\Jobs\SendoutMonnifyHookJob;
use App\Models\PndL;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudpayController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $data2 = json_encode($input);

        DB::table('tbl_webhook_budpay')->insert(['payment_reference' => $input['data']['reference'] ?? '', 'payment_id' => $input['data']['id'], 'status' => $input['data']['status'], 'amount' => $input['data']['amount'], 'fees' => $input['data']['fees'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);


        if ($input['notify'] != "transaction") {
            return "transaction expected";
        }


        if ($input['notifyType'] != "successful") {
            return "successful expected";
        }

        $email=$input['data']['customer']['email'];

        $u=User::where('email', $email)->first();

        if(!$u){
            return "User not found";
        }

        $status = $input['data']['status'];
        $reference = $input['data']['reference'];
        $amount = $input['data']['amount'];
        $fee = $input['data']['fees'];

        if ($status != "success") {
            return "Success status expected";
        }

        $tra = Serverlog::where('transid', $reference)->first();
        if($tra){
            if ($tra->status!="completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm=new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun=Wallet::where('ref',$reference)->first();
        if($fun){
            if ($fun->status!="completed") {
                $fun->status='completed';
                $fun->save();

                $at=new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Budpay", $input['data']['fees']);
            }
        }

        $gateway_response=json_decode($input['data']['gateway_response']);
        $accountNumber=$gateway_response->craccount;
        $originatorname=$gateway_response->originatorname;

        $atm=new ATMmanagerController();
        $atm->RAfundwallet($originatorname, $amount, $u->user_name, $reference, $fee, $input, $accountNumber,"Budpay");

        return "success";
    }
}
