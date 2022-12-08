<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateHWPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:hw {--command= : <tv|data|electricity> command to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate HW plans';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->option('command')) {
            case 'tv':
                $this->tvPlans();
                break;

            case 'data':
                $this->dataPlans();
                break;

            case 'electricity':
                $this->electricityPlans();
                break;

            default:
                $this->error("Invalid Option !!");
                break;
        }
    }

    private function dataPlans()
    {
        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "get/data/plans",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('HW_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        foreach ($rep as $plans) {
            if(str_contains($plans['allowance'], "MB")){
                $allowance=(explode("MB", $plans['allowance'])[0]/1000);
            }else{
                $allowance=explode("GB", $plans['allowance'])[0];
            }

            ResellerDataPlans::create([
                'name' => $plans['allowance'] . " - ".$plans['validity'],
                'product_code' => $allowance,
                'code' => $plans['planId'],
                'level1' => $plans['price'],
                'level2' => $plans['price'],
                'level3' => $plans['price'],
                'level4' => $plans['price'],
                'level5' => $plans['price'],
                'price' => $plans['price'],
                'type' => $plans['network'],
                'plan_id' => $plans['planId'],
                'server' => 1,
                'status' => 1,
            ]);

            AppDataControl::create([
                'name' => $plans['allowance'] . " - ".$plans['validity'],
                'dataplan' => $allowance,
                'network' => $plans['network'],
                'coded' => $plans['planId'],
                'plan_id' => $plans['planId'],
                'pricing' => $plans['price'],
                'price' => $plans['price'],
                'server' => 1,
                'status' => 1,
            ]);
        }
    }

    private function tvPlans()
    {
        $this->info("Fetching tv plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "fetch/packages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . env('HW_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        foreach ($rep as $rep1) {
            foreach ($rep1 as $plans) {
                $this->info("Inserting record for " . $plans['name']);

                ResellerCableTV::create([
                    'name' => $plans['name'],
                    'code' => $plans['code'],
                    'amount' => $plans['price'],
                    'type' =>  strtolower(explode(" ",$plans['name'])[0]),
                    'discount' => '1%',
                    'status' => 1,
                    'server' => 1,
                ]);

                AppCableTVControl::create([
                    'name' => $plans['name'],
                    'coded' => $plans['code'],
                    'code' => $plans['code'],
                    'price' => $plans['price'],
                    'type' => strtolower(explode(" ",$plans['name'])[0]),
                    'discount' => '1%',
                    'status' => 1,
                    'server' => 1,
                ]);
            }
        }
    }
}

//{
//    "network": "MTN",
//        "planId": 3,
//        "price": "230.00",
//        "allowance": "1GB [SME]",
//        "validity": "30 Days"
//    }

//{
//    "name": "GOtv Smallie - monthly",
//      "code": "GOHAN",
//      "month": 1,
//      "price": 900,
//      "period": ""
//    }
