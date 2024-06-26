<?php

namespace App\Console\Commands;

use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateIYIINSTANTPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:iyii {--command= : <tv|data|electricity> command to execute} {--type= : <mtn|airtel|gotv|dstv> type to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate IYII plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
       $this->info("Truncating Reseller & App Data plans table");

       ResellerDataPlans::where('server','3')->delete();
       AppDataControl::where('server','3')->delete();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('IYIINSTANT_BASEURL') . "network/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ' . env('IYIINSTANT_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        $repi=$rep['AIRTEL_PLAN'];

        $this->item($repi);


        $repi=$rep['9MOBILE_PLAN'];

        $this->item($repi);


        $repi=$rep['GLO_PLAN'];

        $this->item($repi);


        $repi=$rep['MTN_PLAN'];

        $this->item($repi);


    }

    private function sDataPlans($type)
    {
       $this->info("Truncating Reseller & App Data plans table for $type");

       echo "Truncating Reseller & App Data plans table for $type";

        ResellerDataPlans::where([['server','3'], ['type', $type]])->delete();
        AppDataControl::where([['server','3'], ['network', $type]])->delete();


        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('IYIINSTANT_BASEURL') . "network/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ' . env('IYIINSTANT_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        if($type == 'AIRTEL') {
            $repi = $rep['AIRTEL_PLAN'];
            $this->item($repi);
        }


        if($type == '9MOBILE') {
            $repi = $rep['9MOBILE_PLAN'];
            $this->item($repi);
        }


        if($type == 'GLO') {
            $repi = $rep['GLO_PLAN'];
            $this->item($repi);
        }


        if($type == 'MTN') {
            $repi = $rep['MTN_PLAN'];
            $this->item($repi);
        }

    }

    private function item($repi){
        foreach ($repi as $plans) {
            if(str_contains($plans['plan'], "MB")){
                $allowance=(explode("MB", $plans['plan'])[0]/1000);
            }else{
                $allowance=explode("GB", $plans['plan'])[0];
            }

            $type = $plans['plan_type'];

            if ($plans['plan_type'] == "CORPORATE GIFTING") {
                $type = "CG";
            } elseif ($plans['plan_type'] == "GIFTING") {
                $type = "DG";
            }

            ResellerDataPlans::create([
                'name' => $type . " " . $plans['plan'] . " - " . $plans['month_validate'],
                'product_code' => $type,
                'code' => "3_" . $plans['dataplan_id'],
                'level1' => $plans['plan_amount'],
                'level2' => $plans['plan_amount'],
                'level3' => $plans['plan_amount'],
                'level4' => $plans['plan_amount'],
                'level5' => $plans['plan_amount'],
                'price' => $plans['plan_amount'],
                'type' => $allowance,
                'network' => $plans['plan_network'],
                'plan_id' => $plans['dataplan_id'],
                'server' => 3,
                'status' => 1,
            ]);

            AppDataControl::create([
                'name' => $type . " " . $plans['plan'] . " - " . $plans['month_validate'],
                'dataplan' => $allowance,
                'product_code' => $type,
                'network' => $plans['plan_network'],
                'coded' => "3_" . $plans['dataplan_id'],
                'plan_id' => $plans['dataplan_id'],
                'pricing' => $plans['plan_amount'],
                'price' => $plans['plan_amount'],
                'server' => 3,
                'status' => 0,
            ]);
        }
    }


}
