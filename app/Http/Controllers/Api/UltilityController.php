<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SMSinboxJob;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
use App\Models\logvoice;
use App\Models\PndL;
use App\Models\SMSinbox;
use App\Models\Transaction;
use App\Models\VoiceSuggesstion;
use App\Models\Withdraw;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UltilityController extends Controller
{
    public function smsinbox(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'sender' => 'required',
            'message' => 'required',
            'time' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
                SMSinbox::create($input);
                SMSinboxJob::dispatch($input)->delay(Carbon::now()->addSeconds(1));

                return response()->json(['status' => 1, 'message' => 'Submitted successfully']);
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'message' => 'Error logging sms', 'error' => $e]);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Error logging SMS', 'error' => $validator->errors()]);
        }

    }

    public function sendemail(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'message' => 'required',);

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $ap = User::where('user_name', $input['user_name'])->first();

        $GLOBALS['email'] = $ap->email;

        $data = array('name' => $ap->full_name, 'messag' => $input['message']);
        Mail::send('email_notification', $data, function ($message) {
            $message->to($GLOBALS['email'], 'Client')->subject('Message Reply');
            $message->from('info@5starcompany.com.ng', '5Star Inn Company');
        });

        DB::table('tbl_emaillog')->insert(
            ['user_name' => $input["user_name"], 'message' => $input['message'], 'email' => $ap->email, 'response' => 'sent']
        );

        return response()->json(['status' => 1, 'message' => 'Message send successfully']);
    }

    public function mcd_logvoice(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'name' => 'required',
            'voice' => 'required',
            'page' => 'required',
            'code' => 'required',
            'version' => 'required',
            'device_details' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
               logvoice::create($input);

                try {
                    $findme = $input['voice'];

                    $s = VoiceSuggesstion::get();

                    foreach ($s as $su) {
                        $pos = strpos($findme, $su->find);
                        // Note our use of ===.  Simply == would not work as expected
                        if ($pos !== false) {
                            return response()->json(['status' => 1, 'message' => $su->response]);
                        }
                    }
                } catch (Exception $e) {
                }

                return response()->json(['status' => 1, 'message' => 'Is neither part of my command or words I understand, I will respond to you next time. Keep using me and I will learn more']);
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'message' => 'Error logging voice', 'error' => $e]);
            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Error logging voice', 'error' => $validator->errors()]);
        }

    }

    public function mcd_a2ca2b(Request $request){

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'network' => 'required',
            'phoneno' => 'required',
            'amount' => 'required',
            'receiver' => 'required',
            'ref' => 'required',
            'version' => 'required',
            'device_details' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
                $input['ip'] = $_SERVER['REMOTE_ADDR'];
                Airtime2Cash::create($input);

                $number = Airtime2CashSettings::where('network', '=', $input['network'])->first();

                return response()->json(['status' => 1, 'message' => 'Transfer #' . $input['amount'] . ' to ' . $number->number . ' and get your value instantly. Reference: ' . $input['ref'] . '. By doing so, you acknowledge that you are the legitimate owner of this airtime and you have permission to send it to us and to take possession of the airtime.']);
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'message' => 'An error occured', 'error' => $e]);
            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Some forms are left out', 'error' => $validator->errors()]);
        }

    }

    public function VerifyTV(Request $request){

        $coded=$request->get('coded');
        $number=$request->get('phone');

        if (isset($_REQUEST['coded']) && isset($_REQUEST['phone'])) {
            switch ($coded){
                case "d":
                    $tv_type="DSTV";
                    $tv_type_code="01";
                    break;

                case "D":
                    $tv_type="DSTV";
                    $tv_type_code="01";
                    break;

                case "g":
                    $tv_type="GOTV";
                    $tv_type_code="02";
                    break;

                case "G":
                    $tv_type="GOTV";
                    $tv_type_code="02";
                    break;

                case "s":
                    $tv_type="STARTIMES";
                    $tv_type_code="03";
                    break;

                case "S":
                    $tv_type="STARTIMES";
                    $tv_type_code="03";
                    break;
                default:
                    $tv_type="";
                    // required field is missing
                    $response["success"] = 0;
                    $response["message"] = "Invalid Service Type. Available are d for DSTV, g for GOTV, s for STARTIME.";
                    // echoing JSON response
                    echo json_encode($response);
                    return;
            }


            function verify_server1($tv_type, $number){
                $url=env('SERVER1_TV')."user_check".env('SERVER1_CRED')."&service=".$tv_type."&number=".$number;
                // Perform initialize to validate name on server
                $result = file_get_contents($url);
                $findme   = 'accountStatus';
                $pos = strpos($result, $findme);
                $arr = json_decode($result, true);
                // Note our use of ===.  Simply == would not work as expected
                if ($pos === false) {
                    $findme   = 'billAmount';
                    $pos = strpos($result, $findme);

                    if ($pos === false) {
                        $response["success"] = 0;
                        $response["message"] = "The device number supplied did not return any data.";
                        // echoing JSON response
                        echo json_encode($response);
                    }else{
                        if($arr["details"]["returnCode"]==0){
                            // Print a single value
                            $response["success"] = 1;
                            $response["message"] = "Name validation successful";
                            $response["bill_amount"] = $arr["details"]["billAmount"];
                            $response["account_name"] = $arr["details"]["customerName"];
                            $response["customer_number"] = $arr["details"]["customerNumber"];
                            $response["customer_type"] = $arr["details"]["customerType"];
                            $response["balance"] = $arr["details"]["balance"];
                            $response["due_date"]="n/a";
                            $response["account_status"]="n/a";
                            // echoing JSON response
                            echo json_encode($response);
                        }else{
                            $response["success"] = 0;
                            $response["message"] = "The device number supplied did not return any data.";
                            // echoing JSON response
                            echo json_encode($response);
                        }
                    }
                } else {
                    // Print a single value
                    $response["success"] = 1;
                    $response["message"] = "Name validation successful";
                    $response["account_name"] = $arr["details"]["firstName"] ." ".$arr["details"]["lastName"];
                    $response["account_status"] = $arr["details"]["accountStatus"];
                    $response["customer_number"] = $arr["details"]["customerNumber"];
                    $response["customer_type"] = $arr["details"]["customerType"];
                    $response["due_date"] = $arr["details"]["dueDate"];
                    // echoing JSON response
                    echo json_encode($response);
                }
            }


            function verify_server2($tv_type_code, $number){
                $url="&cabletv=".$tv_type_code."&smartcardno=".$number;
                // Perform initialize to validate name on server
                $result = file_get_contents($url);
                $findme   = 'INVALID_SMARTCARDNO';
                $pos = strpos($result, $findme);
                $arr = json_decode($result, true);
                // Note our use of ===.  Simply == would not work as expected
                if ($pos === false) {
                    $response["success"] = 0;
                    $response["message"] = $arr["customer_name"];
                    // echoing JSON response
                    echo json_encode($response);
                }else{
                    $response["success"] = 1;
                    $response["message"] = "Name validation successful";
                    $response["account_name"] = $arr["customer_name"];
                    $response["account_status"] = "n/a";
                    $response["customer_number"] = "n/a";
                    $response["customer_type"] = "n/a";
                    $response["due_date"] = "n/a";
                    // echoing JSON response
                    echo json_encode($response);
                }

            }
            verify_server1($tv_type, $number);
            //verify_server2($tv_type_code, $number);
        }
    }

    public function withdraw(Request $request){
        $input=$request->all();
        $u=User::where("user_name", $input['user_name'])->first();


        if($input['wallet']=="Mega Bonus"){
            if($u->bonus < $input['amount']){
                return response()->json(['success'=> 0, 'message'=>'Low wallet balance']);
            }
        }

        if($input['wallet']=="Agent Commission"){
            if($u->agent_commision < $input['amount']){
                return response()->json(['success'=> 0, 'message'=>'Low wallet balance']);
            }
        }

        $u->agent_commision -= $input['amount'];
        $u->save();

        Withdraw::create($input);

        return response()->json(['success'=> 1, 'message'=>'Withdrawal logged successfully']);

    }

    public function fetchmonnifyRA($id){

        $last=$id+1000;

        for ($i=$id; $i<=$last; $i++){
            echo "<br />";
            echo $i . "-";

            $u = User::find($i);

            if (!$u) {
                echo "invalid account";
                continue;
            }

            if ($u->account_number != '0') {
                echo "Account created already";
                continue;
            }

            try {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/auth/login",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic " . env("MONNIFY_AUTH")
                    ),
                ));

                $response = curl_exec($curl);
                $respons = $response;

                curl_close($curl);

                $response = json_decode($response, true);
                $token = $response['responseBody']['accessToken'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/bank-transfer/reserved-accounts/".$u->user_name,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode($response, true);

                $account_number = $response['responseBody']['accountNumber'];
                $bank_name = $response['responseBody']['bankName'];

                $u->account_number = $account_number . " | " . $bank_name;
                $u->save();

                echo $account_number . "|| ";
            } catch (Exception $e) {
                echo "Error encountered ";
                continue;
            }

        }
        return "success";

    }

    public function monnifyRA($id){

        $last=$id+1000;

        for ($i=$id; $i<=$last; $i++){
            echo "<br />";
            echo $i . "-";

            $u = User::find($i);

            if (!$u) {
                echo "invalid account";
                continue;
            }

            if ($u->account_number != '0') {
                echo "Account created already";
                continue;
            }

            try {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/auth/login",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic " . env("MONNIFY_AUTH")
                    ),
                ));

                $response = curl_exec($curl);
                $respons = $response;

                curl_close($curl);

