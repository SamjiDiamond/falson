<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use App\Models\ResellerElecticity;
use Illuminate\Console\Command;

class GenerateVTPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:vtpass {--command= : <tv|data|electricity> command to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VTpass plans';

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

    private function tvPlans()
    {
        $this->info("Fetching tv plans");

//        $inters = ['dstv', 'gotv', 'startimes', 'showmax'];
        $inters = ['showmax'];

        foreach ($inters as $inte) {

            $this->info("Fetching " . $inte . " plans");

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "service-variations?serviceID=" . $inte,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            echo $response;

            curl_close($curl);

            $rep = json_decode($response, true);

            foreach ($rep['content']['varations'] as $plans) {
                $this->info("Inserting record for " . $plans['name']);

                ResellerCableTV::create([
                    'name' => $plans['name'],
                    'code' => $plans['variation_code'],
                    'amount' => $plans['variation_amount'],
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
                    'coded' => "6_" . $plans['variation_code'],
                    'code' => $plans['variation_code'],
                    'price' => $plans['variation_amount'],
                    'type' => $inte,
                    'discount' => '1%',
                    'status' => 1,
                    'server' => 6,
                ]);
            }
        }

    }

    private function dataPlans()
    {
        $this->info("Fetching data plans");
        $inters = ['smile-direct', 'spectranet'];

        foreach ($inters as $inte) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "service-variations?serviceID=" . $inte,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            echo $response;

            curl_close($curl);

            $rep = json_decode($response, true);

            foreach ($rep['content']['varations'] as $plans) {
                if ($plans['name'] != "Buy Airtime") {

                    $vp = explode(" ", $plans['name']);
                    $vp = $vp[0];

                    if (str_contains($vp, "MB")) {
                        $allowance = (explode("MB", $vp)[0] / 1000);
                    } elseif (str_contains($vp, "TB")) {
                        $allowance = explode("TB", $vp)[0] * 1000;
                    } else {
                        $allowance = explode("GB", $vp)[0];
                    }

                    ResellerDataPlans::create([
                        'name' => $plans['name'],
                        'product_code' => "DG",
                        'code' => "6_" . $plans['variation_code'],
                        'level1' => $plans['variation_amount'],
                        'level2' => $plans['variation_amount'],
                        'level3' => $plans['variation_amount'],
                        'level4' => $plans['variation_amount'],
                        'level5' => $plans['variation_amount'],
                        'price' => $plans['variation_amount'],
                        'type' => $allowance,
                        'network' => str_replace('ETISALAT', '9MOBILE', strtoupper(explode('-', $inte)[0])),
                        'plan_id' => $plans['variation_code'],
                        'server' => 6,
                        'status' => 1,
                    ]);

                    AppDataControl::create([
                        'name' => $plans['name'],
//                        'dataplan' => $allowance,
                        'product_code' => "DG",
                        'network' => str_replace('ETISALAT', '9MOBILE', strtoupper(explode('-', $inte)[0])),
                        'coded' => "6_" . $plans['variation_code'],
                        'plan_id' => $plans['variation_code'],
                        'pricing' => $plans['variation_amount'],
                        'price' => $plans['variation_amount'],
                        'server' => 6,
                        'status' => 1,
                    ]);
                }
            }
        }
    }

    private function electricityPlans()
    {
        $this->info("Add electricity");

        ResellerElecticity::create([
            'name' => 'IKEDC',
            'code' => 'ikeja-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'EKEDC',
            'code' => 'eko-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'KEDCO',
            'code' => 'kano-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'PHED',
            'code' => 'portharcourt-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'JED',
            'code' => 'jos-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'IBEDC',
            'code' => 'ibadan-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'KAEDCO',
            'code' => 'kaduna-electric',
            'discount' => '0.5%',
        ]);

        ResellerElecticity::create([
            'name' => 'AEDC',
            'code' => 'abuja-electric',
            'discount' => '0.5%',
        ]);

    }
}
