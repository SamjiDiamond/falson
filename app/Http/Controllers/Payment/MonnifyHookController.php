<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\NewAccountGiveaway;
use App\Jobs\SendoutMonnifyHookJob;
use App\Models\PndL;
use App\Models\Serverlog;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonnifyHookController extends Controller
{
    public function index(Request $request){
        $input = $request->all();

        $data2= json_encode($input);

        Log::info("Monnify Webhook");
        Log::info($data2);

        $paymentstatus= $input['eventData']['paymentStatus'];
        $transactionreference= $input['eventData']['transactionReference'];
        $paymentreference= $input['eventData']['paymentReference'];
        $paymentamount= $input['eventData']['amountPaid'];
        $paymentmethod= $input['eventData']['paymentMethod'];
        $paymentdesc =$input['eventData']['paymentDescription'];
        $paidon= $input['eventData']['paidOn'];
        $product_type= $input['eventData']['product']['type'];
        $product_reference= $input['eventData']['product']['reference'];
        $transactionhash= "";
        $transactionhashME= hash('SHA512', env("MONNIFY_CLIENTSECRET")."|". $paymentreference."|". $paymentamount ."|".$paidon."|".$transactionreference);
        $paymentamount= (int)$input['eventData']['amountPaid'];

//        echo $transactionhashME;

        DB::table('tbl_webhook_monnify')->insert(['payment_reference'=> $paymentreference, 'transaction_reference'=>$transactionreference, 'amount'=>$paymentamount, 'payment_method'=> $paymentmethod, 'product_type'=>$product_type, 'product_reference'=>$paymentreference, 'transaction_hash'=> $transactionhash, 'transaction_hashME'=>$transactionhashME, 'payment_desc'=>$paymentdesc, 'extra'=>$data2]);


        if($paymentstatus !== "PAID"){
            return "!Paid transaction";
        }

//        if($transactionhash != $transactionhashME){
//            return "Invalid transaction signature";
//        }

        $cfee=$input['eventData']['totalPayable']-$input['eventData']['settlementAmount'];

        if($product_type === "MOBILE_SDK"){
            $this->SDK($paymentamount, $paymentreference, $cfee);
        }

        if($product_type === "RESERVED_ACCOUNT"){
            $acctd_name= $input['eventData']['paymentSourceInformation'][0]['accountName'];
            $my_acctno= $input['eventData']['destinationAccountInformation']['accountNumber'];

            $atm=new ATMmanagerController();
            $atm->RAfundwallet($acctd_name, $paymentamount, $product_reference, $transactionreference, $cfee, $input, $my_acctno, "Monnify");
        }

        return "success";
    }

    private function SDK($amount, $reference, $cfee){

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
                $at->atmfundwallet($fun, $amount, $reference, "Monnify", $cfee);
            }
        }
        echo "no way forward";
    }

}
