<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;
use Illuminate\Support\Facades\Log;

class SellBettingTopup extends Controller
{
    public function server8($request, $provider, $number, $transid, $amount, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $payload = '{
    "provider": "' . $provider . '",
    "amount": "' . $amount . '",
    "number": "' . $number . '",
    "payment" : "wallet",
    "promo" : "0",
    "ref":"' . $transid . '"
}';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/betting',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . env('MCD_KEY')
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("MCD Purchase BETTING");
            Log::info("Payload : " . $payload);
            Log::info($response);

        } else {
            $response = '{"success":1,"message":"Your transaction is successful","ref":"mcd_i8875478492007","debitAmount":"100","discountAmount":0,"prevBalance":"4260.5","currentBalance":4160.5}';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['success'] == 1) {

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
