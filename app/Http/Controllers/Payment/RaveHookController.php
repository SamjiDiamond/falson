<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Serverlog;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaveHookController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $data2= json_encode($input);

        DB::table('tbl_webhook_rave')->insert(['payment_reference'=> $input['data']['tx_ref'], 'rave_reference'=>$input['data']['flw_ref'], 'status'=>$input['data']['status'], 'amount'=>$input['data']['amount'], 'fees'=> $input['data']['app_fee'], 'charged_amount'=> $input['data']['charged_amount'], 'customer_id'=>$input['data']['customer']['id'], 'email'=>$input['data']['customer']['email'], 'rave_signature'=> $request->header('Verif-Hash'), 'paid_at'=>$input['data']['created_at'], 'type'=>$input['data']['payment_type'], 'remote_address'=>$_SERVER['REMOTE_ADDR'], 'extra'=>$data2]);


// retrieve the signature sent in the reques header's.
        $signature = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');

        /* It is a good idea to log all events received. Add code *
         * here to log the signature and body to db or file       */

        if (!$signature) {
            // only a post with Flutterwave signature header gets our attention
            echo "does not have signature";
            exit();
        }

// Store the same signature on your server as an env variable and check against what was sent in the headers
        $local_signature = env('RAVE_SECRET_HASH');

// confirm the event's signature
        if( $signature !== $local_signature ){
            // silently forget this ever happened
            echo "signature does not match";
            exit();
        }


        if($input['event']!="charge.completed"){
            return "charge->success expected";
        }
        $status=$input['data']['status'];
        $reference=$input['data']['tx_ref'];
        $amount=$input['data']['amount'];

        if($status!="successful"){
            return "Success status expected";
        }

        $fee=$input['data']['charged_amount'] - $input['data']['app_fee'] - $input['data']['merchant_fee'];
        $cfee=$input['data']['amount']-$fee;

        $tra=Serverlog::where('transid',$reference)->first();
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
                $at->atmfundwallet($fun, $amount, $reference, "Rave", $cfee);
            }
        }

        return "success";
    }
}



/*
{
    "event": "charge.completed",
  "data": {
    "id": 285959875,
    "tx_ref": "Links-616626414629",
    "flw_ref": "PeterEkene/FLW270177170",
    "device_fingerprint": "a42937f4a73ce8bb8b8df14e63a2df31",
    "amount": 100,
    "currency": "NGN",
    "charged_amount": 100,
    "app_fee": 1.4,
    "merchant_fee": 0,
    "processor_response": "Approved by Financial Institution",
    "auth_model": "PIN",
    "ip": "197.210.64.96",
    "narration": "CARD Transaction ",
    "status": "successful",
    "payment_type": "card",
    "created_at": "2020-07-06T19:17:04.000Z",
    "account_id": 17321,
    "customer": {
        "id": 215604089,
      "name": "Yemi Desola",
      "phone_number": null,
      "email": "user@gmail.com",
      "created_at": "2020-07-06T19:17:04.000Z"
    },
    "card": {
        "first_6digits": "123456",
      "last_4digits": "7889",
      "issuer": "VERVE FIRST CITY MONUMENT BANK PLC",
      "country": "NG",
      "type": "VERVE",
      "expiry": "02/23"
    }
  }*/
