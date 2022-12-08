<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
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
                CURLOPT_URL => env('HW_BASEURL').'purchase/airtime',
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
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("HW Payload. - " . $payload);

        } else {
            $response = '{ "code": 200, "message": "SUCCESSFUL", "reference": "HONOUR|WORLD|31|20220611013326|323222" }';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $dada['server_response'] = $response;

        if ($rep['code'] == 200) {
            $dada['server_ref'] = $rep['reference'];
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

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER5'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
   "country": "NG",
   "customer": "' . $phone . '",
   "amount": ' . $amnt . ',
   "recurrence": "ONCE",
   "type": "AIRTIME",
   "reference": "' . $transid . '"
}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('RAVE_SECRET_KEY'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {

            $response = '{"status":"success","message":"Bill payment Successful","data":{"phone_number":"+2348166939205","amount":101,"network":"MTN","flw_ref":"CF-FLYAPI-20210822051137450241","reference":"BPUSSD16296522980070875117"}}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == 'success') {
            $dada['server_ref'] = $rep['data']['flw_ref'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server5", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server5", $response, $amnt, 1, $transid, $input);
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

}
