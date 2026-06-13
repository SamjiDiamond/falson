<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\CombineDataPlans;
use App\Models\ResellerCableTV;
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
        $this->info("Setting status to 0 for Combined Data plans");
        CombineDataPlans::where('server', '6')->update(['status' => 0]);

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

                    $network = str_replace('ETISALAT', '9MOBILE', strtoupper(explode('-', $inte)[0]));
                    $existingCombine = CombineDataPlans::where([['plan_id', $plans['variation_code']], ['server', 6]])->first();
                    if ($existingCombine) {
                        $updateCombine = ['status' => 1];
                        if ($existingCombine->price != $plans['variation_amount']) {
                            $updateCombine['price'] = $plans['variation_amount'];
                            $updateCombine['app_price'] = $plans['variation_amount'];
                            $updateCombine['res_price'] = $plans['variation_amount'];
                        }
                        $existingCombine->update($updateCombine);
                    } else {
                        CombineDataPlans::create([
                            'name' => $plans['name'],
                            'product_code' => "DG",
                            'dataplan' => $allowance,
                            'network' => $network,
                            'coded' => "6_" . $plans['variation_code'],
                            'plan_id' => $plans['variation_code'],
                            'duration' => strtolower($this->getDaysAndCategory($plans['name'])),
                            'app_price' => $plans['variation_amount'],
                            'res_price' => $plans['variation_amount'],
                            'price' => $plans['variation_amount'],
                            'server' => 6,
                            'status' => 1,
                        ]);
                    }
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

    /**
     * Extracts the duration in days from a product name and categorizes it.
     *
     * @param string $productName The name of the product (e.g., "MTN 1GB 30days", "11GB Weekly", "90GB 2-Month").
     * @return string The duration category.
     */
    public static function getDaysAndCategory(string $productName): string
    {
        $days = null;
        $category = 'Unknown';
        $productName = strtolower($productName);

        // 1. Check for specific time-based keywords (Weekly, Monthly, Yearly, Day)
        if (preg_match('/(\d+)\s*years?|yearly/', $productName, $matches)) {
            $multiplier = $matches[1] ?? 1;
            $days = (int)$multiplier * 365;
            $category = 'Yearly';
        } elseif (preg_match('/(\d+)\s*months?|monthly/', $productName, $matches)) {
            $multiplier = $matches[1] ?? 1;
            // Use an average of 30 days per month for categorization
            $days = (int)$multiplier * 30;
            $category = 'Monthly';
        } elseif (preg_match('/(\d+)\s*weeks?|weekly/', $productName, $matches)) {
            $multiplier = $matches[1] ?? 1;
            $days = (int)$multiplier * 7;
            $category = 'Weekly';
        } elseif (preg_match('/(\d+)\s*(days?|day)/', $productName, $matches)) {
            // Catches "30days", "2 Days", "1 Day", etc.
            $days = (int)$matches[1];
            if ($days === 1) {
                $category = 'Daily';
            } elseif ($days > 1 && $days <= 7) {
                $category = 'Weekly'; // Treat 2-7 days as short-term/weekly-ish
            } elseif ($days > 7 && $days <= 31) {
                $category = 'Monthly'; // Treat 8-31 days as monthly-ish
            } elseif ($days > 31) {
                $category = 'Yearly'; // For very long durations in days
            }
        } elseif (str_contains($productName, 'daily')) {
            $days = 1;
            $category = 'Daily';
        }

        // Refine 'Daily' category for specific keywords that imply a single day
        if ($days === 1 && $category !== 'Daily') {
            $category = 'Daily';
        }

        // Final categorization based on extracted days (override if keyword categorization was too broad)
        if ($days !== null) {
            if ($days <= 1) {
                $category = 'Daily';
            } elseif ($days <= 7) {
                $category = 'Weekly';
            } elseif ($days <= 31) { // Up to 31 days
                $category = 'Monthly';
            } else { // 32 days and above
                $category = 'Yearly';
            }
        }

        // Handle cases like "Night Plan" which are typically daily/short duration
        if (str_contains($productName, 'night plan') && $days === null) {
            $days = 1;
            $category = 'Daily';
        }

        return $category;
    }
}
