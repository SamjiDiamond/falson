<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\ATMtransactionserveJob;
use App\Jobs\PushNotificationJob;
use App\Jobs\ReferralBonusAfterFundingJob;
use App\Mail\TransactionNotificationMail;
use App\Models\PndL;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ATMmanagerController extends Controller
{
    public function RAfundwallet($name, $amount, $reference, $transactionreference, $cfee, $input, $account_number, $payment_method)
    {

        $u = User::where('user_name', '=', $reference)->first();
        $w = Wallet::where('ref', $transactionreference)->first();

        if ($payment_method == "Monnify") {
            $data = Settings::where('name', 'monnify_funding_charges')->first();
            $charges = $data->value;

            $crAmount = $amount - $charges - $cfee;
        } else if ($payment_method == "Paylony") {
            $data = Settings::where('name', 'paylony_funding_charges')->first();
            $charges = $data->value;

            $crAmount = $amount - $charges - $cfee;
        } else if ($payment_method == "Budpay") {
            $data = Settings::where('name', 'budpay_funding_charges')->first();
            $charges = $data->value;

            $crAmount = $amount - $charges;
        } else {
            $data = Settings::where('name', 'funding_charges')->first();
            $charges = $data->value;

            $crAmount = $amount - $charges;
        }

        $wallet = $u->wallet + $crAmount;

        if (!$w) {
            if ($u) {
                $input['name'] = "wallet funding";
                $input['amount'] = $crAmount;
                $input['status'] = 'successful';
                $input['description'] = $u->user_name . ' wallet funded using Account Transfer(' . $account_number . ') with the sum of #' . $crAmount . ' from ' . $name;
                $notimssg = $u->user_name . ' wallet funded using Account Transfer(' . $account_number . ') with the sum of #' . $crAmount . ' from ' . $name;
                $input['user_name'] = $u->user_name;
                $input['code'] = 'afund_Personal Account';
                $input['i_wallet'] = $u->wallet;
                $input['f_wallet'] = $wallet;
                $input["ip_address"] = "127.0.0.1:A";
                $input["ref"] = $transactionreference;
                $input["date"] = Carbon::now();
                $input["server"] = $payment_method;

                $tr = Transaction::create($input);

                $input["type"] = "income";
                $input["gl"]="Personal Account";
                $input["amount"] = $charges;
                $input['status'] = 'successful';
                $input["narration"] = "Being amount charged for using automated funding from " . $input["user_name"];

                PndL::create($input);

//                $input["description"] = "Being amount charged for using automated funding";
//                $input["name"] = "Auto Charge";
//                $input["code"] = "af50";
//                $input["i_wallet"] = $wallet;
//                $input["f_wallet"] = $input["i_wallet"] - $charges;
//                $wallet = $input["f_wallet"];
//
//                Transaction::create($input);

                $u->wallet = $wallet;
                $u->save();

                $input['user_name'] = $u->user_name;
                $input['amount'] = $amount;
                $input['medium'] = "Personal Account";
                $input['o_wallet'] = $input["f_wallet"] - $amount - $charges;
                $input['n_wallet'] = $input["f_wallet"];
                $input['ref'] = $transactionreference;
                $input['version'] = "2";
                $input['status'] = "completed";
                $input['deviceid'] = $input['code'];
                Wallet::create($input);

                if ($cfee != 0) {
                    $input["type"] = "expenses";
                    $input["gl"] = $payment_method;
                    $input["amount"] = $cfee;
                    $input["narration"] = "Payment gateway charges on personal account with ref " . $transactionreference;

                    PndL::create($input);
                }

                $u->notify(new UserNotification($notimssg, "Wallet Funding"));

                $noti = new PushNotificationController();
                $noti->PushNoti($input['user_name'], $notimssg, "Account Transfer Successful");

                Mail::to($u->email)->send(new TransactionNotificationMail($tr));

                $data = Settings::where('name', 'enable_referral_bonus')->first();

                if ($data->value == "1") {
                    ReferralBonusAfterFundingJob::dispatch($u->user_name, $amount);
                }

            }
        }else{
            echo "Already credited ";
        }
    }

    public function atmfundwallet($fun, $amount, $reference, $payment_method, $cfee){
        $data=Settings::where('name','funding_charges')->first();
        $charges=$data->value;

        $u=User::where('user_name', '=', $fun->user_name)->first();

        $crAmount=$amount - $charges;
        $wallet=$u->wallet + $crAmount;

        $input['name']="wallet funding";
        $input['amount']=$crAmount;
        $input['status']='successful';
        $input['description']= $u->user_name .' wallet funded using '.$payment_method.' with the sum of NGN'.$crAmount .' with ref->'.$reference;
        $input['user_name']=$u->user_name;
        $input['code']='afund_'.$payment_method;
        $input['i_wallet']=$u->wallet;
        $input['f_wallet']=$wallet;
        $input['ref']=$reference;
        $input["ip_address"]=$_SERVER['REMOTE_ADDR'].":A";
        $input["date"]=date("y-m-d H:i:s");

        $tr=Transaction::create($input);

        if($charges > 0){
            $input["type"]="income";
//            $input["gl"]=$payment_method;
            $input["gl"]="funding_charges";
            $input["amount"]=$charges;
            $input["narration"]="Being amount charged for funding on ".$reference." from ".$u->user_name;

            PndL::create($input);

//            $input["description"]="Being amount charged for funding on ".$reference;
//            $input["name"]="Auto Charge";
//            $input["code"]="ac50";
//            $input['status']='successful';
//            $input["i_wallet"]=$wallet;
//            $input["f_wallet"]=$input["i_wallet"] - $charges;
//
//            Transaction::create($input);

//            $wallet-=$charges;
        }

        if($cfee!=0){
            $input["type"] = "expenses";
            $input["gl"] = $payment_method;
            $input["amount"] = $cfee;
            $input["narration"] = "Payment gateway charges on " . $reference;

            PndL::create($input);
        }

        $u->wallet = $wallet;
        $u->save();

        PushNotificationJob::dispatch($u->user_name, "Hi " . $u->user_name . ", your wallet has been credited with the sum of " . $crAmount . " via " . $payment_method, "Payment Notification");

        Mail::to($u->email)->send(new TransactionNotificationMail($tr));

    }


    public function atmtransactionserve($id){
        echo "launching ATM transaction job";
        $job = (new ATMtransactionserveJob($id))
            ->delay(Carbon::now()->addSeconds(2));
        dispatch($job);
    }

}
