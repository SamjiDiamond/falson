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

class MonnifyHookController extends Controller
{
    public function index(Request $request){
        $input = $request->all();

        $data2= json_encode($input);

        $paymentstatus= $input['paymentStatus'];
        $transactionreference= $input['transactionReference'];
        $paymentreference= $input['paymentReference'];
        $paymentamount= $input['amountPaid'];
        $paymentmethod= $input['paymentMethod'];
        $paymentdesc =$input['paymentDescription'];
        $paidon= $input['paidOn'];
        $product_type= $input['product']['type'];
        $product_reference= $input['product']['reference'];
        $transactionhash= $input['transactionHash'];
        $transactionhashME= hash('SHA512', env("MONNIFY_CLIENTSECRET")."|". $paymentreference."|". $paymentamount ."|".$paidon."|".$transactionreference);
        $paymentamount= (int)$input['amountPaid'];

//        echo $transactionhashME;

        DB::table('tbl_webhook_monnify')->insert(['payment_reference'=> $paymentreference, 'transaction_reference'=>$transactionreference, 'amount'=>$paymentamount, 'payment_method'=> $paymentmethod, 'product_type'=>$product_type, 'product_reference'=>$paymentreference, 'transaction_hash'=> $transactionhash, 'transaction_hashME'=>$transactionhashME, 'payment_desc'=>$paymentdesc, 'extra'=>$data2]);


        if($paymentstatus !== "PAID"){
            return "!Paid transaction";
        }

        if($transactionhash != $transactionhashME){
            return "Invalid transaction signature";
        }

        $cfee=$input['totalPayable']-$input['settlementAmount'];

        if($product_type === "MOBILE_SDK"){
            $this->SDK($paymentamount, $paymentreference, $cfee);
        }

        if($product_type === "RESERVED_ACCOUNT"){
            if($input['accountDetails']== null){
                $acctd_name=$product_reference;
            }else{
                $acctd_name= $input['accountDetails']['accountName'];
            }
            if ($product_reference === "Mcdat"){
                $this->MCDatfundwallet($acctd_name,$paymentamount,$transactionreference, $cfee);
            }else{
                $atm=new ATMmanagerController();
                $atm->RAfundwallet($acctd_name, $paymentamount, $product_reference, $transactionreference, $cfee, $input, "Monnify");
            }
        }

        return "success";
    }

    private function MCDatfundwallet($name, $amount, $transactionreference, $cfee){
        $charge_treshold=2000;
        $charges=50;
        $u=User::where('full_name', 'LIKE', '%'.$name.'%')->first();

        if($u){
            $input['name']="wallet funding";
            $input['amount']=$amount;
            $input['status']='successful';
            $input['description']= $u->user_name .' wallet funded using Bank Transfer with the sum of #'.$amount;
            $input['user_name']=$u->user_name;
            $input['code']='afund_Bank Transfer';
            $input['i_wallet']=$u->wallet;
            $wallet=$u->wallet + $amount;
            $input['f_wallet']=$wallet;
            $input["ip_address"]="127.0.0.1:A";
            $input["ref"]=$transactionreference;
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";

                $input["gl"]="MCD Account";
                $input["amount"]=$charges;
                $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$u->user_name;

                PndL::create($input);

                $input["description"]="Being amount charged for funding less than #".$charge_treshold;
                $input["name"]="Auto Charge";
                $input["code"]="ac50";
                $input['status']='successful';
                $input["i_wallet"]=$wallet;
                $input["f_wallet"]=$input["i_wallet"] - $charges;

                Transaction::create($input);

                $wallet-=$charges;
            }


            if($cfee!=0){
                $input["type"]="expenses";
                $input["amount"]=$cfee;
                $input["narration"]="Payment gateway charges on MCD account with ref ".$transactionreference;

                PndL::create($input);
            }

            $u->wallet=$wallet;
            $u->save();
        }

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
