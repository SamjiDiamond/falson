<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ValidateController extends Controller
{
    public function electricity_server1($phone, $type, $requester = "nm", $sender = "nm")
    {

        $payload='{
    "type": "PREPAID",
    "disco": "' . strtoupper($type) . '",
    "meterNo": "' . $phone . '"
}';
        try {
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
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            $rep = json_decode($response, true);

            
            Log::info("HW Validate Electricity. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            if ($rep['code'] == 200) {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'others' => $rep]);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }catch (\Exception $e){
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function tv_server1($phone, $type, $requester = "nm", $sender = "nm")
    {

        $payload= '{
    "type": "' . strtoupper($type) . '",
    "smartCardNo": "' . $phone . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . 'tv/validation',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH'),
                    'Accept: application/json',
                    'Content-Type: application/json'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            $rep = json_decode($response, true);

            Log::info("HW Validate TV. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);



            if ($rep['code'] == 200) {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'details' => $rep]);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }catch (\Exception $e){
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function tv_server2($phone, $type, $requester = "nm", $sender = "nm")
    {
        $payload='{
"serviceCode" : "V-TV",
"type" : "' . strtoupper($type) . '",
"smartCardNo" : "' . $phone . '"
}';
        try {
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

            $rep = json_decode($response, true);

            Log::info("RINGO Validate TV. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);


            if ($rep['status'] == "200") {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'details' => $rep]);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }catch (\Exception $e){
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }


}
