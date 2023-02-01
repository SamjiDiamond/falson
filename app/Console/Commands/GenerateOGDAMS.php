<?php

namespace App\Console\Commands;

use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateOGDAMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:ogdams {--command= : <tv|data|electricity> command to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OGDAMS plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        switch ($this->option('command')) {

            case 'data':
                $this->dataPlans();
                break;

            default:
                $this->error("Invalid Option !!");
                break;
        }
    }


    private function dataPlans()
    {
//        $this->info("Truncating Reseller & App Data plans table");
//
//        ResellerDataPlans::truncate();
//        AppDataControl::truncate();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('OGDAMS_BASEURL') . "get/data/plans",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('OGDAMS_TOKEN'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);


        foreach ($rep as $plans) {
            if(str_contains($plans['name'], "MB")){
                $allowance=(explode("MB", $plans['name'])[0]/1000);
            }elseif(str_contains($plans['name'], "TB")){
                $allowance=(explode("TB", $plans['name'])[0]*1000);
            }else{
                $allowance=explode("GB", $plans['name'])[0];
            }

            $type="DG";
            $network="9MOBILE";

            if(str_contains($plans['name'], "SME")){
                $type = "SME";
            }elseif (str_contains($plans['name'], "CG")){
                $type ="CG";
            }

            if($plans['networkId'] == 1){
                $network="MTN";
            }elseif($plans['networkId'] == 2){
                $network="AIRTEL";
            }elseif($plans['networkId'] == 3){
                $network="GLO";
            }

            ResellerDataPlans::create([
                'name' => $plans['name'],
                'product_code' => $allowance,
                'code' => "4_".$plans['planId'],
                'level1' => $plans['price'],
                'level2' => $plans['price'],
                'level3' => $plans['price'],
                'level4' => $plans['price'],
                'level5' => $plans['price'],
                'price' => $plans['price'],
                'type' => $network,
                'plan_id' => $plans['planId'],
                'server' => 4,
                'status' => 0,
            ]);

            AppDataControl::create([
                'name' => $plans['name'],
                'dataplan' => $allowance,
                'network' => $network,
                'coded' => "4_".$plans['planId'],
                'plan_id' => $plans['planId'],
                'pricing' => $plans['price'],
                'price' => $plans['price'],
                'server' => 4,
                'status' => 0,
            ]);
        }


    }

}
