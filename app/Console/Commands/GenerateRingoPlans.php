<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\ResellerCableTV;
use Illuminate\Console\Command;

class GenerateRingoPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:ringo {--command= : <tv|electricity> command to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Ringo plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        switch ($this->option('command')) {
            case 'tv':
                $this->tvPlans();
                break;
            default:
                $this->error("Invalid Option !!");
                break;
        }

        return Command::SUCCESS;
    }

    private function tvPlans()
    {

        $this->info("Fetching tv plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.api.ringo.ng/api/agent/p2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
"serviceCode" : "V-TV",
"type" : "DSTV",
"smartCardNo" : "10441003943"
}',
            CURLOPT_HTTPHEADER => array(
                'email: '.env('RINGO_EMAIL'),
                'password: '.env('RINGO_PASSWORD'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;


        curl_close($curl);

        $reps = json_decode($response, true);

        $rep=$reps['product'];

        foreach ($rep as $plans) {
            $this->info("Inserting record for " . $plans['name']);

            ResellerCableTV::create([
                'name' => $plans['name'],
                'code' => $plans['code'],
                'amount' => $plans['price'],
                'type' =>  strtolower(explode(" ",$plans['name'])[0]),
                'level1' => $plans['price'],
                'level2' => $plans['price'],
                'level3' => $plans['price'],
                'level4' => $plans['price'],
                'level5' => $plans['price'],
                'status' => 1,
                'server' => 2,
            ]);

            AppCableTVControl::create([
                'name' => $plans['name'],
                'coded' => "2_".$plans['code'],
                'code' => $plans['code'],
                'price' => $plans['price'],
                'type' => strtolower(explode(" ",$plans['name'])[0]),
                'discount' => '1%',
                'status' => 1,
                'server' => 2,
            ]);
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.api.ringo.ng/api/agent/p2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
"serviceCode" : "V-TV",
"type" : "GOTV",
"smartCardNo" : "2022188682"
}',
            CURLOPT_HTTPHEADER => array(
                'email: '.env('RINGO_EMAIL'),
                'password: '.env('RINGO_PASSWORD'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;


        curl_close($curl);

        $reps = json_decode($response, true);

        $rep=$reps['product'];

        foreach ($rep as $plans) {
            $this->info("Inserting record for " . $plans['name']);

            ResellerCableTV::create([
                'name' => $plans['name'],
                'code' => $plans['code'],
                'amount' => $plans['price'],
                'type' => strtolower(explode(" ", $plans['name'])[0]),
                'level1' => '1%',
                'level2' => '1%',
                'level3' => '1%',
                'level4' => '1%',
                'level5' => '1.2%',
                'status' => 1,
                'server' => 2,
            ]);

            AppCableTVControl::create([
                'name' => $plans['name'],
                'coded' => "2_".$plans['code'],
                'code' => $plans['code'],
                'price' => $plans['price'],
                'type' => strtolower(explode(" ",$plans['name'])[0]),
                'discount' => '1%',
                'status' => 1,
                'server' => 2,
            ]);
        }
    }
}
