<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use App\Models\VirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'service' => 'required',
            'provider' => 'required',
            'number' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $s = new ValidateController();

        switch ($input['service']) {
            case "electricity":
                return $s->electricity_server2($input['number'], strtoupper($input['provider']), strtoupper($input['type']));
            case "tv":
                $sett=Settings::where('name', 'tv_server')->first();
                if($sett->value == "HW") {
                    return $s->tv_server1($input['number'], strtolower($input['provider']));
                }else{
                    return $s->tv_server2($input['number'], strtolower($input['provider']));
                }
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }
    }

    public function kycUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required',
            'bvn' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => $validator->errors()->all()]);
        }

        $username=User::where('email', $input['email'])->first();

        if (!$username) {
            return response()->json(['success' => 0, 'message' => 'Email does not exist']);
        }


        try {
            $settA=Settings::where('name', 'fund_monnify_apikey')->first();
            $settS=Settings::where('name', 'fund_monnify_secretkey')->first();
            $settC=Settings::where('name', 'fund_monnify_contractcode')->first();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env("MONNIFY_URL") . "/v1/auth/login",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic " . base64_encode($settA->value .":".$settS->value)
                ),
            ));
            $response = curl_exec($curl);
            $respons = $response;

            curl_close($curl);

            Log::info("Monnify Login");
            Log::info($response);

//            echo $response;

//        $response='{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTU5MTQ5Nzc5OSwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiOTYyNTA5NzctMmZkOS00ZDM4LTliYzEtNTMyMTMwYmFiODc0IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9LUFoyQjJUQ1hLIn0.iTOX9RWwA0zcLh3OsTtuFD-ehAbW1FrUcAZLM73V66_oTuV2jJ5wBjWNvyQToZKl2Rf5TH2UgiJyaapAZR6yU9Y4Di_oz97kq0CwpoFoe_rLmfgWgh-jrYEsrkj751jiQQm_vZ6BEw9OJhYtMBb1wEXtY4rFMC7I2CLmCnwpJaMWgrWnTRcoLZlPTcWGMBLeggaY9oLfIIorV9OTVkB2kihA9QHX-8oUGkYpvKyC9ERNYMURcK01LnPgSBWI7lXrjf8Ct2BjHi6RKdlFRPNpp3OAbN9Oautvwy09WS3XOhA8eycA0CNBh8o7jekVLCLjXgz6YrcMH0j9ahb3mPBr7Q","expiresIn":368}}';

            $response = json_decode($response, true);
            $token = $response['responseBody']['accessToken'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env("MONNIFY_URL") . "/v1/bank-transfer/reserved-accounts/$username->user_name/kyc-info",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => '{
      "bvn":"'.$input['bvn'].'"
}',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

//            echo $response;

            Log::info("Monnify Account Update");
            Log::info("Monnify:- ".json_encode($response));
            Log::info($response);

            $response = json_decode($response, true);

            if($response['requestSuccessful']){
                return response()->json(['success' => 1, 'message' => 'Verified Successfully', 'data'=>$response['responseBody']['accountName']]);
            }else{
                return response()->json(['success' => 0, 'message' => $response['responseMessage']]);
            }

        }catch (\Exception $e){
            echo "Error encountered ";
            Log::info("Error encountered on Monnify account update on ".json_encode($input));
            Log::info($e);

            return response()->json(['success' => 0, 'message' => 'Unable to verify try again later.']);
        }

    }
}
