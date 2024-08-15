<?php

namespace App\Jobs;

use App\Models\Settings;
use App\Models\User;
use App\Models\VirtualAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BudpayVirtualAccountJob implements ShouldQueue
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
            return;
        }

        $w = VirtualAccount::where(["user_id" => $u->id, "provider" => "budpay","status" => 1])->exists();

        if (!$w) {
            Log::info("Running BUDPAY Virtual account generation on ".$u->user_name);
            $sett=Settings::where('name', 'fund_budpay_secret')->first();

            try {


                if ($u->full_name == null) {
                    $fname = $u->user_name;
                    $lname = $u->user_name;
                } else {
                    $fname = explode(" ", $u->full_name)[0];
                    $lname = explode(" ", $u->full_name)[1] ?? "";
                }

                $payload = '{
    "email": "' . $u->email . '",
    "first_name": "' . $fname . '",
    "last_name": "' . $lname . '",
    "phone": "' . $u->phone . '"
}';

                dd($payload);
                Log::info($payload);
                echo $payload;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("BUDPAY_URL") . "/v1/customer",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . $sett->value,
                        'Content-Type: application/json'
                    ),
                ));
                $response = curl_exec($curl);

                curl_close($curl);

                echo $response;

                Log::info($response);

                $response = json_decode($response, true);

                if ($response['status']) {

                    $payload2 = '{ "customer": "' . $response['data']['customer_code'] . '"}';

                    Log::info(env("BUDPAY_URL") . "/v2/dedicated_virtual_account");
                    Log::info($payload2);

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => env("BUDPAY_URL") . "/v2/dedicated_virtual_account",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_POSTFIELDS => $payload2,
                        CURLOPT_HTTPHEADER => array(
                            "Authorization: Bearer " . $sett->value,
                            'Content-Type: application/json'
                        ),
                    ));
                    $response = curl_exec($curl);

                    curl_close($curl);

                    Log::info($response);

                    echo $response;

                    $response = json_decode($response, true);

                    $customer_name = $response['data']['account_name'];
                    $account_number = $response['data']['account_number'];
                    $bank_name = $response['data']['bank']['name'];
                    $reservation_reference = $response['data']['reference'];


                    VirtualAccount::create([
                        "user_id" => $u->id,
                        "provider" => "budpay",
                        "account_name" => $customer_name,
                        "account_number" => $account_number,
                        "bank_name" => $bank_name,
                        "reference" => $reservation_reference,
                    ]);

                    echo $account_number . "|| ";
                }
            } catch (\Exception $e) {
                echo "Error encountered ";
               Log::info("Error encountered on BUDPAY Virtual account generation on ".$u->user_name);
               Log::info($e);
            }
        }
    }
}
