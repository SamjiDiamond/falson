<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ValidateController extends Controller
{
    public function electricity_server1($phone, $type, $requester = "nm", $sender = "nm")
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "disco/validation",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "type": "PREPAID",
    "disco": "'.strtoupper($type).'",
    "meterNo": "'.$phone.'"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' .env('HW_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        if ($rep['code'] == 200) {
            return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'others'=>$rep]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function tv_server1($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL').'tv/validation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "type": "'.strtoupper($type).'",
    "smartCardNo": "'.$phone.'"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('HW_AUTH'),
                'Accept: application/json',
                'Content-Type: application/json'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);


        if ($rep['code'] == 200) {
            return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'details' => $rep]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }


}
