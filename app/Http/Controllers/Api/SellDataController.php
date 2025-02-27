<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class SellDataController extends Controller
{
    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $network=$rac->network;

            $payload='{
  "network" : "' . $network . '",
   "planId" : "' . $rac->plan_id . '",
  "phone" : "' . $phone . '"
}';


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL').'data/buy',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH'),
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'User-Agent: samji'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("HW Payload. - " . $payload);

        } else {
            $response = '{"msg":"AIRTEL Data Purchase of NGN 95.00 for 09016816446 | 7 DAYS | 300MB was successful","data":{"code":200,"reference":"TN|D|305|20240519174737PM326_8RJVT","message":"You have successfully gifted 2349016816446 with 300MB of Data","msg":"You have successfully gifted 2349016816446 with 300MB of Data","description":"AIRTEL Data Purchase of NGN 95.00 for 09016816446 | 7 DAYS | 300MB","type":"data"},"code":200} ';
        }

        $rep = json_decode($response, true);

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        try {
            if ($rep['data']['code'] == 200) {
                $dada['server_ref'] = $rep['data']['reference'];
                if ($requester == "reseller") {
                    return $rs->outputResponse($request, $transid, 1, $dada);
                } else {
                    return $ms->outputResp($request, $transid, 1, $dada);
                }
            } elseif ($rep['data']['code'] == 300) {
                $dada['server_ref'] = $rep['data']['reference'];
                if ($requester == "reseller") {
                    return $rs->outputResponse($request, $transid, 4, $dada);
                } else {
                    return $ms->outputResp($request, $transid, 4, $dada);
                }
            } else {
                $dada['message'] = $rep['data']['message'];

                //Incase the SIM is not linked to NIN and MTN is holding grudge against the user
                if (env('ENABLE_DELIVERY_NIN_ISSUE', 0) == 1) {
                    if (str_contains($dada['message'], "was not successful. Please try again")) {
                        if ($requester == "reseller") {
                            return $rs->outputResponse($request, $transid, 1, $dada);
                        } else {
                            return $ms->outputResp($request, $transid, 1, $dada);
                        }
                    }
                }

                if ($requester == "reseller") {
                    return $rs->outputResponse($request, $transid, 4, $dada);
                } else {
                    return $ms->outputResp($request, $transid, 4, $dada);
                }
            }
        } catch (\Exception $e) {
//            $dada['message'] = $rep['error'][0]['msg'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }

    }

    public function server2($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
            $code = $rac->code;
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
            $code = $rac->coded;
        }

        switch ($rac->network) {
            case "MTN":
                $service_id = "mtn-data";
                break;

            case "9MOBILE":
                $service_id = "etisalat-data";
                break;

            case "GLO":
                $service_id = "glo-data";
                break;

            case "AIRTEL":
                $service_id = "airtel-data";
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $reqid = Carbon::now()->format('YmdHi') . $transid;


        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"request_id": "' . $reqid . '", "serviceID": "' . $service_id . '","variation_code": "' . $code . '","phone": "' . $phone . '","billersCode": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"MTNData","unique_element":"08166939205","unit_price":100,"quantity":1,"service_verification":null,"channel":"api","commission":3,"total_amount":97,"discount":null,"type":"DataServices","email":"odejinmisamuel@gmail.com","phone":"08166939205","name":null,"convinience_fee":0,"amount":100,"platform":"api","method":"api","transactionId":"16287015152955612203232964"}},"response_description":"TRANSACTIONSUCCESSFUL","requestId":"R16287015121692605289","amount":"100.00","transaction_date":{"date":"2021-08-1118:05:15.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
//            $dada['server_ref'] = $rep['content']['transactions']['transactionId'];
            $dada['server_ref'] = $reqid;
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {

            if(env('ENABLE_DELIVERY_NIN_ISSUE',0) == 1) {
                if (str_contains($dada['message'], "was not successful. Please try again")) {
                    if ($requester == "reseller") {
                        return $rs->outputResponse($request, $transid, 1, $dada);
                    } else {
                        return $ms->outputResp($request, $transid, 1, $dada);
                    }
                }
            }

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }
    }

    public function server3($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        switch ($rac->network) {
            case "MTN":
                $service_id = 1;
                break;

            case "9MOBILE":
                $service_id = 3;
                break;

            case "GLO":
                $service_id = 2;
                break;

            case "AIRTEL":
                $service_id = 4;
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $payload='{
    "network": ' . $service_id . ',
    "mobile_number": "' . $phone . '",
    "plan": ' . $rac->plan_id . ',
    "Ported_number": true
}';
        Log::info("IYII Payload. - " . $payload);

        if (env('FAKE_TRANSACTION', 1) == 0) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('IYIINSTANT_BASEURL').'data/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token ' . env('IYIINSTANT_AUTH'),
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{"id":3765810,"ident":"32af1e248b-a06e-421b-9320-6d486f7f549d","customer_ref":"","network":2,"payment_medium":"MAIN WALLET","balance_before":"38562.0","balance_after":"38456.0","mobile_number":"07050930828","plan":252,"Status":"successful","api_response":"You have successfully gifted 500MB Oneoff to 2347050930828","plan_network":"GLO","plan_name":"500.0MB","plan_amount":"106.0","create_date":"2024-03-28T07:51:15.417087","Ported_number":true}';
        }

        try {
            $rep = json_decode($response, true);
        } catch (Exception $e) {
            $response = '{"error":["SME Data not available on this network currently"]}';
        }

        Log::info("IYII Transaction. - " . $transid);
        Log::info($response);


        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['ident'])) {
            $dada['message'] = $rep['api_response'];

            if(env('ENABLE_DELIVERY_NIN_ISSUE',0) == 1) {
                if (str_contains($dada['message'], "was not successful. Please try again")) {
                    if ($requester == "reseller") {
                        return $rs->outputResponse($request, $transid, 1, $dada);
                    } else {
                        return $ms->outputResp($request, $transid, 1, $dada);
                    }
                }
            }


            if($rep['Status'] == "failed") {
                if ($requester == "reseller") {
                    return $rs->outputResponse($request, $transid, 4, $dada);
                } else {
                    return $ms->outputResp($request, $transid, 4, $dada);
                }
            }else{
                $dada['server_ref'] = $rep['id'];
                if ($requester == "reseller") {
                    return $rs->outputResponse($request, $transid, 1, $dada);
                } else {
                    return $ms->outputResp($request, $transid, 1, $dada);
                }
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }
    }

    public function server4($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        switch ($rac->network) {
            case "MTN":
                $service_id = 1;
                break;

            case "9MOBILE":
                $service_id = 4;
                break;

            case "GLO":
                $service_id = 3;
                break;

            case "AIRTEL":
                $service_id = 2;
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $payload = '{
    "networkId" : ' . $service_id . ',
    "planId" : ' . $rac->plan_id . ',
    "phoneNumber" : "' . $phone . '"
}';

        Log::info("OGDAMS Payload. - " . $payload);

        if (env('FAKE_TRANSACTION', 1) == 0) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('OGDAMS_BASEURL') . 'vend/data',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('OGDAMS_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("OGDAMS Transaction. - " . $transid);
            Log::info($response);

        }else{
            $response='{"status":true,"code":200,"data":{"msg":"Dear Customer, You have successfully shared 2GB Data to 2348143346729. Your SME data balance is 368.14GB expires 01\/05\/2024. Thankyou","ref":"OG|DAMS|91|20240328080957|442329"}}';
        }

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $rep = json_decode($response, true);

        $dada['server_response'] = $response;
        $dada['message'] = $rep['data']['msg'];

        if ($rep['status']) {
            $dada['server_ref'] = $rep['data']['ref'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if(env('ENABLE_DELIVERY_NIN_ISSUE',0) == 1) {
                if (str_contains($dada['message'], "was not successful. Please try again")) {
                    if ($requester == "reseller") {
                        return $rs->outputResponse($request, $transid, 1, $dada);
                    } else {
                        return $ms->outputResp($request, $transid, 1, $dada);
                    }
                }
            }

            $dada['message'] = explode("Sponsor", $rep['data']['msg'])[0];
            $dada['message'] = explode("Your SME data", $rep['data']['msg'])[0];

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }
    }

    public function server5($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }


        switch ($rac->network) {
            case "MTN":
                $service_id = 1;
                break;

            case "9MOBILE":
                $service_id = 3;
                break;

            case "GLO":
                $service_id = 2;
                break;

            case "AIRTEL":
                $service_id = 4;
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }


        $payload = '{
    "network": ' . $service_id . ',
    "mobile_number": "' . $phone . '",
    "plan": ' . $rac->plan_id . ',
    "Ported_number": true
}';

        Log::info("UZOBEST Payload. - " . $payload);

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('UZOBEST_BASEURL') . 'data/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token ' . env('UZOBEST_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("UZOBEST Transaction. - " . $transid);
            Log::info($response);

        } else {
            $response = '{"status":"successful","message":"Data topup of 500MB - SME topped up to to 07037773815 was successful ","transaction_id":"Data6605238e5739d","response":"Dear Customer, You have successfully shared 500MB Data to 2347037773815. Your SME data balance is 19563.68GB expires 03\/05\/2024. Thankyou"}';
        }


        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $rep = json_decode($response, true);

        $dada['server_response'] = $response;

        if (isset($rep['error'])) {
            $dada['message'] = 'Transaction failed. Kindly buy another plan for now, this plan will be restored soon. Your money will be refund in a minute.';
            return $ms->outputResp($request, $transid, 2, $dada);
        }

        if (!isset($rep['ident'])) {
            $dada['message'] = 'Something went wrong. Try again later';
            return $ms->outputResp($request, $transid, 4, $dada);
        }

        $dada['server_ref'] = $rep['ident'];
        $dada['message'] = $rep['api_response'];

        if ($rep['Status'] == "successful" || $rep['Status'] == "processing") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if (env('ENABLE_DELIVERY_NIN_ISSUE', 0) == 1) {
                if (str_contains($dada['message'], "was not successful. Please try again")) {
                    if ($requester == "reseller") {
                        return $rs->outputResponse($request, $transid, 1, $dada);
                    } else {
                        return $ms->outputResp($request, $transid, 1, $dada);
                    }
                }
            }


            $dada['message'] = explode("Sponsor", $rep['api_response'])[0];

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }
    }

    public function server5old($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        switch ($rac->network) {
            case "MTN":
                $service_id = 1;
                break;

            case "9MOBILE":
                $service_id = 3;
                break;

            case "GLO":
                $service_id = 4;
                break;

            case "AIRTEL":
                $service_id = 2;
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $payload = '{
    "network_id" : ' . $service_id . ',
    "plan_id" : ' . $rac->plan_id . ',
    "phone_number" : "' . $phone . '",
    "ported":true
}';

        Log::info("UZOBEST Payload. - " . $payload);

        if (env('FAKE_TRANSACTION', 1) == 0) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('UZOBEST_BASEURL') . 'purchase_data',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . env('UZOBEST_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("UZOBEST Transaction. - " . $transid);
            Log::info($response);

        } else {
            $response = '{"status":"successful","message":"Data topup of 500MB - SME topped up to to 07037773815 was successful ","transaction_id":"Data6605238e5739d","response":"Dear Customer, You have successfully shared 500MB Data to 2347037773815. Your SME data balance is 19563.68GB expires 03\/05\/2024. Thankyou"}';
        }


        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $rep = json_decode($response, true);

        $dada['server_response'] = $response;

        if (!isset($rep['status'])) {
            return $ms->outputResp($request, $transid, 0, $dada);
        }

        $dada['message'] = $rep['response'];

        if ($rep['status'] == "successful" || $rep['status'] == "processing") {
            $dada['server_ref'] = $rep['transaction_id'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if (env('ENABLE_DELIVERY_NIN_ISSUE', 0) == 1) {
                if (str_contains($dada['message'], "was not successful. Please try again")) {
                    if ($requester == "reseller") {
                        return $rs->outputResponse($request, $transid, 1, $dada);
                    } else {
                        return $ms->outputResp($request, $transid, 1, $dada);
                    }
                }
            }


            $dada['message'] = explode("Sponsor", $rep['response'])[0];

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }

    public function server7($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {
        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        $url = "data";

        switch ($rac->product_code) {
            case "SME":
                $url = "data/sme";
                break;

            case "DATA TRANSFER":
                $url = "data/transfer";
                break;

            case "CG":
                $url = "data/corporate";
                break;
        }


        $pid = explode("_", $input['coded'])[1];

        $payload = '{
    "request_ref": "' . $transid . '",
    "phone": "' . $phone . '",
    "product_id": "' . $pid . '",
    "variation_code": "' . $rac->plan_id . '",
    "ported_no": "false",
    "pin": "' . env('AUTOSYNCNG_PIN') . '"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);
        } else {
            $response = '{ "status": "ok", "message": "MTN Gifting Awoof 1GB Daily sent to 08166939205", "data": { "transaction": { "reference": "9d25a88b-0581-4a2a-8bcf-bc2841f75640", "request_ref": "55421632500993330", "type": "MTN Gifting", "details": "MTN Gifting Awoof 1GB Daily sent to 08166939205", "amount": 218, "status": "successful", "request_data": { "request_ref": "55421632500993330", "phone": "08166939205", "product_id": "2", "variation_code": "mtn-1gb-awoof", "webhook_url": "false", "ported_no": "false" }, "balance_before": null, "balance_after": null, "created_at": "2024-10-02T06:31:10.000000Z", "gateway_id": 3067 } } }';
        }

        Log::info("AutoSync Transaction. - " . $transid);
        Log::info($payload);
        Log::info($response);


        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['error'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }

        $dada['message'] = $rep['message'];

        if ($rep['status'] == "error") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }

        $dada['server_ref'] = $rep['data']['transaction']['reference'];

        if ($rep['data']['transaction']['status'] == "successful") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else if ($rep['data']['transaction']['status'] == "pending") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        }
    }

}
