<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SellAirtimeController extends Controller
{

    public function server1($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $payload='{
  "network" : "' . $net . '",
   "amount" : ' . $amnt. ',
  "phone" : "' . $phone . '"
}';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL').'airtime/buy',
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
            $response = '{ "msg": "MTN Airtime purchase of NGN 100.00 for 08166939205 was successful", "data": { "code": 200, "reference": "TN|A|125|20240325115852AM921_JARAC", "msg": "SUCCESSFUL", "description": "MTN Airtime Purchase", "message": "SUCCESSFUL", "type": "airtime" } }';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $dada['server_response'] = $response;

        if ($rep['data']['code'] == 200) {
            $dada['server_ref'] = $rep['data']['reference'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }

    public function server2($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        switch ($net) {
            case "MTN":
                $service_id = "MFIN-5-OR";
                break;

            case "9MOBILE":
                $service_id = "MFIN-2-OR";
                break;

            case "GLO":
                $service_id = "MFIN-6-OR";
                break;

            case "AIRTEL":
                $service_id = "MFIN-1-OR";
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        $payload='{
    "serviceCode": "VAR",
    "msisdn": "' . $phone . '",
    "amount": "' . $amnt . '",
    "request_id": "' . $transid . '",
    "product_id": "'.$service_id.'"
}';


        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('RINGO_BASEURL'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'email: '.env('RINGO_EMAIL'),
                    'password: '.env('RINGO_PASSWORD'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);


            Log::info("Ringo Airtime Payload. - " . $payload);
            Log::info("Ringo Airtime Response. - " . $response);

        } else {

            $response = "{ 'message' : 'successful', 'status' : '200', 'productamount' : '200',  'amountCharged' :'200',  'transref' :'hgwuiuegyu', 'type': 'airtime', 'network' : 'mtn', 'date' : 'date', 'phone' : '09087655643' }";
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == '200') {
            $dada['server_ref'] = $rep['TransRef'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } elseif ($rep['status'] == '400') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }

    public function server3($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "9MOBILE":
                $netcode = "etisalat";
                break;
            default:
                $netcode = strtolower($net);
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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $reqid . '", "serviceID": "' . $netcode . '","amount": "' . $amnt . '","phone": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);
        } else {

            $response = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"MTN Airtime VTU","unique_element":"08166939205","unit_price":100,"quantity":1,"service_verification":null,"channel":"api","commission":3,"total_amount":97,"discount":null,"type":"Airtime Recharge","email":"odejinmisamuel@gmail.com","phone":"08166939205","name":null,"convinience_fee":0,"amount":100,"platform":"api","method":"api","transactionId":"16286982315467608027176693"}},"response_description":"TRANSACTION SUCCESSFUL","requestId":"R16286982281950119922","amount":"100.00","transaction_date":{"date":"2021-08-11 17:10:31.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';
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
//                $tran->addtrans("server6", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server7($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "MTN":
                $netcode = "1";
                break;
            case "GLO":
                $netcode = "15";
                break;
            case "AIRTEL":
                $netcode = "16";
                break;
            case "9MOBILE":
                $netcode = "17";
                break;
            default:
                $netcode = strtolower($net);
        }


        $reqid = Carbon::now()->format('YmdHi') . $transid;

        $payload = '{
    "request_ref": "' . $reqid . '",
    "phone": "' . $phone . '",
    "product_id": "' . $netcode . '",
    "amount": "' . $amnt . '",
    "is_mtn_awuf": "false",
    "ported_no": "false",
    "pin": "' . env('AUTOSYNCNG_PIN') . '"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "airtime",
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
            $response = '{ "status": "ok", "message": "MTN VTU Airtime #100 sent to 08166939205", "data": { "transaction": { "reference": "9d1ccb8f-78a0-430a-b1a8-7976b35bae94", "request_ref": "554215632562009983330", "type": "MTN VTU", "details": "MTN VTU Airtime #100 sent to 08166939205", "amount": 100, "status": "successful", "request_data": { "request_ref": "554215632562009983330", "phone": "08166939205", "product_id": "1", "amount": "100", "is_mtn_awuf": "false", "webhook_url": "false", "ported_no": "false" }, "balance_before": null, "balance_after": null, "created_at": "2024-09-27T20:46:38.000000Z", "gateway_id": 8931 } } }';
//            $response = '{ "status": "ok", "message": "no gateway found for this service, kindly set it.", "data": { "transaction": { "reference": "9d0e278b-3eee-4efc-bdc8-4fa5742b4303", "request_ref": "5254215632562300", "type": "MTN VTU", "details": "no gateway found for this service, kindly set it.", "amount": 100, "status": "failed", "request_data": { "request_ref": "5254215632562300", "phone": "08166939205", "product_id": "1", "amount": "100", "is_mtn_awuf": "false", "webhook_url": "false", "ported_no": "false" }, "balance_before": null, "balance_after": null, "created_at": "2024-09-20T14:06:25.000000Z", "gateway_id": null } } }';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['data']['transaction']['status'] == "successful") {
            $dada['server_ref'] = $rep['data']['transaction']['reference'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }

}
