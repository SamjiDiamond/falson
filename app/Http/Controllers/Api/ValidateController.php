<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppCableTVControl;
use App\Models\AppElectricityControl;
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
                CURLOPT_URL => env('HW_BASEURL') . "electricity/validate",
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

    public function electricity_server2($phone, $provider, $type, $requester = "nm", $sender = "nm")
    {

        $payload='{
    "serviceCode": "V-ELECT",
    "disco": "' . $provider . '",
    "meterNo": "' . $phone . '",
    "type": "' . $type . '"
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

//            $response='{
//  "meterNo": "7624854203",
//  "customerName": " OKPALA UCHENNA",
//  "customerAddress": "OPPOSITE BYCICLE SPARE PARTS, OGBUNIKE ANAMBRA STATE.OGIDI (7624854203)",
//  "customerDistrict": null,
//  "phoneNumber": null,
//  "type": "postpaid",
//  "disco": "EEDC",
//  "status": "200",
//  "minimumPayable": "",
//  "outstadingAmount": ""
//}';
            $rep = json_decode($response, true);

            Log::info("Ringo Validate Electricity. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            if ($rep['status'] == '200') {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['customerName'], 'others' => $rep]);
            } else {
                return response()->json(['success' => 0, 'message' => $rep['message']]);
            }
        }catch (\Exception $e){
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function electricity_server7($phone, $provider, $type, $requester = "nm", $sender = "nm")
    {

        $rac = AppElectricityControl::where("code", strtoupper($provider))->first();

        $payload='{
    "product_id": "' . $rac->autosync_plan_id . '",
    "meter_number": "' . $phone . '",
    "type": "' . $type . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . 'validate/electricity',
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

//            {
//                "status": "ok",
//    "message": "Verified",
//    "data": {
//                "is_valid": true,
//        "customer_name": " ADEGBOYE JOSHUA",
//        "customer_address": null,
//        "customer_district": null,
//        "customer_phone": "7037279289",
//        "type": null,
//        "disco": "",
//        "minimum_payable": 0,
//        "outstanding_amount": 1145.140000000000100044417195022106170654296875
//    }
//}
            $rep = json_decode($response, true);

            Log::info("Autosync Validate Electricity. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            if ($rep['message'] == 'Verified') {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']['customer_name'], 'others' => [
                      "meterNo"=> $phone,
                      "customerName"=> $rep['data']['customer_name'],
                      "customerAddress"=> $rep['data']['customer_address'],
                      "customerDistrict"=> $rep['data']['customer_district'],
                      "phoneNumber"=> $rep['data']['customer_phone'],
                      "type"=> $type,
                      "disco"=> $provider,
                      "status"=> "200",
                      "minimumPayable"=> $rep['data']['minimum_payable'],
                      "outstadingAmount"=> $rep['data']['outstanding_amount']
                ]]);
            } else {
                return response()->json(['success' => 0, 'message' => $rep['message']]);
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
                CURLOPT_URL => env('HW_BASEURL') . 'cables/validate',
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
                    'Content-Type: application/json',
                    'User-Agent: samji'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            $rep = json_decode($response, true);

            Log::info("HW Validate TV. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);


            if ($rep['data']['status'] == 200) {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']['customerName'], 'details' => $rep['data']]);
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
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }


    public function tv_server7($phone, $type, $requester = "nm", $sender = "nm")
    {

        $rac = AppCableTVControl::where("type", strtolower($type))->first();
        $pid = explode("_", $rac->coded)[1];

        $payload='{
    "product_id": "' . $pid . '",
    "iuc_number": "' . $phone . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . 'validate/cable',
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

            $rep = json_decode($response, true);

            Log::info("Autosync Validate Cabletv. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            if ($rep['message'] == 'Verified') {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']['customer_name'], 'details' => $rep['data']]);
            } else {
                return response()->json(['success' => 0, 'message' => $rep['message']]);
            }
        }catch (\Exception $e){
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }


    public function betting($phone, $type, $requester = "nm", $sender = "nm")
    {
        $payload = '{
    "service": "betting",
    "provider": "' . strtoupper($type) . '",
    "number": "' . $phone . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/validate',
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

            Log::info("MCD Validate BETTING. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            $rep = json_decode($response, true);

            try {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Unable to validate'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function airtime($phone, $type, $requester = "nm", $sender = "nm")
    {
        $payload = '{
    "service": "airtime",
    "provider": "' . strtoupper($type) . '",
    "number": "' . $phone . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/validate',
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

            Log::info("MCD Validate Airtime. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            $rep = json_decode($response, true);

            try {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Unable to validate'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function jamb($phone, $type, $requester = "nm", $sender = "nm")
    {
        $payload = '{
    "service": "jamb",
    "provider": "' . strtoupper($type) . '",
    "number": "' . $phone . '"
}';
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/validate',
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

            Log::info("MCD Validate Jamb. - " . $type);
            Log::info("Payload : " . $payload);
            Log::info($response);

            $rep = json_decode($response, true);

            try {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Unable to validate'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }


}
