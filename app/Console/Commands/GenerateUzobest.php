<?php

namespace App\Console\Commands;

use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateUzobest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:uzobest {--command= : <tv|data|electricity> command to execute} {--type= : <mtn|airtel|gotv|dstv> type to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Uzobest plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        print($this->option('type'));

        switch ($this->option('command')) {

            case 'data':
                if($this->option('type') == ""){
                    $this->dataPlans();
                }else{
                    $this->sDataPlans($this->option('type'));
                }
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
        ResellerDataPlans::where('server','5')->delete();
        AppDataControl::where('server','5')->delete();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('UZOBEST_BASEURL') . "dataplans",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . env('UZOBEST_TOKEN'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);


        foreach ($rep as $plans) {
            $this->item($plans);
        }


    }

    private function sDataPlans($types)
    {
        $this->info("Deleting Reseller & App Data plans table for $types");
//
        ResellerDataPlans::where([['server','5'], ['type', $types]])->delete();
        AppDataControl::where([['server','5'], ['network', $types]])->delete();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('UZOBEST_BASEURL') . "dataplans",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . env('UZOBEST_TOKEN'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);


        foreach ($rep as $plans) {
            echo $plans['network'];
//            echo $type;
            if ($plans['network'] == $types) {
                echo json_encode($plans);
                $this->item($plans);
            }

        }


    }

    private function item($plans)
    {
        if (str_contains($plans['size'], "MB")) {
            $allowance = (explode("MB", $plans['size'])[0] / 1000);
        } elseif (str_contains($plans['size'], "TB")) {
            $allowance = (explode("TB", $plans['size'])[0] * 1000);
        } else {
            $allowance = explode("GB", $plans['size'])[0];
        }

        $type = $plans['type'];

        if (str_contains($plans['type'], "GIFTING")) {
            $type = "DG";
        } elseif (str_contains($plans['type'], "CG") || str_contains($plans['type'], "CDG")) {
            $type = "CG";
        } elseif (str_contains($plans['type'], "DIRECT COUPON")) {
            $type = "DATA COUPONS";
        }

        $plans['price'] = 0;

        ResellerDataPlans::create([
            'name' => $type . " " . $plans['size'] . " - " . $plans['validity'],
            'product_code' => $type,
            'code' => "5_" . $plans['planId'],
            'level1' => $plans['price'],
            'level2' => $plans['price'],
            'level3' => $plans['price'],
            'level4' => $plans['price'],
            'level5' => $plans['price'],
            'price' => $plans['price'],
            'type' => $allowance,
            'network' => $plans['network'],
            'plan_id' => $plans['planId'],
            'server' => 5,
            'status' => 1,
        ]);

        AppDataControl::create([
            'name' => $type . " " . $plans['size'] . " - " . $plans['validity'],
            'product_code' => $type,
            'dataplan' => $allowance,
            'network' => $plans['network'],
            'coded' => "5_" . $plans['planId'],
            'plan_id' => $plans['planId'],
            'pricing' => $plans['price'],
            'price' => $plans['price'],
            'server' => 5,
            'status' => 0,
        ]);
    }
}
