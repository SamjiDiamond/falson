<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateAutosyncng extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:autosyncng {--command= : <tv|data|electricity> command to execute} {--type= : <mtn|airtel|gotv|dstv> type to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Autosyncng plans';

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
            case 'data':
                if ($this->option('type') == "") {
                    $this->dataPlans();
                } else {
                    $this->sDataPlans($this->option('type'));
                }
                break;

            default:
                $this->error("Invalid Option !!");
                break;
        }
    }

    private function tvPlans()
    {
        $this->info("Truncating Reseller & App Data plans table");

        ResellerCableTV::where('server', '7')->delete();
        AppCableTVControl::where('server', '7')->delete();

        $this->info("Fetching tv plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "cable",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);

        foreach ($rep['data']['category']['products'] as $type) {
            $this->info("Inserting record for " . $type['name']);
            $inte = $type['name'];
            foreach ($type['variations'] as $plans) {
                ResellerCableTV::create([
                    'name' => $plans['name'],
                    'code' => $plans['code'],
                    'amount' => $plans['amount'],
                    'type' => $inte,
                    'level1' => '1%',
                    'level2' => '1%',
                    'level3' => '1%',
                    'level4' => '1%',
                    'level5' => '1.5%',
                    'status' => 1,
                    'server' => 6,
                ]);

                AppCableTVControl::create([
                    'name' => $plans['name'],
                    'coded' => "7_" . $plans['code'],
                    'code' => $plans['code'],
                    'price' => $plans['amount'],
                    'type' => $inte,
                    'discount' => '1%',
                    'status' => 0,
                    'server' => 7,
                ]);
            }
        }

    }


    private function dataPlans()
    {
        $this->info("Truncating Reseller & App Data plans table");

        ResellerDataPlans::where('server', '7')->delete();
        AppDataControl::where('server', '7')->delete();

        $this->info("Fetching data plans");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "data/sme",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);
        $this->sitem($rep);

        $this->info("Fetching data plans");

        //DG Data
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "data",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);
        $this->sitem($rep);

        //transfer Data
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "data/transfer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);
        $this->sitem($rep);

        //corporate Data
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "data/corporate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

        $rep = json_decode($response, true);
        $this->sitem($rep);

    }

    private function sitem($rep)
    {
        foreach ($rep['data']['category']['products'] as $networks) {
            foreach ($networks['variations'] as $plans) {
                $dcn = strtoupper($rep['data']['category']['name']);
                if (str_contains($dcn, "GIFTING")) {
                    $type = "DG";
                } elseif (str_contains($dcn, "CORPORATE") || str_contains($dcn, "CDG")) {
                    $type = "CG";
                } elseif (str_contains($dcn, "DIRECT COUPON")) {
                    $type = "DATA COUPONS";
                } elseif (str_contains($dcn, "SME2")) {
                    $type = "SME2";
                } elseif (str_contains($dcn, "SME")) {
                    $type = "SME";
                } else {
                    $type = $dcn;
                }

                $this->item($plans, strtoupper($networks['code']), $type, $networks['id']);
            }
        }
    }


    private function item($plans, $network, $type, $productId)
    {

        if (str_contains($plans['name'], "MB")) {
            $ext = explode("MB", $plans['name'])[0];
            $allowance = (explode(" ", $ext)[1] ?? explode(" ", $ext)[0] / 1000);
        } elseif (str_contains($plans['name'], "TB")) {
            $ext = explode("TB", $plans['name'])[0];
            $allowance = (explode(" ", $ext)[1] ?? explode(" ", $ext)[0] * 1000);
        } else {
            $ext = explode("GB", $plans['name'])[0];
            $allowance = explode(" ", $ext)[1] ?? explode(" ", $ext)[0];
        }

        $plans['price'] = 0;

        ResellerDataPlans::create([
            'name' => substr($plans['name'], 0, 60),
            'product_code' => $type,
            'code' => "7_" . $productId . "_" . substr($plans['code'], 0, 20),
            'level1' => $plans['amount'],
            'level2' => $plans['amount'],
            'level3' => $plans['amount'],
            'level4' => $plans['amount'],
            'level5' => $plans['amount'],
            'price' => $plans['amount'],
            'type' => $allowance,
            'network' => $network,
            'plan_id' => $plans['code'],
            'server' => 7,
            'status' => 0,
        ]);

        AppDataControl::create([
            'name' => $plans['name'],
            'product_code' => $type,
            'dataplan' => $allowance,
            'network' => $network,
            'coded' => "7_" . $productId . "_" . $plans['code'],
            'plan_id' => $plans['code'],
            'pricing' => $plans['amount'],
            'price' => $plans['amount'],
            'server' => 7,
            'status' => 0,
        ]);
    }

}
