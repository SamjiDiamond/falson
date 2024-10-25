<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppCableTVControl;
use App\Models\ResellerCableTV;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SellTVController extends Controller
{

    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
        }

        $reqid = Carbon::now()->format('YmdHi') . $transid;

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $payload='{
    "type": "' . strtoupper($rac->type) . '",
    "smartCardNo": "' . $phone . '",
    "packagename": "' . $rac->name . '",
    "productsCode": "' . $rac->code . '",
    "amount": "' . $rac->price . '"
}';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . "cables/buy",
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

            Log::info("HW Payload. - " . $payload);

        } else {
            $response = '{ "code": 200, "message": "Payment Successful", "reference": "HONOUR|WORLD|11|20220610234440|226156" }';
        }

        $rep = json_decode($response, true);

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == 200) {
//            $dada['server_ref'] = $rep['content']['transactions']['transactionId'];
            $dada['server_ref'] = $reqid;
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

    public function server2($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
        }

        $reqid = Carbon::now()->format('YmdHi') . $transid;

        if (env('FAKE_TRANSACTION', 1) == 0) {

            if(strtoupper($rac->type) == "GOTV") {
                $payload = '{
                    "serviceCode" : "P-TV",
                    "type" : "' . strtoupper($rac->type) . '",
                    "code" : "' . strtoupper($rac->type) . '",
                    "smartCardNo" : "' . $phone . '",
                    "name" : "' . $rac->name . '",
                    "code": "' . $rac->code . '",
                    "period": "1",
                    "request_id": "' . $transid . '"
                }';
            }elseif(strtoupper($rac->type) == "DSTV"){
                $payload = '{
                    "serviceCode": "P-TV",
                    "type": "' . strtoupper($rac->type) . '",
                    "smartCardNo": "' . $phone . '",
                    "name": "' . $rac->name . '",
                    "code": "' . $rac->code . '",
                    "period": "1",
                    "request_id": "' . $transid . '",
                    "hasAddon": "False",
                    "addondetails": {
                        "name": "Asian Add-on",
                        "addoncode": "ASIADDE36"
                    }
                }';
            }else{
                $payload = '{
                    "serviceCode": "P-TV",
                    "type": "' . strtoupper($rac->type) . '",
                    "smartCardNo": "' . $phone . '",
                    "request_id": "' . $transid . '",
                    "price": "' . $rac->price . '"
                }';
            }

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

            Log::info("RINGO Payload. - " . $payload);

        } else {
            $response = '{ "message": "Successful", "status": 200, "transref": "BUBUBCL8238BJO", "date": "date", "type": "GOTV", "package": "GOTV GOHAN", "amount": "1000", "amountCharged": "1000" }';
        }


        $rep = json_decode($response, true);

        Log::info("RINGO Transaction. - " . $transid);
        Log::info($response);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == 200) {
            $dada['server_ref'] = $rep['transref'];
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

    public function server6($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $reqid . '", "serviceID": "' . $rac->type . '","variation_code": "' . $rac->code . '","phone": "' . $phone . '","billersCode": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{ "code":"000", "response_description":"TRANSACTION SUCCESSFUL", "requestId":"SAND0192837465738253A1HSD", "transactionId":"1563873435424", "amount":"50.00", "transaction_date":{ "date":"2019-07-23 10:17:16.000000", "timezone_type":3, "timezone":"Africa/Lagos" }, "purchased_code":"" }';
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
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server7($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
            $pid = explode("_", $input['coded'])[0];
            $vcode = explode("_", $input['coded'])[1];
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
            $pid = explode("_", $input['coded'])[1];
            $vcode = $rac->code;
        }


        $reqid = Carbon::now()->format('YmdHi') . $transid;

        $payload = '{
    "request_ref": "' . $reqid . '",
    "iuc_number": "' . $phone . '",
    "product_id": "' . $pid . '",
    "variation_code": "' . $vcode . '",
    "type": "change",
    "webhook_url": "false",
    "pin": "' . env('AUTOSYNCNG_PIN') . '"
}';

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "cable",
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
            $response = '{ "status": "ok", "message": "GOTv IUC 08166939205 loaded with 1 Month of GOtv Max ", "data": { "transaction": { "reference": "9d2a2095-52ea-4370-bf2e-461938b37bfc", "request_ref": "554216325009933301", "type": "GOTv", "details": "GOTv IUC 08166939205 loaded with 1 Month of GOtv Max ", "amount": 7200, "status": "successful", "request_data": { "request_ref": "554216325009933301", "iuc_number": "08166939205", "product_id": "50", "variation_code": "GOTVMAX", "type": "change", "webhook_url": "false" }, "balance_before": null, "balance_after": null, "created_at": "2024-10-04T11:50:08.000000Z", "gateway_id": 8931 } } }';
        }

        Log::info("AutoSync Transaction. - " . $transid);
        Log::info($payload);
        Log::info($response);


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
