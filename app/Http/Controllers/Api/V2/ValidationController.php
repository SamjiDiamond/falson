<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Jobs\CreateProvidusAccountJob;
use App\Models\PndL;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
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
                $sett = Settings::where('name', 'tv_server')->first();
                if ($sett->value == "RINGO" || $sett->value == "2") {
                    return $s->tv_server2($input['number'], strtolower($input['provider']));
                } else {
                    return $s->tv_server1($input['number'], strtolower($input['provider']));
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
            'bvn' => 'nullable',
            'nin' => 'nullable'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => $validator->errors()->all()]);
        }

        $username = User::where('email', $input['email'])->first();

        if (!$username) {
            return response()->json(['success' => 0, 'message' => 'Email does not exist']);
        }

        if (!isset($input['bvn']) && !isset($input['nin'])) {
            return response()->json(['success' => 0, 'message' => 'Kindly provide your BVN or NIN or both']);
        }

        if ($username->bvn != null || $username->nin != null) {
            return response()->json(['success' => 0, 'message' => 'KYC can only be done once']);
        }


        if (isset($input['bvn'])) {
            $bvne = User::where([['email', '!=', $input['email']], ['bvn', $input['bvn']]])->first();

            if ($bvne) {
                return response()->json(['success' => 0, 'message' => 'BVN has already been used for another account']);
            }

            $settM = Settings::where('name', 'verification_charge_bvn')->first();
            $payload = '{
                  "bvn":"' . $input['bvn'] . '"
                }';
        }

        if (isset($input['nin'])) {
            $nine = User::where([['email', '!=', $input['email']], ['nin', $input['nin']]])->first();

            if ($nine) {
                return response()->json(['success' => 0, 'message' => 'NIN has already been used for another account']);
            }

            $settM = Settings::where('name', 'verification_charge_nin')->first();
            $payload = '{
                  "nin":"' . $input['nin'] . '"
                }';
        }

        if (isset($input['bvn']) && isset($input['nin'])) {
            $settM = Settings::where('name', 'verification_charge')->first();
            $payload = '{
                  "bvn":"' . $input['bvn'] . '",
                  "nin":"' . $input['nin'] . '"
                }';
        }

        if ($settM->value > 0) {
            if ($username->wallet <= 0) {
                return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
            }

            if ($username->wallet < $settM->value) {
                return response()->json(['success' => 0, 'message' => 'Error, insufficient balancer to complete request']);
            }
        }


        try {
            $settA = Settings::where('name', 'fund_monnify_apikey')->first();
            $settS = Settings::where('name', 'fund_monnify_secretkey')->first();
            $settC = Settings::where('name', 'fund_monnify_contractcode')->first();

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

            Log::info("Monnify Account Update Payload - " . $payload);

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
                CURLOPT_POSTFIELDS => $payload,
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

            if($response['requestSuccessful']) {
                if (isset($input['bvn'])) {
                    $username->bvn = $input['bvn'];
                }

                if (isset($input['nin'])) {
                    $username->nin = $input['nin'];
                }
                $username->full_name = $response['responseBody']['accountName'];
                $username->save();


                if ($settM->value > 0) {
                    if ($username->wallet <= 0) {
                        return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
                    }

                    if ($username->wallet < $settM->value) {
                        return response()->json(['success' => 0, 'message' => 'Error, insufficient balance to complete request']);
                    }

                    $this->chargeCustomer4KYC($settM, $input, $username);
                }

                return response()->json(['success' => 1, 'message' => 'Verified Successfully', 'data' => $response['responseBody']['accountName']]);
            }else {
                if ($response['responseMessage'] == "Cannot find reserved account") {
                    if (isset($input['bvn'])) {
                        $username->bvn = $input['bvn'];
                    }

                    if (isset($input['nin'])) {
                        $username->nin = $input['nin'];
                    }
                    $username->save();


                    if ($settM->value > 0) {
                        if ($username->wallet <= 0) {
                            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
                        }

                        if ($username->wallet < $settM->value) {
                            return response()->json(['success' => 0, 'message' => 'Error, insufficient balance to complete request']);
                        }

                        $this->chargeCustomer4KYC($settM, $input, $username);
                    }

                    CreateProvidusAccountJob::dispatch($username->id);

                    return response()->json(['success' => 1, 'message' => 'KYC Submitted Successfully', 'data' => ""]);

                }
                return response()->json(['success' => 0, 'message' => $response['responseMessage']]);
            }

        } catch (\Exception $e) {
//            echo "Error encountered ";
            Log::info("Error encountered on Monnify account update on " . json_encode($input));
            Log::info($e);

            return response()->json(['success' => 0, 'message' => 'Unable to verify try again later.']);
        }

    }

    private function chargeCustomer4KYC($settM, $input, $username)
    {

        $input['amount'] = $settM->value;
        $input['ref'] = time();
        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = "KYC verification";
        $input['extra'] = "";
        $input['name'] = "KYC Verification";
        $input['status'] = 'successful';
        $input['code'] = 'kycv';
        $input["user_name"] = $username->user_name;
        $input["i_wallet"] = $username->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $input['amount'];

        // mysql inserting a new row
        Transaction::create($input);

        $username->wallet = $input['f_wallet'];
        $username->save();

        $input["type"] = "income";
        $input["gl"] = $input['name'];
        $input["amount"] = $input['amount'];
        $input['date'] = Carbon::now();
        $input["narration"] = "Being " . $input['name'] . " charges from " . $input['user_name'] . " on " . $input['ref'];

        PndL::create($input);
    }
}
