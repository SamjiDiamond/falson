<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use App\Models\ResellerElecticity;
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
        $this->info("Truncating Reseller & App Data plans table");

        ResellerDataPlans::where('server','1')->delete();
        AppDataControl::where('server','1')->delete();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "data",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('HW_AUTH'),
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: samji'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

//        $response='{"msg":"Data retrieved successfully","data":[{"planId":"4792","network":"9MOBILE","price":"7250","validity":"30 DAYS","allowance":"50 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4721","network":"9MOBILE","price":"725","validity":"30 DAYS","allowance":"5 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4043","network":"9MOBILE","price":"73","validity":"30 DAYS","allowance":"500 MB","size":"MB","name":"9MOBILE_CG"},{"planId":"4262","network":"9MOBILE","price":"675","validity":"30 DAYS","allowance":"4.5 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4465","network":"9MOBILE","price":"5800","validity":"30 DAYS","allowance":"40 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4107","network":"9MOBILE","price":"4350","validity":"30 DAYS","allowance":"30 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4576","network":"9MOBILE","price":"435","validity":"30 DAYS","allowance":"3 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4562","network":"9MOBILE","price":"40","validity":"30 DAYS","allowance":"250 MB","size":"MB","name":"9MOBILE_CG"},{"planId":"4431","network":"9MOBILE","price":"3625","validity":"30 DAYS","allowance":"25 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4952","network":"9MOBILE","price":"2900","validity":"30 DAYS","allowance":"20 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4818","network":"9MOBILE","price":"290","validity":"30 DAYS","allowance":"2 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4714","network":"9MOBILE","price":"2175","validity":"30 DAYS","allowance":"15 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4163","network":"9MOBILE","price":"225","validity":"30 DAYS","allowance":"1.5 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4032","network":"9MOBILE","price":"20","validity":"30 DAYS","allowance":"100 MB","size":"MB","name":"9MOBILE_CG"},{"planId":"4801","network":"9MOBILE","price":"1595","validity":"30 DAYS","allowance":"11 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4536","network":"9MOBILE","price":"14500","validity":"30 DAYS","allowance":"100 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4311","network":"9MOBILE","price":"1450","validity":"30 DAYS","allowance":"10 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4980","network":"9MOBILE","price":"145","validity":"30 DAYS","allowance":"1 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4056","network":"9MOBILE","price":"10875","validity":"30 DAYS","allowance":"75 GB","size":"GB","name":"9MOBILE_CG"},{"planId":"4991","network":"9MOBILE","price":"100","validity":"30 DAYS","allowance":"650 MB","size":"MB","name":"9MOBILE_CG"},{"planId":"4202","network":"9MOBILE","price":"960","validity":"30 DAYS ","allowance":"2 GB","size":"GB","name":"9MOBILE"},{"planId":"4993","network":"9MOBILE","price":"8000","validity":"30 DAYS ","allowance":"40 GB","size":"GB","name":"9MOBILE"},{"planId":"4732","network":"9MOBILE","price":"800","validity":"30 DAYS","allowance":"1.5 GB","size":"GB","name":"9MOBILE"},{"planId":"4237","network":"9MOBILE","price":"4000","validity":"30 DAYS","allowance":"15 GB","size":"GB","name":"9MOBILE"},{"planId":"4024","network":"9MOBILE","price":"400","validity":"30 DAYS ","allowance":"500 MB","size":"MB","name":"9MOBILE"},{"planId":"4585","network":"9MOBILE","price":"400","validity":"7 DAYS","allowance":"1 GB","size":"GB","name":"9MOBILE"},{"planId":"4937","network":"9MOBILE","price":"3200","validity":"30 DAYS","allowance":"11 GB","size":"GB","name":"9MOBILE"},{"planId":"4673","network":"9MOBILE","price":"1600","validity":"30 DAYS","allowance":"4.5 GB","size":"GB","name":"9MOBILE"},{"planId":"4677","network":"9MOBILE","price":"160","validity":"7 DAYS","allowance":"250 MB","size":"MB","name":"9MOBILE"},{"planId":"4867","network":"9MOBILE","price":"12000","validity":"30 DAYS","allowance":"75 GB","size":"GB","name":"9MOBILE"},{"planId":"4042","network":"9MOBILE","price":"1200","validity":"30 DAYS","allowance":"3 GB","size":"GB","name":"9MOBILE"},{"planId":"4891","network":"9MOBILE","price":"1200","validity":"7 DAYS","allowance":"7 GB","size":"GB","name":"9MOBILE"},{"planId":"3452","network":"AIRTEL","price":"95","validity":"7 DAYS","allowance":"300 MB","size":"MB","name":"AIRTEL_CG"},{"planId":"3435","network":"AIRTEL","price":"5480","validity":"30 DAYS","allowance":"20 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3883","network":"AIRTEL","price":"548","validity":"30 DAYS ","allowance":"2 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3892","network":"AIRTEL","price":"4110","validity":"30 DAYS ","allowance":"15 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3227","network":"AIRTEL","price":"30","validity":"7 DAYS","allowance":"100 MB","size":"MB","name":"AIRTEL_CG"},{"planId":"3608","network":"AIRTEL","price":"2740","validity":"30 DAYS ","allowance":"10 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3946","network":"AIRTEL","price":"274","validity":"30 DAYS ","allowance":"1 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3269","network":"AIRTEL","price":"1370","validity":"30 DAYS ","allowance":"5 GB","size":"GB","name":"AIRTEL_CG"},{"planId":"3568","network":"AIRTEL","price":"137","validity":"30 DAYS ","allowance":"500 MB","size":"MB","name":"AIRTEL_CG"},{"planId":"3777","network":"AIRTEL","price":"8000","validity":"30 DAYS","allowance":"40 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3121","network":"AIRTEL","price":"800","validity":"30 DAYS","allowance":"1.2 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3333","network":"AIRTEL","price":"4000","validity":"30 DAYS","allowance":"18 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3986","network":"AIRTEL","price":"400","validity":"7 DAYS","allowance":"750 MB","size":"MB","name":"AIRTEL_DG"},{"planId":"3548","network":"AIRTEL","price":"3200","validity":"30 DAYS","allowance":"15 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3596","network":"AIRTEL","price":"280","validity":"7 DAYS","allowance":"350 MB","size":"MB","name":"AIRTEL_DG"},{"planId":"3237","network":"AIRTEL","price":"2400","validity":"30 DAYS","allowance":"10 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3477","network":"AIRTEL","price":"2000","validity":"30 DAYS","allowance":"6 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3144","network":"AIRTEL","price":"16000","validity":"30 DAYS","allowance":"120 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3495","network":"AIRTEL","price":"1600","validity":"30 DAYS","allowance":"4.5 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3680","network":"AIRTEL","price":"160","validity":"3 DAYS","allowance":"200 MB","size":"MB","name":"AIRTEL_DG"},{"planId":"3516","network":"AIRTEL","price":"12000","validity":"30 DAYS","allowance":"75 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3301","network":"AIRTEL","price":"1200","validity":"30 DAYS","allowance":"3 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"3219","network":"AIRTEL","price":"960","validity":"30 DAYS","allowance":"1.5 GB","size":"GB","name":"AIRTEL_DG"},{"planId":"2401","network":"GLO","price":"690","validity":" 30 DAYS ","allowance":"3 GB","size":"GB","name":"GLO_CG"},{"planId":"2175","network":"GLO","price":"60","validity":"14 DAYS ","allowance":"200 MB","size":"MB","name":"GLO_CG"},{"planId":"2606","network":"GLO","price":"460","validity":"30 DAYS ","allowance":"2 GB","size":"GB","name":"GLO_CG"},{"planId":"2132","network":"GLO","price":"2300","validity":"30 DAYS ","allowance":"10 GB","size":"GB","name":"GLO_CG"},{"planId":"2941","network":"GLO","price":"230","validity":"30 DAYS","allowance":"1 GB","size":"GB","name":"GLO_CG"},{"planId":"2695","network":"GLO","price":"115","validity":"30 DAYS ","allowance":"500 MB","size":"MB","name":"GLO_CG"},{"planId":"2810","network":"GLO","price":"8000","validity":"30 DAYS","allowance":"50 GB","size":"GB","name":"GLO_MEGA"},{"planId":"2510","network":"GLO","price":"6400","validity":"30 DAYS","allowance":"29.5 GB","size":"GB","name":"GLO_MEGA"},{"planId":"2877","network":"GLO","price":"16000","validity":"30 DAYS","allowance":"138 GB","size":"GB","name":"GLO_MEGA"},{"planId":"2457","network":"GLO","price":"14400","validity":"30 DAYS","allowance":"119 GB","size":"GB","name":"GLO_MEGA"},{"planId":"2298","network":"GLO","price":"12000","validity":"30 DAYS","allowance":"93 GB","size":"GB","name":"GLO_MEGA"},{"planId":"2019","network":"GLO","price":"90000","validity":"360 DAYS ","allowance":"1 TB","size":"TB","name":"GLO"},{"planId":"2048","network":"GLO","price":"9000","validity":"30 DAYS","allowance":"50 GB","size":"GB","name":"GLO"},{"planId":"2240","network":"GLO","price":"900","validity":"30 DAYS","allowance":"2.9 GB","size":"GB","name":"GLO"},{"planId":"2448","network":"GLO","price":"7200","validity":"30 DAYS","allowance":"29.5 GB","size":"GB","name":"GLO"},{"planId":"2424","network":"GLO","price":"4500","validity":"30 DAYS","allowance":"18.25 GB","size":"GB","name":"GLO"},{"planId":"2784","network":"GLO","price":"450","validity":"14 DAYS","allowance":"1.35 GB","size":"GB","name":"GLO"},{"planId":"2543","network":"GLO","price":"3600","validity":"30 DAYS","allowance":"13.25 GB","size":"GB","name":"GLO"},{"planId":"2738","network":"GLO","price":"2700","validity":"30 DAYS","allowance":"10 GB","size":"GB","name":"GLO"},{"planId":"2931","network":"GLO","price":"2250","validity":"30 DAYS","allowance":"7.7 GB","size":"GB","name":"GLO"},{"planId":"2504","network":"GLO","price":"18000","validity":"30 DAYS ","allowance":"138 GB","size":"GB","name":"GLO"},{"planId":"2202","network":"GLO","price":"16200","validity":"30 DAYS","allowance":"119 GB","size":"GB","name":"GLO"},{"planId":"2560","network":"GLO","price":"13500","validity":"30 DAYS","allowance":"93 GB","size":"GB","name":"GLO"},{"planId":"2575","network":"GLO","price":"1350","validity":"7 DAYS","allowance":"7 GB","size":"GB","name":"GLO"},{"planId":"1174","network":"MTN","price":"95000","validity":"365 DAYS ","allowance":"1 TB","size":"TB","name":"MTN_DG"},{"planId":"1537","network":"MTN","price":"5225","validity":"30 DAYS ","allowance":"20 GB","size":"GB","name":"MTN_DG"},{"planId":"1438","network":"MTN","price":"3800","validity":"30 DAYS ","allowance":"12 GB","size":"GB","name":"MTN_DG"},{"planId":"1461","network":"MTN","price":"3325","validity":"30 DAYS ","allowance":"10 GB","size":"GB","name":"MTN_DG"},{"planId":"1992","network":"MTN","price":"28500","validity":"30 DAYS ","allowance":"200 GB","size":"GB","name":"MTN_DG"},{"planId":"1038","network":"MTN","price":"20900","validity":"30 DAYS ","allowance":"120 GB","size":"GB","name":"MTN_DG"},{"planId":"1010","network":"MTN","price":"1900","validity":"7 DAYS ","allowance":"7 GB","size":"GB","name":"MTN_DG"},{"planId":"1770","network":"MTN","price":"15200","validity":"30 DAYS ","allowance":"75 GB","size":"GB","name":"MTN_DG"},{"planId":"1511","network":"MTN","price":"1520","validity":"30 DAYS ","allowance":"3 GB","size":"GB","name":"MTN_DG"},{"planId":"1311","network":"MTN","price":"1425","validity":"7 DAYS ","allowance":"5 GB","size":"GB","name":"MTN_DG"},{"planId":"1846","network":"MTN","price":"1140","validity":"30 DAYS ","allowance":"1.5 GB","size":"GB","name":"MTN_DG"},{"planId":"1185","network":"MTN","price":"10450","validity":"30 DAYS ","allowance":"40 GB","size":"GB","name":"MTN_DG"},{"planId":"1741","network":"MTN","price":"795","validity":"30 DAYS","allowance":"3 GB","size":"GB","name":"MTN_CG"},{"planId":"1588","network":"MTN","price":"795","validity":"30 DAYS","allowance":"3 GB","size":"GB","name":"MTN_SME"},{"planId":"1727","network":"MTN","price":"768","validity":"30 DAYS ","allowance":"3 GB","size":"GB","name":"MTN_SME2"},{"planId":"1145","network":"MTN","price":"5300","validity":"30 DAYS","allowance":"20 GB","size":"GB","name":"MTN_CG"},{"planId":"1582","network":"MTN","price":"530","validity":"30 DAYS","allowance":"2 GB","size":"GB","name":"MTN_CG"},{"planId":"1412","network":"MTN","price":"530","validity":"30 DAYS","allowance":"2 GB","size":"GB","name":"MTN_SME"},{"planId":"1339","network":"MTN","price":"512","validity":"30 DAYS ","allowance":"2 GB","size":"GB","name":"MTN_SME2"},{"planId":"1213","network":"MTN","price":"3975","validity":"30 DAYS","allowance":"15 GB","size":"GB","name":"MTN_CG"},{"planId":"1336","network":"MTN","price":"26500","validity":"30 DAYS","allowance":"100 GB","size":"GB","name":"MTN_CG"},{"planId":"1218","network":"MTN","price":"2650","validity":"30 DAYS","allowance":"10 GB","size":"GB","name":"MTN_CG"},{"planId":"1043","network":"MTN","price":"265","validity":"30 DAYS","allowance":"1 GB","size":"GB","name":"MTN_CG"},{"planId":"1523","network":"MTN","price":"2650","validity":"30 DAYS","allowance":"10 GB","size":"GB","name":"MTN_SME"},{"planId":"1178","network":"MTN","price":"265","validity":"30 DAYS","allowance":"1 GB","size":"GB","name":"MTN_SME"},{"planId":"1475","network":"MTN","price":"2560","validity":"30 DAYS ","allowance":"10 GB","size":"GB","name":"MTN_SME2"},{"planId":"1808","network":"MTN","price":"256","validity":"30 DAYS ","allowance":"1 GB","size":"GB","name":"MTN_SME2"},{"planId":"1476","network":"MTN","price":"19875","validity":"30 DAYS","allowance":"75 GB","size":"GB","name":"MTN_CG"},{"planId":"1566","network":"MTN","price":"1325","validity":"30 DAYS","allowance":"5 GB","size":"GB","name":"MTN_CG"},{"planId":"1515","network":"MTN","price":"133","validity":"30 DAYS","allowance":"500 MB","size":"MB","name":"MTN_CG"},{"planId":"1478","network":"MTN","price":"133","validity":"30 DAYS","allowance":"500 MB","size":"MB","name":"MTN_SME"},{"planId":"1687","network":"MTN","price":"1325","validity":"30 DAYS","allowance":"5 GB","size":"GB","name":"MTN_SME"},{"planId":"1995","network":"MTN","price":"128","validity":"30 DAYS ","allowance":"500 MB","size":"MB","name":"MTN_SME2"},{"planId":"1585","network":"MTN","price":"1280","validity":"30 DAYS ","allowance":"5 GB","size":"GB","name":"MTN_SME2"},{"planId":"1747","network":"MTN","price":"10600","validity":"30 DAYS","allowance":"40 GB","size":"GB","name":"MTN_CG"}]}';
        echo $response;

        $rep = json_decode($response, true);

        foreach ($rep['data'] as $plans) {
            if(str_contains($plans['allowance'], "MB")){
                $allowance=(explode("MB", $plans['allowance'])[0]/1000);
            }else{
                $allowance=explode("GB", $plans['allowance'])[0];
            }

            ResellerDataPlans::create([
                'name' => $plans['name'] . " " . $plans['allowance'] . " - " . $plans['validity'],
                'product_code' => $allowance,
                'code' => "1_" . $plans['planId'],
                'level1' => $plans['price'],
                'level2' => $plans['price'],
                'level3' => $plans['price'],
                'level4' => $plans['price'],
                'level5' => $plans['price'],
                'price' => $plans['price'],
                'type' => $plans['network'],
                'network' => $plans['network'],
                'plan_id' => $plans['planId'],
                'server' => 1,
                'status' => 1,
            ]);

            AppDataControl::create([
                'name' => $plans['name'] . " " . $plans['allowance'] . " - " . $plans['validity'],
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
        $this->info("Truncating Reseller & App TV plans table");

        ResellerCableTV::where('server','1')->delete();;
        AppCableTVControl::where('server','1')->delete();;

        $this->info("Fetching tv plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "cables",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('HW_AUTH'),
                'Content-Type: application/json',
                'User-Agent: samji'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

//        $response='{"msg":"Cables retrieved successfully","data":{"dstv":[{"name":"DStv Premium Streaming Subscription","code":"PREMOTT","month":1,"price":29500,"period":1},{"name":"DStv Yanga OTT Streaming Subscription","code":"YANGAOTT","month":1,"price":4200,"period":1},{"name":"DStv Compact Plus Streaming Subscription","code":"COMPLSOTT","month":1,"price":19800,"period":1},{"name":"DStv Compact Streaming Subscription","code":"COMPOTT","month":1,"price":12500,"period":1},{"name":"GWALLE36 - Great Wall Standalone Bouquet E36 + Showmax","code":"SHOWGWALLE36","month":1,"price":4950,"period":1},{"name":"DStv Premium W/Afr E36 + Showmax","code":"SHOWPRWE36","month":1,"price":29500,"period":1},{"name":"DStv Yanga Bouquet E36 + Showmax","code":"SHOWNNJ1E36","month":1,"price":5650,"period":1},{"name":"DStv Comfam Streaming Subscription","code":"COMFAMOTT","month":1,"price":7400,"period":1},{"name":"DStv Compact Plus Bouquet E36 + Showmax","code":"SHOWCOMPLE36","month":1,"price":21250,"period":1},{"name":"DStv Comfam Bouquet E36 + Showmax","code":"SHOWNNJ2E36","month":1,"price":8850,"period":1},{"name":"DStv Prestige","code":"PRESTIGENGE36","month":12,"price":650000,"period":12},{"name":"DStv Compact Bouquet E36 + Showmax","code":"SHOWCOMPE36","month":1,"price":13950,"period":1},{"name":"PRWASIE36-Premium W/Afr E36 + ASIAE36 + Showmax","code":"SHOWPRWASIE36","month":1,"price":27500,"period":1},{"name":"DStv Padi Bouquet E36 + Showmax","code":"SHOWNLTESE36","month":1,"price":5850,"period":1},{"name":"ASIAE36 - Asian Bouquet E36 + Showmax","code":"SHOWASIAE36","month":1,"price":33000,"period":1},{"name":"DStv Premium W/Afr + French Bonus Bouquet E36 + Showmax","code":"SHOWPRWFRNSE36","month":1,"price":45600,"period":1},{"name":"DStv Compact Plus","code":"COMPLE36","month":1,"price":19800,"period":1},{"name":"Asian Bouqet","code":"ASIAE36","month":1,"price":9900,"period":1},{"name":"DStv Compact","code":"COMPE36","month":1,"price":12500,"period":1},{"name":"DStv Premium French","code":"PRWFRNSE36","month":1,"price":45600,"period":1},{"name":"DStv Premium","code":"PRWE36","month":1,"price":29500,"period":1},{"name":"DStv Confam Bouquet E36","code":"NNJ2E36","month":1,"price":7400,"period":1},{"name":"Padi","code":"NLTESE36","month":1,"price":2950,"period":1},{"name":"DStv Premium Asia","code":"PRWASIE36","month":1,"price":33000,"period":1},{"name":"DStv Yanga Bouquet E36","code":"NNJ1E36","month":1,"price":4200,"period":1}],"dstvaddon":[{"name":"French 11 Bouquet E36","code":"FRN11E36","month":1,"price":7200,"period":1},{"name":"French Touch","code":"FRN7E36","month":1,"price":4600,"period":1},{"name":"HDPVR/XtraView","code":"HDPVRE36","month":1,"price":4000,"period":1},{"name":"Asian Add-on","code":"ASIADDE36","month":1,"price":9900,"period":1},{"name":"French Plus","code":"FRN15E36","month":1,"price":16100,"period":1},{"name":"DStv Great Wall Standalone Bouquet","code":"GWALLE36","month":1,"price":2500,"period":1},{"name":"DStv Asian Bouquet E36","code":"ASIAE36","month":1,"price":9900,"period":1}],"gotv":[{"name":"GOtv Supa Plus","code":"GOTVSUPAPLUS","month":1,"price":12500,"period":""},{"name":"GOtv Smallie - quarterly","code":"GOLITE","month":3,"price":3450,"period":""},{"name":"GOtv Supa","code":"GOTVSUPA","month":1,"price":7600,"period":""},{"name":"GOtv Smallie - monthly","code":"GOHAN","month":1,"price":1300,"period":""},{"name":"GOtv Jinja Bouquet","code":"GOTVNJ1","month":1,"price":2700,"period":""},{"name":"GOtv Max","code":"GOTVMAX","month":1,"price":5700,"period":""},{"name":"GOtv Jolli Bouquet","code":"GOTVNJ2","month":1,"price":3950,"period":""},{"name":"GOtv Smallie - yearly","code":"GOLTANL","month":12,"price":10200,"period":""}],"startimes":[{"code":"STAR90CBS","price":2500,"name":"StarTimes Super (dish) -weekly"},{"code":"STARESUTQ","price":1700,"name":"StarTimes Nova (antenna) - Monthly"},{"code":"STARXLJPN","price":4500,"name":"StarTimes Classic (antenna) - Monthly"},{"code":"STAR3GSEP","price":5600,"name":"StarTimes Special (dish) - Monthly"},{"code":"STARNA5CH","price":3000,"name":"StarTimes Basic (antenna) - Monthly"},{"code":"STAR2VAAX","price":14000,"name":"StarTimes Dth_chinese - Monthly"},{"code":"STARDS9EM","price":1000,"name":"StarTimes Dtt_ Basic - Weekly"},{"code":"STARRTVCH","price":3800,"name":"StarTimes Smart (dish) - Monthly"},{"code":"STARLREX6","price":1600,"name":"StarTimes Special (dish) - Weekly"},{"code":"STARI6CSI","price":7500,"name":"StarTimes Super (dish) - Monthly"},{"code":"STARLS7C6","price":500,"name":"StarTimes Nova (dish) - Weekly"}]},"dstvaddon":[{"name":"French 11 Bouquet E36","code":"FRN11E36","month":1,"price":7200,"period":1},{"name":"French Touch","code":"FRN7E36","month":1,"price":4600,"period":1},{"name":"HDPVR/XtraView","code":"HDPVRE36","month":1,"price":4000,"period":1},{"name":"Asian Add-on","code":"ASIADDE36","month":1,"price":9900,"period":1},{"name":"French Plus","code":"FRN15E36","month":1,"price":16100,"period":1},{"name":"DStv Great Wall Standalone Bouquet","code":"GWALLE36","month":1,"price":2500,"period":1},{"name":"DStv Asian Bouquet E36","code":"ASIAE36","month":1,"price":9900,"period":1}]}';

        $rep = json_decode($response, true);

        foreach ($rep['data'] as $rep1) {
            foreach ($rep1 as $plans) {
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

    private function electricityPlans()
    {
        $this->info("Truncating Reseller & App Electricity plans table");

        ResellerElecticity::truncate();

        $this->info("Fetching tv plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HW_BASEURL') . "electricity",
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
            $this->info("Inserting record for " . $plans);

            $name=explode("[",$plans)[0];

            ResellerElecticity::create([
                'name' => $name,
                'code' => $name,
                'discount' => '1%',
                'status' => 1,
                'server' => 1,
            ]);
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
