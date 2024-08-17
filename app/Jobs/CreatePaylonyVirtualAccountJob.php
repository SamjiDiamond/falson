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

class CreatePaylonyVirtualAccountJob implements ShouldQueue
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

        $w=VirtualAccount::where(["user_id" =>$u->id, "provider" =>"paylony", "status" => 1])->exists();


        if (!$w){

            $settS = Settings::where('name', 'fund_paylony_secretkey')->first();
            $settB = Settings::where('name', 'fund_paylony_bank')->first();

            $gender = "Male";
            $address = "34b Olaiya close, Mogodo Phase 1, Lagos";
            $dob = "1996-10-10";
            $phone = preg_replace('/234/', '0', $u->phoneno, 1);

            $provider = $settB->value ?? 'gtb';

            if ($u->full_name == null) {
                $fname = $u->user_name;
                $lname = $u->user_name;
            } else {
                $fname = explode(" ", $u->full_name)[0];
                $lname = explode(" ", $u->full_name)[1] ?? "";
            }

            $payload = '{
    "firstname": "' . $fname . '",
    "lastname": "' . $lname . '",
    "address": "' . $address . '",
    "gender": "' . $gender . '",
    "email": "' . $u->email . '",
    "phone": "' . $phone . '",
    "dob": "' . $dob . '",
    "provider":"' . $provider . '"
}';

            echo $payload;

            Log::info("Create Paylony Account for " . $u->email);
            Log::info($payload);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYLONY_BASEURL') . 'v1/create_account',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $settS->value,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            echo($response);

            Log::info("Response");
            Log::info($response);

            $rep = json_decode($response, true);

            if ($rep['success']) {

                VirtualAccount::create([
                    "user_id" => $u->id,
                    "provider" => "paylony",
                    "account_name" => $rep['data']['account_name'],
                    "account_number" => $rep['data']['account_number'],
                    "bank_name" => strtoupper($settB->value ?? 'gtb')." BANK",
                    "reference" => $rep['data']['reference'],
                ]);

            }

        }

    }
}
