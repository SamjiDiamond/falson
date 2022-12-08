<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;

class SellBettingTopup extends Controller
{
    public function server7($request, $provider, $number, $transid, $amount, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $json = '{"amount": "' . $amount . '","customerId": "' . $number . '","provider": "' . $provider . '","reference": "' . $transid . '"}';
            $code = json_decode($json, true);
            ksort($code);
            $sorted = json_encode($code);
            $sec_key = hash_hmac('SHA512', $sorted, trim(env('GIFTBILLS_ENCRYPTION')));

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('GIFTBILLS_URL') . "betting/topup",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'MerchantId: ' . env('GIFTBILLS_MERCHANTID'),
                    'Authorization: Bearer ' . env('GIFTBILLS_API_KEY'),
                    'Encryption: ' . $sec_key,
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{ "success": true, "code": "00000", "message": "SUCCESSFUL", "data": { "orderNo": "211104130931335009", "reference": "25696593r9622", "status": "PENDING", "errorMsg": null } }';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['success']) {

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

    public function server0($request, $provider, $number, $transid, $amount, $input, $dada, $requester)
    {
        $message = "Betting: " . $input['provider'] . "|#" . $input['amount'] . "|" . $input['number'];

        $push = new PushNotificationController();
        $push->PushNotiAdmin($message, "Purchase Notification");

        $dada['server_response'] = "manual";

        $rs = new PayController();
        $ms = new V2\PayController();


        if ($requester == "reseller") {
            return $rs->outputResponse($request, $transid, 1, $dada);
        } else {
            return $ms->outputResp($request, $transid, 0, $dada);
        }
    }
}
