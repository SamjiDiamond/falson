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
                    $this->dataPlans("all");
                }else{
                    $this->dataPlans($this->option('type'));
                }
                break;

            default:
                $this->error("Invalid Option !!");
                break;
        }
    }

    private function dataPlans($type)
    {
        $this->info("Truncating Reseller & App Data plans table");


        AppDataControl::where('server', '5')->delete();

        if ($type == "all") {
            $this->info("Truncating All Data plans table for s5");
            AppDataControl::where("server", 5)->delete();
            ResellerDataPlans::where('server', '5')->delete();
        } else {
            $this->info("Truncating $type Data plans table for s5");
            AppDataControl::where([["server", 5], ["network", strtoupper($type)]])->delete();
            ResellerDataPlans::where([["server", 5], ["network", strtoupper($type)]])->delete();
        }


        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('UZOBEST_BASEURL') . "network/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ' . env('UZOBEST_TOKEN'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        if ($type == "all" || $type == "AIRTEL") {
            $repi = $rep['AIRTEL_PLAN'];
            $this->item($repi);
        }


        if ($type == "all" || $type == "9MOBILE") {
            $repi = $rep['9MOBILE_PLAN'];
            $this->item($repi);
        }


        if ($type == "all" || $type == "GLO") {
            $repi = $rep['GLO_PLAN'];
            $this->item($repi);
        }


        if ($type == "all" || $type == "MTN") {
            $repi = $rep['MTN_PLAN'];
            $this->item($repi);
        }


    }

    private function item($repi)
    {
        foreach ($repi as $plans) {
            if (str_contains($plans['plan'], "MB")) {
                $allowance = (explode("MB", $plans['plan'])[0] / 1000);
            } elseif (str_contains($plans['plan'], "TB")) {
                $allowance = explode("TB", $plans['plan'])[0] * 1000;
            } else {
                $allowance = explode("GB", $plans['plan'])[0];
            }

            if ($plans['plan_type'] == "CORPORATE GIFTING" || $plans['plan_type'] == "CORPORATE") {
                $type = "CG";
            } elseif ($plans['plan_type'] == "GIFTING") {
                $type = "DG";
            } else {
                $type = $plans['plan_type'];
            }

            ResellerDataPlans::create([
                'name' => $type . " " . $plans['plan'] . " - " . $plans['month_validate'],
                'product_code' => $type,
                'code' => "5_" . $plans['dataplan_id'],
                'level1' => $plans['plan_amount'],
                'level2' => $plans['plan_amount'],
                'level3' => $plans['plan_amount'],
                'level4' => $plans['plan_amount'],
                'level5' => $plans['plan_amount'],
                'price' => $plans['plan_amount'],
                'type' => $allowance,
                'network' => $plans['plan_network'],
                'plan_id' => $plans['dataplan_id'],
                'server' => 5,
                'status' => 0,
            ]);

            AppDataControl::create([
                'name' => $type . " " . $plans['plan'] . " - " . $plans['month_validate'],
                'dataplan' => $allowance,
                'product_code' => $type,
                'network' => $plans['plan_network'],
                'coded' => "5_" . $plans['dataplan_id'],
                'plan_id' => $plans['dataplan_id'],
                'pricing' => $plans['plan_amount'],
                'price' => $plans['plan_amount'],
                'server' => 5,
                'status' => 0,
            ]);
        }
    }

}