//        $response='{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTU5MTQ5Nzc5OSwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiOTYyNTA5NzctMmZkOS00ZDM4LTliYzEtNTMyMTMwYmFiODc0IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9LUFoyQjJUQ1hLIn0.iTOX9RWwA0zcLh3OsTtuFD-ehAbW1FrUcAZLM73V66_oTuV2jJ5wBjWNvyQToZKl2Rf5TH2UgiJyaapAZR6yU9Y4Di_oz97kq0CwpoFoe_rLmfgWgh-jrYEsrkj751jiQQm_vZ6BEw9OJhYtMBb1wEXtY4rFMC7I2CLmCnwpJaMWgrWnTRcoLZlPTcWGMBLeggaY9oLfIIorV9OTVkB2kihA9QHX-8oUGkYpvKyC9ERNYMURcK01LnPgSBWI7lXrjf8Ct2BjHi6RKdlFRPNpp3OAbN9Oautvwy09WS3XOhA8eycA0CNBh8o7jekVLCLjXgz6YrcMH0j9ahb3mPBr7Q","expiresIn":368}}';

                $response = json_decode($response, true);

                $token = $response['responseBody']['accessToken'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/bank-transfer/reserved-accounts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\"accountReference\": \"" . $u->user_name . "\", \"accountName\": \"MCD-" . $u->user_name . "\",  \"currencyCode\": \"NGN\",  \"contractCode\": \"" . env('MONNIFY_CONTRACTCODE') . "\",  \"customerEmail\": \"" . $u->email . "\",  \"customerName\": \"MCD-" . $u->user_name . "\"}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode($response, true);

                $contract_code = $response['responseBody']['contractCode'];
                $account_reference = $response['responseBody']['accountReference'];
                $currency_code = $response['responseBody']['currencyCode'];
                $customer_email = $response['responseBody']['customerEmail'];
                $customer_name = $response['responseBody']['customerName'];
                $account_number = $response['responseBody']['accountNumber'];
                $bank_name = $response['responseBody']['bankName'];
                $collection_channel = $response['responseBody']['collectionChannel'];
                $status = $response['responseBody']['status'];
                $created_on = $response['responseBody']['createdOn'];
                $reservation_reference = $response['responseBody']['reservationReference'];
                $extra = $respons;

                DB::table('tbl_reserveaccount_monnify')->insert(['contract_code' => $contract_code, 'account_reference' => $account_reference, 'currency_code' => $currency_code, 'customer_email' => $customer_email, 'customer_name' => $customer_name, 'account_number' => $account_number, 'bank_name' => $bank_name, 'collection_channel' => $collection_channel, 'status' => $status, 'reservation_reference' => $reservation_reference, 'created_on' => $created_on, 'extra' => $extra]);

                $u->account_number = $account_number;
                $u->save();

                echo $account_number . "|| ";
            } catch (Exception $e) {
                echo "Error encountered ";
                continue;
            }

        }
        return "success";

    }

    function monnifyhook(Request $request){
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

        if($product_type === "RESERVED_ACCOUNT"){
            $acctd_name= $input['accountDetails']['accountName'];
            if ($product_reference === "Mcdat"){
                $this->MCDatfundwallet($acctd_name,$paymentamount);
            }else{
                $this->RAfundwallet($acctd_name,$paymentamount,$product_reference);
            }
        }

        echo "success";
    }

    public function MCDatfundwallet($name, $amount){
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
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";
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

            $u->wallet=$wallet;
            $u->save();
        }

    }

    public function RAfundwallet($name, $amount, $reference){
        $charge_treshold=2000;
        $charges=50;
        $u=User::where('user_name', '=', $reference)->first();

        if($u){
            $input['name']="wallet funding";
            $input['amount']=$amount;
            $input['status']='successful';
            $input['description']= $u->user_name .' wallet funded using Account Transfer('.$u->account_number .') with the sum of #'.$amount. ' from '. $name;
            $input['user_name']=$u->user_name;
            $input['code']='afund_Bank Transfer';
            $input['i_wallet']=$u->wallet;
            $wallet=$u->wallet + $amount;
            $input['f_wallet']=$wallet;
            $input["ip_address"]="127.0.0.1:A";
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            $input["type"]="income";
            $input["amount"]=$charges;
            $input['status']='successful';
            $input["narration"]="Being amount charged for using automated funding from ".$input["user_name"];

            PndL::create($input);

            $input["description"]="Being amount charged for using automated funding";
            $input["name"]="Auto Charge";
            $input["code"]="af50";
            $input["i_wallet"]=$wallet;
            $input["f_wallet"]=$input["i_wallet"] - $charges;
            $wallet=$input["f_wallet"];

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";
                $input["amount"]=$charges;
                $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$input["user_name"];

                PndL::create($input);

                $input["description"]="Being amount charged for funding less than #".$charge_treshold;
                $input["name"]="Auto Charge";
                $input["code"]="ac50";
                $input["i_wallet"]=$wallet;
                $input["f_wallet"]=$input["i_wallet"] - $charges;

                Transaction::create($input);

                $wallet-=$charges;
            }

            $u->wallet=$wallet;
            $u->save();
        }

    }

    function hook(Request $request){
        $input = $request->all();

//        $data1= implode($input);
        $data2= json_encode($input);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data2]);

        echo "success";
    }

    function assistantHook(Request $request){
        $input = $request->all();

//        $data1= '{"responseId":"30e2cf1d-c429-4335-9201-b97da2d2f617-0820055c","queryResult":{"queryText":"the reference is mcd_transaction_13263839484757","action":"helptransaction.helptransaction-custom","parameters":{"transaction_reference":"mcd_transaction_13263839484757"},"allRequiredParamsPresent":true,"fulfillmentText":"Am checking it, i will give you feedback soon","fulfillmentMessages":[{"text":{"text":["Am checking it, i will give you feedback soon"]}}],"outputContexts":[{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/helptransaction-followup","lifespanCount":1,"parameters":{"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}},{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/__system_counters__","parameters":{"no-input":0,"no-match":0,"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}}],"intent":{"name":"projects\/mcdvoice-kign\/agent\/intents\/2ff5c48e-d989-4882-90cd-3535b6563143","displayName":"help.transaction - reference"},"intentDetectionConfidence":1,"languageCode":"en"},"originalDetectIntentRequest":{"payload":[]},"session":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df"}';
        $data1= json_encode($input);
        $data2= json_decode($input);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data1]);

        if($data2->queryResult->action == "helptransaction.helptransaction-custom"){
            $ref=$data2->queryResult->parameters->transaction_reference;

            $tran=Transaction::where('ref', $ref)->first();

            if (!$tran){
                $rep="Transaction reference does not exit in our system";
            }else{
                $rep=$tran->description;
            }

        }else{
            $rep="Not configure to handle this request yet";
        }

        return '{
  "fulfillmentMessages": [
    {
      "text": {
        "text": [
          '.$rep.'
        ]
      }
    }
  ]
}';
    }
}



