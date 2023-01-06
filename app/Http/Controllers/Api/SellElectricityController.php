<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SellElectricityController extends Controller
{
    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $reqid = Carbon::now()->format('YmdHi') . $transid;

        $payload='{
        "phoneNumber" : "'.Auth::user()->phoneno.'",
   "type": "PREPAID",
   "disco" : "' . $code . '",
   "amount" : ' . $request->get('amount') . ',
   "meterNo" : "' . $phone . '"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . "purchase/disco",
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
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

//            Log::info("HW Payload. - " . $payload);

        } else {
            $response = '{ "code": "200", "message": "Purchase Successful", "reference": "HONOUR|WORLD|62|20220611021605|347694", "token": "1498-2330-4576-0458-1880", "unit": 2.95, "taxAmount": null, "bonusUnit": null, "bonusToken": null, "amount": 100, "status": "200", "customerName": null, "customerAddress": "232", "date": "2022-06-11 02:16:18", "disco": "IBEDC_PREPAID" }';
        }

        $rep = json_decode($response, true);

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '200') {
            $dada['token'] = $rep['token'];

            if ($requester == "reseller") {
                $dada['server_ref'] = $reqid;
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            $dada['token'] = "Token: pending";
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }

    public function server2($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $payload='{
    "serviceCode": "P-ELECT",
    "disco": "' . $code . '",
    "meterNo": "' . $phone . '",
    "type": "' . strtoupper($request->get('type')) . '",
    "amount": "' . $request->get('amount') . '",
    "phonenumber": "' . $request->get('phone') . '",
    "request_id": "' . $transid . '"
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

            Log::info("Ringo Electric Payload. - " . $payload);
            Log::info("Ringo Electric Response. - " . $response);

        } else {
            $response = '{ token: "1234-5543-5436-7657", unit: "20kwh", amount: 1000, message: "successful", status: "200", customerName: "Roy", date: "", transId: "bhu76ryu9ijhy", disco: "IKEDC", }';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '200') {
            $dada['token'] = $rep['token'];

            if ($requester == "reseller") {
                $dada['server_ref'] = $rep['transId'];
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            $dada['token'] = "Token: pending";
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }
}
