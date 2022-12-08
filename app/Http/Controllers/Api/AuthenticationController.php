<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CreateProvidusAccountJob;
use App\Jobs\LoginAttemptApiFinderJob;
use App\Mail\PasswordResetMail;
use App\Models\LoginAttempt;
use App\Models\Settings;
use App\Models\SocialLogin;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function signup(Request $request){
        /*Updated on 22/07/2020 by Samji
         * Following code will get single product details
         * A product is identified by product id (user_name)
         */

// check for post data
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'password'      => 'required',
            'phoneno'      => 'required',
            'deviceid'      => 'required',
            'email' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {
            $user_name = $input['user_name'];
            $deviceid = $input['deviceid'];

            // get all user device first
            $users=User::all();
            foreach ($users as $key => $user) {
                // Assign JSON encoded string to a PHP variable
                $json = $user->devices;

                if ($json != "") {
                    // Decode JSON data to PHP associative array
                    $arr = json_decode($json, true);

                    // Loop through the associative array
                    foreach ($arr as $value) {
                        if ($value == $deviceid) {
                            $GLOBALS['found'] = 1;

                            $response["success"] = 0;
                            $response["message"] = "Device already linked to a user";
//                            echo json_encode($response);
                            return response()->json(['success'=> 0, 'message'=>'Device already linked to a user']);
                        }
                    }
                }//looping through the database and also checking for device id match
            }

            $user=User::where('user_name', $user_name)->get();
            if(!$user->isEmpty()){
                $response["success"] = 0;
                $response["message"] = "User name already exist";
                //values gotten
                // echoing JSON response
//                echo json_encode($response);
                return response()->json(['success'=> 0, 'message'=>'User name already exist']);

            }else{
                //values gotten
                $create["wallet"] = "0";
                $create["status"] = "client";
                $create["level"] = "1";
                $create["target"] = "Make up to 10 transactions to be eligible for an Agent and send a request mail to info@5starcompany.com.ng where you earn incentives on transactions done at the end of the month";
                $create["user_name"] = $user_name;
                $create["email"] = $input["email"];
                $create["phoneno"] = $input["phoneno"];
                $create["mcdpassword"] = $input["password"];
                $create["password"] = "";
                $create["referral"] = $input["referral"];
                $create["gnews"] = 'If you are a business person that needs to increase your investment and make more money, you just arrived at the right place';
                $date = date("Y-m-d H:i:s");
                $dev = array($date => $deviceid);
                $create["devices"] = json_encode($dev);

                if (User::create($create)) {
                    // successfully inserted into database
                    $job = (new CreateProvidusAccountJob($create["user_name"]))
                        ->delay(Carbon::now()->addSeconds(10));
                    dispatch($job);

                    $response["success"] = 1;
                    $response["message"] = "Client Successfully Added";

                    // echoing JSON response
//                    echo json_encode($response);
                    return response()->json(['success'=> 1, 'message'=>'Client Successfully Added']);
                } else {
                    // failed to insert row
                    $response["success"] = 0;
                    $response["message"] = "Oops! An error occurred.";
                    // echoing JSON response
//                    echo json_encode($response);
                    return response()->json(['success'=> 0, 'message'=>'Oops! An error occurred.']);
                }
            }

        }else{
            // required field is missing
            $response["success"] = 0;
            $response["message"] = "Required field(s) is missing";
            // echoing JSON response
//            echo json_encode($response);
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function login(Request $request){
        /* updated on 22/07/2020 by samji
         * Following code will get single product details
         * A product is identified by product id (uid)
         */

        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'password'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {
            if (isset($input['login'])) {
                $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $input['device'] = $_SERVER['HTTP_USER_AGENT'];
                $la = LoginAttempt::create($input);
                $job = (new LoginAttemptApiFinderJob($la->id))
                    ->delay(Carbon::now()->addSeconds(1));
                dispatch($job);
            }

            if ($input['deviceid'] != null) { //mainactivity login check
                $de = User::all();

                $GLOBALS['found'] = 0;
                $GLOBALS['found_username'] = $input['user_name'];
                foreach ($de as $d) {
                    // user devices per user
                    $r_device = $d->devices;
                    $r_username = $d->user_name;

                    // Assign JSON encoded string to a PHP variable
                    $json = $r_device;
                    if ($json != "") {
                        // Decode JSON data to PHP associative array
                        $arr = json_decode($json, true);
                        // Loop through the associative array
                        foreach ($arr as $key => $value) {
                            if ($value == $input["deviceid"]) {
                                $GLOBALS['found_username'] = $r_username;
                                $GLOBALS['found'] = 1;
                            }
                        }
                    }//looping through the database and also checking for device id match for username
                }// finish device checking
            }// end

            if ($input['user_name'] != "null") {
                $user = User::where('user_name', $input['user_name'])->first();
                if (!$user) {
                    return response()->json(['success' => 0, 'message' => 'User does not exist']);
                }
                if ($user->mcdpassword != $input['password']) {
                    if ($user->email != $input['password']) {
                        return response()->json(['success' => 0, 'message' => 'Incorrect password attempt']);
                    }
                }

                if ($GLOBALS['found'] == 0) {
                    $e_device = $user->devices;
                    $e_arr = json_decode($e_device, true);
                    $date = date("Y-m-d H:i:s");
                    $array = array($date => $input['deviceid']);
                    if ($e_device != "") {
                        $arr = array_merge($e_arr, $array);
                    } else {
                        $arr = $array;
                    }
                    $ar = json_encode($arr);
                    $user->devices = $ar;
                    $user->save();
                } else {

                    if (trim($GLOBALS['found_username']) != $input['user_name']) {
                        return response()->json(['success' => 0, 'message' => 'Device belongs to another user. Kindly contact support at info@5starcompany.com.ng']);
                    }
                }
            } else {
                if ($GLOBALS['found'] == 0) {
                    return response()->json(['success' => 0, 'message' => 'DeviceID not found']);
                } else {
                    return response()->json(['success' => 1, 'message' => 'DeviceID match found', 'user_name' => $GLOBALS['found_username']]);
                }
            }

            // mysql update row with matched user name
            $date = date("Y-m-d H:i:s");
            $user->last_login = $date;
            $user->save();

            $uinfo['full_name'] = $user->full_name;
            $uinfo['company_name'] = $user->company_name;
            $uinfo['dob'] = $user->dob;
            $uinfo['wallet'] = $user->wallet;
            $uinfo['bonus'] = $user->bonus;
            $uinfo['status'] = $user->status;
            $uinfo['level'] = $user->level;
            $uinfo['photo'] = $user->photo;
            $uinfo['reg_date'] = $user->reg_date;
            $uinfo['target'] = $user->target;
            $uinfo['user_name'] = $user->user_name;
            $uinfo['email'] = $user->email;
            $uinfo['phoneno'] = $user->phoneno;
            $uinfo['gnews'] = $user->gnews;
            $uinfo['fraud'] = $user->fraud;
            $uinfo['referral'] = $user->referral;
            $uinfo['referral_plan'] = $user->referral_plan;
            $uinfo['account_number'] = $user->account_number;
            $uinfo['account_number2'] = $user->account_number2;
            $uinfo['last_login'] = $user->last_login;
            $uinfo['agent_commision'] = $user->agent_commision;
            $uinfo['points'] = $user->points;

            $uinfo["total_fund"] =Transaction::where([['user_name',$input['user_name']], ['name', 'wallet funding'], ['status', 'successful']])->count();
            $uinfo["total_trans"] =Transaction::where([['user_name',$input['user_name']], ['status', 'delivered']])->count();
            // get user transactions report from transactions table

            //get airtime discounts
            $airsets=DB::table("tbl_serverconfig_airtime")->where('name','=','discount')->first();
            $uinfo['airtime_discount_mtn']=$airsets->mtn;
            $uinfo['airtime_discount_glo']=$airsets->glo;
            $uinfo['airtime_discount_etisalat']=$airsets->etisalat;
            $uinfo['airtime_discount_airtel']=$airsets->airtel;

            $settings=Settings::all();
            foreach ($settings as $setting){
                $sett[$setting->name]=$setting->value;
            }
            $d=array_merge($uinfo, $sett);

            if(isset($input['login'])){
                $la->status="authorized";
                $la->save();
            }

            return response()->json(['success'=> 1, 'message'=>'Login successfully', 'data'=>$d]);

        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function updateAgent(Request $request){
        /* updated on 11/08/2019 by samji
         * Following code will get single product details
         * A product is identified by product id (uid)
         */

        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'dob'      => 'required',
            'image'      => 'required',
            'address'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input['user_name'])->first();
            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            if ($user->dob == "") {
                $image = $input["image"];
                $photo = $input["user_name"] . ".JPG";

                $decodedImage = base64_decode("$image");
                file_put_contents(storage_path("app/public/avatar/". $photo) , $decodedImage);

                $user->full_name = $input['full_name'];
                $user->company_name = $input['company_name'];
                $user->dob = $input['dob'];
                $user->bvn = $input['bvn'];
                $user->address = $input['address'];
                $user->target = $input["request"] . " in progress...";
                $user->photo = $input["user_name"] . ".JPG";
                $user->note = $input["note"];
                $user->save();

                return response()->json(['success' => 1, 'message' => 'Data submitted successfully, kindly check your mail for progress']);
            } else {
                return response()->json(['success' => 0, 'message' => 'Data can only be submitted once']);
            }
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function resetpassword(Request $request){

        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'email'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {
            // mysql update row with matched user name
            $user=User::where('user_name',$input['user_name'])->first();
            if(!$user){
                return response()->json(['success' => 0, 'message' => 'User does not exist']);
            }

            if($input['email'] != $user->email){
                return response()->json(['success' => 0, 'message' => 'Invalid request detected']);
            }

            $pass= str_shuffle(substr(date('sydmM').rand().$input['user_name'], 0, 8));

            $user->mcdpassword=$pass;
            $user->save();

            $tr['password']=$pass;
            $tr['email']=$input['email'];
            $tr['user_name']=$input['user_name'];

            Mail::to($user->email)->send(new PasswordResetMail($tr));

            return response()->json(['success' => 1, 'message' => 'A new password has been sent to your mail successfully']);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function social_login(Request $request){
        /*
         * Following code will get single product details
         * A product is identified by product id (uid)
         */
        $input = $request->all();
        $rules = array(
            'email'      => 'required',
            'name'      => 'required',
            'avatar'      => 'required',
            'accesstoken'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {
            $user=User::where('email', $input['email'])->first();

            if (!$user){
                return response()->json(['success' => 0, 'message' => 'User does not exist']);
            }
            SocialLogin::create($input);

            return response()->json(['success'=> 1, 'message'=>'Social login successful', 'user_name'=>$user->user_name, 'email'=>$user->email]);
        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function update_referral(Request $request){
        /*
         * Following code will get single product details
         * A product is identified by product id (uid)
         */
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'referral'      => 'required',
            'version'      => 'required',
            'deviceid'      => 'required');

        $validator = Validator::make($input, $rules);

        $input=$request->all();

        if ($validator->passes()) {

            $uid=User::where('user_name', $input['user_name'])->first();
            if (!$uid){
                return response()->json(['success' => 0, 'message' => 'User does not exist']);
            }

            $referral=User::where('user_name', $input['referral'])->first();
            if (!$referral){
                return response()->json(['success' => 0, 'message' => 'Referral does not exist']);
            }

            if ($uid == $referral){
                return response()->json(['success' => 0, 'message' => 'You can not add your self as a referral']);
            }

            if ($uid->referral!=""){
                return response()->json(['success' => 0, 'message' => 'Referral has already been added']);
            }
            //values gotten
            $r_wallet = $referral->wallet;
            $r_email = $referral->email;
            $r_referralplan = $referral->referral_plan;
            $r_user_name = $referral->user_name;

            $referral_count=User::where('referral', $input['referral'])->count();

            if ($r_referralplan == "free") {
                $max = 20;
            } elseif ($r_referralplan == "larvae") {
                $max = 50;
            } elseif ($r_referralplan == "butterfly") {
                $max = 100;
            }

            if ($max == $referral_count) {
                return response()->json(['success' => 0, 'message' => $referral->user_name . " has reached referral limit. Kindly inform the user to upgrade referral plan"]);
            }

            $uid->referral=$input['referral'];
            $uid->save();

            $noti = new ATMmanagerController();
            $noti->PushNoti($input['referral'], "Hi ".$input['referral'].", ".$input['user_name']." has added you as a referral. You will start receiving atleast #5 on every data transaction, to earn more kindly upgrade. Thanks", "Referral");

            return response()->json(['success' => 1, 'message' => $referral->user_name . " has been added as your referral successfully", 'referral'=>$input['referral']]);

        }else{
            // required field is missing
            // echoing JSON response
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function CreateMonnifyAcct($id){

            $u = User::find($id);

            if (!$u) {
                echo "invalid account";
            }

            if ($u->account_number != '0') {
                echo "Account created already";
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
            }
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'o_password' => 'required',
            'n_password' => 'required',
            );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user=User::where("user_name",$input['user_name'])->first();
            if($user->mcdpassword!=$input['o_password']){
                return response()->json(['success'=> 0, 'message'=>'Wrong Old Password']);
            }

            $user->mcdpassword=$input['n_password'];
            $user->save();

            return response()->json(['success'=> 1, 'message'=>'Password changed successfully']);
        }
    }

    public function change_pin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'o_pin' => 'required',
            'n_pin' => 'required',
            );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user=User::where("user_name",$input['user_name'])->first();
            if($user->pin!=$input['o_pin']){
                return response()->json(['success'=> 0, 'message'=>'Wrong Old Pin']);
            }

            $user->pin=$input['n_pin'];
            $user->save();

            return response()->json(['success'=> 1, 'message'=>'Pin changed successfully']);
        }
    }



        }
