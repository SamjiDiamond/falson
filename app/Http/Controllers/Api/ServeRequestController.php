<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\ServeRequestJob;
use App\Models\GeneralMarket;
use App\Models\PndL;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServeRequestController extends Controller
{
    public function paytv(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'api' => 'required',
            'coded' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'transid' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            /*httpParams . put("api", "mcd_app_9876234875356148750");
            httpParams . put("coded", getIntent() . getStringExtra("code"));
            httpParams . put("phone", phone . getText() . toString());
            httpParams . put("service", "paytv");
            httpParams . put("user_name", sharedpreferences . getString(myJson . user_name, "nt/p"));
            httpParams . put("wallet", sharedpreferences . getString(myJson . wallet, "nt/p"));
            httpParams . put("deviceid", ansJson . androidId);
            httpParams . put("version", NavigationDrawerConstants . version);
            httpParams . put("transid", ref);*/

            try {
            $api = $input['api'];
            $coded = $input['coded'];
            $phone = $input['phone'];
            $transid = $input['transid'];

            if ($api != "mcd_app_9876234875356148750") {
                return response()->json(['success' => 0, 'message' => 'Error, invalid request']);
            }
                $sys = DB::table("tbl_serverconfig_tv")->where('name', '=', 'tv')->first();

                switch ($coded) {
                    case "d_padi":
                        $tv_type = "DSTV";
                        $tv_package = "NLTESE36";
                        $bundle_code = "NLTESE36";
                        $link = "dstv";
                        $amount = "1850";
                        $tv_type_code = "14";
                        $tv_package_code = "01";
                        $service_id = "14";
                        $server = $sys->dstv;
                        break;

                    case "d_yanga":
                        $tv_type = "DSTV";
                        $tv_package = "NNJ1E36";
                        $bundle_code = "NNJ1E36";
                        $link = "dstv";
                        $amount = "2565";
                        $tv_type_code = "14";
                        $tv_package_code = "01";
                    $service_id = "14";
                    $server=$sys->dstv;
                    break;

                    case "d_confam":
                    $tv_type = "DSTV";
                    $tv_package = "NNJ2E36";
                    $bundle_code = "NNJ2E36";
                    $link = "dstv";
                    $amount = "4615";
                    $tv_type_code = "14";
                    $tv_package_code = "01";
                    $service_id = "14";
                    $server=$sys->dstv;
                    break;

                case "d_family":
                    $tv_type = "DSTV";
                    $tv_package = "COFAME36";
                    $bundle_code = "COFAMW4";
                    $link = "dstv";
                    $amount = "4000";
                    $tv_type_code = "01";
                    $tv_package_code = "02";
                    $service_id = "14";
                    $server=$sys->dstv;
                    break;

                case "d_compact":
                    $tv_type = "DSTV";
                    $tv_package = "COMPE36";
                    $bundle_code = "MINIBW4";
                    $link = "dstv";
                    $amount = "6975";
                    $tv_type_code = "01";
                    $tv_package_code = "03";
                    $service_id = "14";
                    $server=$sys->dstv;
                    break;

                case "d_compactplus":
                    $tv_type = "DSTV";
                    $tv_package = "COMPLE36";
                    $bundle_code = "COMPLW7";
                    $link = "dstv";
                    $amount = "10925";
                    $tv_type_code = "01";
                    $tv_package_code = "04";
                    $service_id = "14";
                    $server=$sys->dstv;
                    break;

                case "g_smallie":
                    $tv_type = "GOTV";
                    $tv_package = "GOHAN";
                    $bundle_code = "GOHAN";
                    $link = "gotv";
                    $amount = "800";
                    $tv_type_code = "02";
                    $tv_package_code = "01";
                    $service_id = "15";
                    $server=$sys->gotv;
                    break;

                case "g_jinja":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ1";
                    $bundle_code = "GOTVNJ1";
                    $amount = "1640";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    $server=$sys->gotv;
                    break;

                case "g_jolli":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ2";
                    $bundle_code = "GOTVNJ2";
                    $amount = "2460";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    $server=$sys->gotv;
                    break;

                case "g_max":
                    $tv_type = "GOTV";
                    $tv_package = "GOtvMax";
                    $bundle_code = "GOTVMAX";
                    $link = "gotv";
                    $amount = "3600";
                    $tv_type_code = "02";
                    $tv_package_code = "04";
                    $service_id = "15";
                    $server=$sys->gotv;
                    break;


                case "s_nova":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARNO";
                    $bundle_code = "900";
                    $link = "startimes";
                    $amount = "900";
                    $tv_type_code = "03";
                    $tv_package_code = "01";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_sportplus":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARSP";
                    $bundle_code = "1200";
                    $link = "startimes";
                    $amount = "1200";
                    $tv_type_code = "03";
                    $tv_package_code = "01";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_basic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARBA";
                    $bundle_code = "1700";
                    $link = "startimes";
                    $amount = "1700";
                    $tv_type_code = "03";
                    $tv_package_code = "02";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_smart":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARSM";
                    $bundle_code = "2200";
                    $link = "startimes";
                    $amount = "2200";
                    $tv_type_code = "03";
                    $tv_package_code = "03";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_classic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARCL";
                    $bundle_code = "2500";
                    $link = "startimes";
                    $amount = "2500";
                    $tv_type_code = "03";
                    $tv_package_code = "04";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_super":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARSU";
                    $bundle_code = "4200";
                    $link = "startimes";
                    $amount = "4200";
                    $tv_type_code = "03";
                    $tv_package_code = "04";
                    $service_id = "16";
                    $server=$sys->startimes;
                    break;

                case "s_chinese":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARCH";
                    $bundle_code = "6600";
                    $link = "startimes";
                    $amount = "6600";
                    $tv_type_code = "03";
                    $tv_package_code = "05";
                    $service_id = "16";
                    $server = $sys->startimes;
                    break;

                    default:
                        $tv_type = "";
                        // required field is missing
                        return response()->json(['success' => 0, 'message' => 'Error, Invalid coded Type. Contact info@5starcompany.com.ng for help']);
                }

                if ($tv_type == "") {
                    return response()->json(['success' => 0, 'message' => 'Error, invalid request check and try again']);
                }

                if ($server == 0) {
                    $this->Process0($coded, $amount, $tv_type, $phone, $transid, $input);
                }

                if ($server == 1) {
                    $this->paytvProcess($amount, $tv_package, $link, $tv_type, $phone, $transid, $input);
                }

                if ($server == 4) {
                    $this->paytvProcess4($service_id, $phone, $bundle_code, $amount, $transid, $input);
                }

            } catch (Exception $e) {
//                dd($e);
                return response()->json(['success' => 0, 'message' => 'Error processing transaction', 'error' => $e]);
            }

        }else{
            return response()->json(['success'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }

    }

    public function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'api' => 'required',
            'coded' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'transid' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
            $api = $input['api'];
            $coded = $input['coded'];
            $phone = $input['phone'];
            $transid = $input['transid'];

            if ($api != "mcd_app_9876234875356148750") {
                return response()->json(['success' => 0, 'message' => 'Error, invalid request']);
            }

            $dbc=DB::table("tbl_serverconfig_data")->where('coded', $coded)->first();

            if (!$dbc) {
                return response()->json(['success' => 0, 'message' => 'Error, invalid coded check and try again']);
            }

            if($dbc->server==0){
                $this->Process0($coded,$dbc->price, $dbc->network,$phone,$transid,$input);
            }

                if ($dbc->server == 1) {
                    if ($dbc->network == "SMILE") {
                        $do = new DataTransactionController();
                        $do->buysmile($dbc->price, $dbc->product_code, $dbc->network, $phone, $transid, $input);
                    } else {
                        $this->dataProcess($dbc->price, $dbc->product_code, $dbc->network, $phone, $transid, $input);
                    }
                }

                if ($dbc->server == 2) {
                    $this->dataProcess2($dbc->dataplan, $dbc->network_code, $dbc->network, $phone, $transid, $dbc->price, $input);
                }

                if ($dbc->server == 3) {
                    $this->dataProcess3($dbc->price, $dbc->product_code, $phone, $transid, $input, $dbc->network);
                }


            } catch (Exception $e) {
                dd($e);
                return response()->json(['success' => 0, 'message' => 'Error processing transaction', 'error' => $e]);
            }

        }else{
            return response()->json(['success'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }
    }



    public function buyairtime(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'api' => 'required',
            'coded' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'transid' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
                $api = $input['api'];
                $coded = $input['coded'];
                $phone = $input['phone'];
                $amnt = $input['amount'];
                $transid = $input['transid'];

                if ($api != "mcd_app_9876234875356148750") {
                    return response()->json(['success'=> 0, 'message'=>'Error, invalid request']);
                }

                $sys=DB::table("tbl_serverconfig_airtime")->where('name','=','airtime')->first();

        switch ($coded){
            case "m":
                $network="MTN";
                $network_code="01";
                $service_id="7";
                $server=$sys->mtn;
                break;

            case "M":
                $network="MTN";
                $network_code="01";
                $service_id="7";
                $server=$sys->mtn;
                break;

            case "e":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                $server=$sys->etisalat;
                break;

            case "E":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                $server=$sys->etisalat;
                break;

            case "9":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                $server=$sys->etisalat;
                break;

            case "g":
                $network="GLO";
                $network_code="02";
                $service_id="8";
                $server=$sys->glo;
                break;

            case "G":
                $network="GLO";
                $network_code="02";
                $service_id="8";
                $server=$sys->glo;
                break;

            case "a":
                $network="AIRTEL";
                $network_code="04";
                $service_id="6";
                $server=$sys->airtel;
                break;

            case "A":
                $network="AIRTEL";
                $network_code="04";
                $service_id="6";
                $server=$sys->airtel;
                break;

            default:
                $network="";
                // required field is missing
                return response()->json(['success'=> 0, 'message'=>'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $airtimesell=new AirtimeSellController();

            if(!is_numeric($amnt)){
                // required field is missing
                // echoing JSON response
                return response()->json(['success'=> 0, 'message'=>'Invalid amount, retry with valid amount.']);
            }elseif($amnt < 100) {
                $airtimesell->server5($amnt, $phone,$transid, $input);
            }else{

                if(isset($input['country'])){
                    if($input['country']=="GHS"){
                        $airtimesell->ghanaAirtime($amnt, $phone,$transid, $input);
                    }
                }

                if ($server == '1') {
                    $this->airtimeProcess($amnt, $network, $phone, $transid, $input);
                } elseif ($server == '1b') {
                    $this->airtimeProcess1b($amnt, $network, $phone, $transid, $input);
                } elseif ($server == '2') {
                    $this->airtimeProcess2($amnt, $network_code, $phone, $transid, $input);
                } elseif ($server == '3') {
                    $this->airtimeProcess3($amnt, $network, $phone, $transid, $input);
                } elseif ($server == '4') {
                    $this->airtimeProcess4($amnt, $service_id, $phone, $input);
                } elseif ($server == '5') {
                    $airtimesell->server5($amnt, $phone, $transid, $input);
                } else {
                    $this->Process0($coded, $amnt, $network, $phone, $transid, $input);
                }
            }
            } catch (Exception $e) {
                dd($e);
                return response()->json(['success' => 0, 'message' => 'Error processing transaction', 'error' => $e]);
            }

        } else {
            return response()->json(['success' => 0, 'message' => 'Error processing transaction', 'error' => $validator->errors()]);
        }
    }

    public function paytvProcess4($service_id, $phone, $bundle_code, $amount, $transid, $input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4") . "/users/account/authenticate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => env("SERVER4_AUTH"),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $token = $response['token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4") . "/services/category/" . $service_id . "/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"account\": \"" . $phone . "\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token,
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $name = $response['data']['name'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4") . "/bills/pay/tv",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"service_category_id\": \"" . $service_id . "\",\n\t\"smartcard\": \"" . $phone . "\",\n\t\"bundleCode\": \"" . $bundle_code . "\",\n\t\"amount\": \"" . $amount . "\",\n\t\"name\": \"" . $name . "\",\n\t\"invoicePeriod\": \"1\",\n\t\"phone\": \"08000000000\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: " . $token,
                "Content-Type: text/plain"
            ),
        ));

        $respons = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($respons, true);
        $status = $response['status'];

        if ($status == "success") {
            $this->addtrans("server4", $respons, $amount, 1, $response['transaction']['_id'], $input);
        } else {
            $this->addtrans("server4", $respons, $amount, 0, $transid, $input);
        }
    }

    function paytvProcess($amnt, $tv_package, $link, $tv_type, $phone, $transid, $input)
    {
        $url = env('SERVER1_TV') . "user_check" . env('SERVER1_CRED') . "&service=" . $tv_type . "&number=" . $phone;
        // Perform initialize to validate name on server
        $resul = file_get_contents($url);
        $findme = 'accountStatus';
        $pos = strpos($resul, $findme);
        $arr = json_decode($resul, true);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos === false) {
            $findme = 'billAmount';
            $pos = strpos($resul, $findme);

            if ($pos === false) {
                $GLOBALS['success'] = 0;
                $response["message"] = "The device number supplied did not return any data.";
            } else {
                if ($arr["details"]["returnCode"] == 0) {
                    // Print a single value
                    $GLOBALS['success'] = 1;
                    $GLOBALS['customer_name'] = $arr["details"]["customerName"];
                    $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
                } else {
                    $GLOBALS['success'] = 0;
                    $response["message"] = "The device number supplied did not return any data.";
                }
            }
        } else {
            // Print a single value
            $GLOBALS['success'] = 1;
            $GLOBALS['customer_name'] = $arr["details"]["lastName"];
            $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
        }

//begining of buying
        if ($GLOBALS['success'] == 1) {
            $url = env('SERVER1_TV') . $link . env('SERVER1_CRED') . "&smartno=" . $phone . "&product_code=" . $tv_package . "&customer_name=" . trim($GLOBALS['customer_name']) . "&customer_number=" . $GLOBALS['customer_number'] . "&trans_id=" . $transid . "&price=" . $amnt;
            $result = file_get_contents($url);

            $findme = 'service';
            $pos = strpos($result, $findme);
            // Note our use of ===.  Simply == would not work as expected

            if ($pos !== false) {
                $this->addtrans("server1", $result, $amnt, 1, $transid, $input);
            } else {
                $this->addtrans("server1", $result, $amnt, 0, $transid, $input);
            }
        } else {
            $this->addtrans("server1", $resul, $amnt, 0, $transid, $input);
        }
    }

    public function Process0($coded, $amnt, $network, $phone, $transid, $input)
    {
        $message = $coded . " | " . $phone . " | " . $amnt . " | " . $network;

        $noti = new PushNotificationController();
        $noti->PushNotiAdmin($message, "MCD Purchase Notification");

        $this->addtrans("server0", "Notification Sent", $amnt, 1, $transid, $input);
    }

    public function airtimeProcess($amnt, $network, $phone, $transid, $input)
    {
        $url = env("SERVER1") . "&network=" . $network . "&phoneNumber=" . $phone . "&amount=" . $amnt . "&trans_id=" . $transid;
        $result = file_get_contents($url);

        $findme   = 'trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $this->addtrans("server1",$result,$amnt,1, $transid,$input);
        }else {
            $this->addtrans("server1",$result,$amnt,0, $transid,$input);
        }
    }

    public function airtimeProcess1b($amnt, $network, $phone, $transid, $input){

        $url=env("SERVER1b")."&network=".$network."&phoneNumber=".$phone."&amount=".$amnt."&trans_id=".$transid."&return_url=https://superadmin.mcd.5starcompany.com.ng/api/hook";
        $result = file_get_contents($url);

        $findme   = 'trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
             $this->addtrans("server1b",$result,$amnt,1,$transid,$input);
        }else {
             $this->addtrans("server1b",$result,$amnt,1,$transid,$input);
        }
    }

    public function airtimeProcess2($amnt, $network_code, $phone, $transid, $input){
//01 for MTN, 02 for Glo, 03 for Etisalat, 04 for Airtel

        $url=env("SERVER2")."&MobileNetwork=".$network_code."&Amount=".$amnt."&MobileNumber=".$phone."&RequestID=".$transid."&CallBackURL=https://www.5starcompany.com.ng";
        // Perform transaction/initialize on our server to buy
        $resul = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($resul, true);
        // Dump all data of the Array
        $result=$someArray["status"]; // Access Array data

        if ($result == "ORDER_RECEIVED" || $result == "ORDER_COMPLETED") {
            $this->addtrans("server2",$resul,$amnt,1,$someArray["orderid"],$input);
        }else {
            $this->addtrans("server2",$resul,$amnt,0,$someArray["orderid"],$input);
        }
    } //ending function

    public function airtimeProcess3($amnt, $network, $phone, $transid, $input)
    {
        $url=env("SERVER3") ."&network=" . $network . "&number=" . $phone . "&amount=" . $amnt."&transaction_id=".$transid;
//        $url = env("SERVER3")."&network=" . $network . "&number=" . $phone . "&amount=" . $amnt."&ref=".$transid;
        $result = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($result, true);
        // Dump all data of the Array
        $status=$someArray["status"]; // Access Array data
        $ref=$someArray["ref"]; // Access Array data

        if ($status == "success") {
            $this->addtrans("server3",$result,$amnt,1,$transid,$input);
        } else {
            $this->addtrans("server3",$result,$amnt,0,$transid,$input);
        }
    }


    public function airtimeProcess4($amnt, $service_id, $phone, $input)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4")."/users/account/authenticate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => env("SERVER4_AUTH"),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $token = $response['token'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4")."/bills/pay/airtime",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"amount\": \"".$amnt."\",\n  \"service_category_id\": \"" . $service_id . "\",\n  \"phonenumber\": \"" . $phone . "\",\n  \"status_url\": \"https://superadmin.mcd.5starcompany.com.ng/api/hook\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token,
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $respons = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($respons, true);
        $status = $response['status'];

        if($status == "success"){
            $this->addtrans("server4",$respons,$amnt,1,$response['transaction']['_id'],$input);
        }else {
            $this->addtrans("server4",$respons,$amnt,0,$response['transaction']['_id'],$input);
        }
    }

    function dataProcess($price, $productcode, $network, $phone, $transid, $input){
        $url= env('SERVER1_DATA')."&network=".$network."&phoneNumber=".$phone."&price=".$price."&product_code=".$productcode."&trans_id=".$transid."&return_url=https://superadmin.mcd.5starcompany.com.ng/api/hook";
        $result = file_get_contents($url);

        $findme='trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $this->addtrans("server1",$result,$price,1,$transid,$input);
        }else {
            $this->addtrans("server1",$result,$price,0,$transid,$input);
        }
    } //ending function


    function dataProcess2($dataplan, $network_code, $network, $phone, $transid, $price, $input){
//01 for MTN, 02 for Glo, 03 for Etisalat, 04 for Airtel

        $url=env("SERVER2_DATA")."&MobileNetwork=".$network_code."&DataPlan=".$dataplan."&MobileNumber=".$phone."&RequestID=".$transid."&CallBackURL=https://superadmin.mcd.5starcompany.com.ng/api/hook";
        // Perform transaction/initialize on our server to buy
        $resul = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($resul, true);
        // Dump all data of the Array
        $result=$someArray["status"]; // Access Array data

        if ($result == "ORDER_RECEIVED" || $result == "ORDER_COMPLETED") {
            $this->addtrans("server2",$resul,$price,1,$someArray["orderid"],$input);
        }else {
            $this->addtrans("server2",$resul,$price,0,$someArray["orderid"],$input);
        }
    } //ending function dataprocess2

    function dataProcess3($price, $productcode, $phone, $transid, $input, $network){

        $url=env("SERVER3_DATA") ."&number=".$phone."&plan=".$productcode."&transaction_id=".$transid;
//        $url=env("SERVER3_DATA") ."&network=".$network."&number=".$phone."&amount=".$price."&ref=".$transid."&return_url=http://minitechs.com.ng/buydata.php";
        $result = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($result, true);
        // Dump all data of the Array
        $status=$someArray["status"]; // Access Array data

        if ($status  == "success") {
            $ref=$someArray["ref"]; // Access Array data
            $this->addtrans("server3",$result,$price,1,$transid,$input);
        }else {
//            $this->addtrans("server3",$result,$price,0,$transid,$input);
            $this->dataProcess($price, $productcode, $network, $phone,$transid, $input);
        }
    } //ending function

    public function addtrans($server,$server_response, $price, $status, $orderid, $input ){
        $user = User::where('user_name', $input["user_name"])->first();
        if(!$user) {
            return json_encode(['success' => 0, 'message' => 'User not found']);
        }
            if(isset($input['device_details'])){
                $tr['device_details'] = $input['device_details'];
            }else{
                $tr['device_details']="";
            }

            $price=$input['amount'];

        if($input['service']=="airtime") {
            //get airtime discounts
            $airsets=DB::table("tbl_serverconfig_airtime")->where('name','=','discount')->first();

            switch ($input['network']) {
                case "MTN":
                    $d=$airsets->mtn;
                    break;

                    case "GLO":
                    $d=$airsets->glo;
                    break;

                    case "AIRTEL":
                    $d=$airsets->airtel;
                    break;

                    case "9MOBILE":
                    $d=$airsets->etisalat;
                    break;

                default:
                    $d=2;
            }

            $a = $price * $d * 0.01;
            $price = round($price - $a);
        }

            $tr['name']=strtoupper($input['network']).$input['service'];
            $tr['amount']=$price;
            $tr['date']=Carbon::now();
            if(isset($_SERVER['REMOTE_ADDR'])){
                $tr['ip_address']=$_SERVER['REMOTE_ADDR'];
            }else{
            $tr['ip_address']='127.0.0.1';
            }
            $tr['i_wallet']=$user->wallet;
            $tr['user_name']=$input['user_name'];
            $tr['ref']=$orderid;
            $tr['code']=$input['service']."_".$input['coded'];
            $tr['server']=$server;
            $tr['server_response']=$server_response;
            $tr['payment_method']=$input['payment_method'];
            $tr['transid']=$input['transid'];

            if($status==1){
                $tr['status'] = 'delivered';

                if($input['service']=="airtime"){
                    $tr['description']=$input['user_name']." purchase ".$input['network']." ".$input['amount']." airtime on ".$input['phone'] ." using ".$input['payment_method'];
                }else{
                    $tr['description']=$input['user_name']." purchase ".$input['network']." ".$input['service']." ".$input['coded']." on ".$input['phone'] ." using ".$input['payment_method'];
                }
                if($input['payment_method'] =="wallet") {
                    if($input['service']=="data") {
                        $tr['f_wallet'] = $user->wallet - $price - 20;
                    }else{
                        $tr['f_wallet'] = $user->wallet - $price;
                    }
                }else{
                    $tr['f_wallet'] = $user->wallet;
                }

                if($input['service']=="data"){
                    $set=Settings::where('name','general_market')->first();
                    $tr['version']=$input['version'];
                    $tr['o_wallet']=$set->value;
                    $tr['n_wallet']=$tr['o_wallet']+5;
                    $tr['type']='credit';
                    GeneralMarket::create($tr);
                    $set->value=$tr['n_wallet'];
                    $set->save();
                }
            }

            if($status==0){
                if($input['service']=="airtime"){
                    $tr['description']=$input['user_name']." purchase ".$input['network']." ".$input['amount']." airtime and failed to delivered on ".$input['phone'] ." using ".$input['payment_method'];
                }else{
                    $tr['description']=$input['user_name']." purchase ".$input['network']." ".$input['service']." ".$input['coded']." and failed to delivered on ".$input['phone'] ." using ".$input['payment_method'];
                }

                $tr['f_wallet']=$user->wallet;
                $tr['status']='cancelled';
            }

            Transaction::create($tr);

        if($input['payment_method'] =="general_market"){
            $set=Settings::where('name','general_market')->first();
            $tr['transid']=$input['transid'];
            $tr['version']=$input['version'];
            $tr['o_wallet']=$set->value;
            $tr['n_wallet']=$tr['o_wallet']-$price;
            $tr['type']='debit';
            GeneralMarket::create($tr);
            $set->value-=$price;
            $set->save();

            $input["type"]="expenses";
            $input["gl"]="General Market";
            $input["amount"]=$price;
            $input['date'] = Carbon::now();
            $input["narration"]="Being general market used by ".$input['user_name']. " on ".$input['transid'];

            PndL::create($input);
        }

        if($input['payment_method'] =="wallet") {
            $user->wallet=$tr['f_wallet'];
            $user->save();
        }

        $job = (new ServeRequestJob($input, $status, $tr, $user->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);

        $uinfo['full_name']=$user->full_name;
        $uinfo['company_name']=$user->company_name;
        $uinfo['dob']=$user->dob;
        $uinfo['wallet']=$user->wallet;
        $uinfo['bonus']=$user->bonus;
        $uinfo['status']=$user->status;
        $uinfo['level']=$user->level;
        $uinfo['photo']=$user->photo;
        $uinfo['reg_date']=$user->reg_date;
        $uinfo['target']=$user->target;
        $uinfo['user_name']=$user->user_name;
        $uinfo['email']=$user->email;
        $uinfo['phoneno']=$user->phoneno;
        $uinfo['gnews']=$user->gnews;
        $uinfo['fraud']=$user->fraud;
        $uinfo['referral']=$user->referral;
        $uinfo['account_number']=$user->account_number;
        $uinfo['account_number2']=$user->account_number2;
        $uinfo['last_login']=$user->last_login;
        $uinfo['agent_commision']=$user->agent_commision;
        $uinfo['points']=$user->points;

        $uinfo["total_fund"] =Transaction::where([['user_name',$input['user_name']], ['name', 'wallet funding'], ['status', 'successful']])->count();
        $uinfo["total_trans"] =Transaction::where([['user_name',$input['user_name']], ['status', 'delivered']])->count();
        // get user transactions report from transactions table

        $settings=Settings::all();
        foreach ($settings as $setting){
            $sett[$setting->name]=$setting->value;
        }
        $d=array_merge($uinfo, $sett);

        echo json_encode(['success'=> $status, 'message'=>'Transaction executed successfully', 'data'=>$d]);
    }

}