/*{
    "transactionReference": "MNFY|20200606202911|015526",
    "paymentReference": "MNFY|20200606202911|015526",
    "amountPaid": "1700.00",
    "totalPayable": "1700.00",
    "settlementAmount": "1686.29",
    "paidOn": "06/06/2020 08:29:17 PM",
    "paymentStatus": "PAID",
    "paymentDescription": "PLANETF",
    "transactionHash": "ea4017c38add1cf770a15ec6977267051751531694058339e1b14e4977a1daeb0dcecbbef846fb045c841ca5e3edeaf1f44e06ec9db3f0b24aab79870c6ef96d",
    "currency": "NGN",
    "paymentMethod": "ACCOUNT_TRANSFER",
    "product": {
        "type": "RESERVED_ACCOUNT",
        "reference": "Mcdat"
    },
    "cardDetails": null,
    "accountDetails": {
        "accountName": "SOHENOU SEWAN ESTHER",
        "accountNumber": "******5166",
        "bankCode": "000016",
        "amountPaid": "1700.00"
    },
    "accountPayments": [
        {
            "accountName": "SOHENOU SEWAN ESTHER",
            "accountNumber": "******5166",
            "bankCode": "000016",
            "amountPaid": "1700.00"
        }
    ],
    "customer": {
        "email": "info@5starcompany.com.ng",
        "name": "5Star Inn Company"
    },
    "metaData": []
}


{
    "transactionReference": "MNFY|20200606175538|014351",
    "paymentReference": "MegaCheapData_fundwallet_1591462450234",
    "amountPaid": "2500.00",
    "totalPayable": "2500.00",
    "settlementAmount": "2467.75",
    "paidOn": "06/06/2020 05:58:18 PM",
    "paymentStatus": "PAID",
    "paymentDescription": "Grasun Telecoms fund wallet with this user email - grasun83@gmail.com",
    "transactionHash": "e579b0ab2ef55623a8c70dd90cdb2f45fee9b304b61d75cae2b33d794fcf6e5e0bcb91eb281ea16544a51d19a7efbcd97a74587152c65ef2ffdd0cab2a5973b3",
    "currency": "NGN",
    "paymentMethod": "CARD",
    "product": {
    "type": "MOBILE_SDK",
        "reference": "MegaCheapData_fundwallet_1591462450234"
    },
    "cardDetails": {
    "cardType": null,
        "authorizationCode": null,
        "last4": "9229",
        "expMonth": "10",
        "expYear": "22",
        "bin": "418745",
        "bankCode": null,
        "bankName": null,
        "reusable": false,
        "countryCode": null,
        "cardToken": null
    },
    "accountDetails": null,
    "accountPayments": [],
    "customer": {
    "email": "grasun83@gmail.com",
        "name": "Grasun Telecoms MegaCheapData"
    },
    "metaData": []
}*/
