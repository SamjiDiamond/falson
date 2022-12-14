<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\Settings;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateProvidusAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user_name;
    public function __construct($user_name)
    {
        $this->user_name=$user_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $u = User::find($this->user_name);

        if (!$u) {
            echo "invalid account";
        }

        $w=VirtualAccount::where(["user_id" =>$u->id, "provider" =>"monnify",])->exists();

        if (!$w){

            try {
                $settA=Settings::where('name', 'fund_monnify_apikey')->first();
                $settS=Settings::where('name', 'fund_monnify_secretkey')->first();
                $settC=Settings::where('name', 'fund_monnify_contractcode')->first();

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/auth/login",
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

                echo $response;

//        $response='{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTU5MTQ5Nzc5OSwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiOTYyNTA5NzctMmZkOS00ZDM4LTliYzEtNTMyMTMwYmFiODc0IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9LUFoyQjJUQ1hLIn0.iTOX9RWwA0zcLh3OsTtuFD-ehAbW1FrUcAZLM73V66_oTuV2jJ5wBjWNvyQToZKl2Rf5TH2UgiJyaapAZR6yU9Y4Di_oz97kq0CwpoFoe_rLmfgWgh-jrYEsrkj751jiQQm_vZ6BEw9OJhYtMBb1wEXtY4rFMC7I2CLmCnwpJaMWgrWnTRcoLZlPTcWGMBLeggaY9oLfIIorV9OTVkB2kihA9QHX-8oUGkYpvKyC9ERNYMURcK01LnPgSBWI7lXrjf8Ct2BjHi6RKdlFRPNpp3OAbN9Oautvwy09WS3XOhA8eycA0CNBh8o7jekVLCLjXgz6YrcMH0j9ahb3mPBr7Q","expiresIn":368}}';

                $response = json_decode($response, true);
                $token = $response['responseBody']['accessToken'];

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/bank-transfer/reserved-accounts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => "{\"accountReference\": \"" . $u->user_name . "\", \"accountName\": \"PLANETF-" . $u->user_name . "\",  \"currencyCode\": \"NGN\",  \"contractCode\": \"" . $settC->value . "\",  \"customerEmail\": \"" . $u->email . "\",  \"customerName\": \"PLANETF-" . $u->user_name . "\"}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                echo $response;

                $response = json_decode($response, true);

                $contract_code = $response['responseBody']['contractCode'];
                $account_reference = $response['responseBody']['accountReference'];
                $currency_code = $response['responseBody']['currencyCode'];
                $customer_email = $response['responseBody']['customerEmail'];
                $customer_name = $response['responseBody']['customerName'];
                $account_number = $response['responseBody']['accountNumber'];
                $bank_name = $response['responseBody']['bankName'];
                $collection_channel = $response['responseBody']['collectionChannel'];
                $status = $response['responseBody']['status'];
                $created_on = $response['responseBody']['createdOn'];
                $reservation_reference = $response['responseBody']['reservationReference'];
                $extra = $respons;

                VirtualAccount::create([
                    "user_id" =>$u->id,
                    "provider" =>"monnify",
                    "account_name" =>$customer_name,
                    "account_number" => $account_number,
                    "bank_name" =>$bank_name,
                    "reference" =>$reservation_reference,
                ]);


//            DB::table('tbl_reserveaccount_monnify')->insert(['contract_code' => $contract_code, 'account_reference' => $account_reference, 'currency_code' => $currency_code, 'customer_email' => $customer_email, 'customer_name' => $customer_name, 'account_number' => $account_number, 'bank_name' => $bank_name, 'collection_channel' => $collection_channel, 'status' => $status, 'reservation_reference' => $reservation_reference, 'created_on' => $created_on, 'extra' => $extra]);
//            $u->account_number = $account_number ." | ".$bank_name;
//            $u->save();

                echo $account_number . "|| ";
            }catch (\Exception $e){
                echo "Error encountered ";
                Log::info("Error encountered on Monnify Virtual account generation on ".$u->user_name);
                Log::info($e);
            }
        }

    }
}
