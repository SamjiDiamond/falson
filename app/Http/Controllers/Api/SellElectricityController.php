<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppElectricityControl;
use App\Models\ResellerElecticity;
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
                CURLOPT_URL => env('HW_BASEURL') . "electricity/buy",
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
                    'Content-Type: application/json',
                    'User-Agent: samji'
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
            $dada['server_ref'] = $reqid;

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } elseif ($rep['data']['code'] == 400) {
            $dada['server_ref'] = $rep['data']['reference'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 4, $dada);
            } else {
                return $ms->outputResp($request, $transid, 4, $dada);
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
    "type": "' . strtoupper($request->get('type') ?? 'prepaid') . '",
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
            $response = '{"token":"47902051484719373697","unit":58.7,"amount":4000,"amountCharged":4000,"message":"Successful","status":"200","customerName":"AFOLABI OLAYINKA KAZEEM","date":"2024-07-11 21:23:20","TransRef":"1720729394395","disco":"AEDC_PREPAID","resetToken":"","configureToken":""}';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == '200') {

            if (isset($rep['token'])) {
                $dada['token'] = $rep['token'];
            } else {
                $dada['token'] = "";
            }

            $dada['server_ref'] = $rep['TransRef'];

            if ($requester == "reseller") {
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

    public function server7($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerElecticity::where("code", strtoupper($input['coded']))->first();
        } else {
            $rac = AppElectricityControl::where("code", strtoupper($input['provider']))->first();
        }

        $payload = '{
    "request_ref": "' . $transid . '",
    "meter_number": "' . $phone . '",
    "product_id": "' . $rac->autosync_plan_id . '",
    "type": "' . strtolower($request->get('type') ?? 'prepaid') . '",
    "amount": "' . $request->get('amount') . '",
    "pin": "' . env('AUTOSYNCNG_PIN') . '"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . 'electricity',
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
            $response = '{ "status": "ok", "message": "Ikeja Electricity Distribution Company [IKEDC] Meter No 04289798300 loaded with 1,000.00", "data": { "transaction": { "reference": "9d7145be-c3cc-46ce-8497-29f31186dd87", "request_ref": "samjitest", "type": "Ikeja Electricity Distribution Company [IKEDC]", "details": "Ikeja Electricity Distribution Company [IKEDC] Meter No 04289798300 loaded with 1,000.00", "amount": 1000, "status": "pending", "request_data": { "request_ref": "samjitest", "meter_number": "04289798300", "product_id": "39", "amount": "1000", "type": "prepaid" }, "balance_before": "&#8358;175,533.00", "balance_after": "&#8358;174,533.00", "created_at": "2024-11-08T20:37:49.000000Z", "gateway_id": 8931 } } }';
        }

        Log::info("AutoSync Transaction. - " . $transid);
        Log::info($payload);
        Log::info($response);


        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['error'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }

        $dada['message'] = $rep['message'];

        if ($rep['status'] == "error") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }

        $dada['server_ref'] = $rep['data']['transaction']['reference'];

        if ($rep['data']['transaction']['status'] == "successful") {
            $dada['token'] = $rep['data']['transaction']['token'];
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } elseif ($rep['data']['transaction']['status'] == "pending") {
            $dada['server_ref'] = $rep['data']['transaction']['reference'];
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
}
