<?php

namespace App\Console\Commands;

use App\Models\AppCableTVControl;
use App\Models\CombineDataPlans;
use App\Models\ResellerCableTV;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
                $this->dataPlans($this->option('type'));
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
            $pid = $type['id'];
            foreach ($type['variations'] as $plans) {
                ResellerCableTV::create([
                    'name' => $plans['name'],
                    'code' => $pid . "_" . $plans['code'],
                    'amount' => $plans['amount'],
                    'type' => strtolower($inte),
                    'level1' => '1%',
                    'level2' => '1%',
                    'level3' => '1%',
                    'level4' => '1%',
                    'level5' => '1.5%',
                    'status' => 1,
                    'server' => 7,
                ]);

                AppCableTVControl::create([
                    'name' => strtolower($plans['name']),
                    'coded' => "7_" . $pid . "_" . $plans['code'],
                    'code' => $plans['code'],
                    'price' => $plans['amount'],
                    'type' => strtolower($inte),
                    'discount' => '1%',
                    'status' => 0,
                    'server' => 7,
                ]);
            }
        }

    }


    private function dataPlans($type)
    {
        Log::alert("Setting status to 0 for Reseller, App & Combined Data plans");

        if ($type == "") {
            CombineDataPlans::where('server', '7')->update(['status' => 0]);
        } else {
            CombineDataPlans::where([['server', '7'], ['network', $type]])->update(['status' => 0]);
        }

        Log::alert("Fetching data plans data/sme");

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
        $this->sitem($rep, $type);

        try {
            Log::alert("Fetching data plans DG data");

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
            $this->sitem($rep, $type);
        } catch (\Exception $e) {
            Log::alert("Fetching data plans DG data ==failed ==");
        }


        try {
            Log::alert("Fetching data plans data/transfer");
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
            $this->sitem($rep, $type);
        } catch (\Exception $e) {
            Log::alert("Fetching data plans data/transfer ==failed");
        }

        Log::alert("Fetching data plans data/corporate");

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
        $this->sitem($rep, $type);

    }

    private function sitem($rep, $net)
    {
        foreach ($rep['data']['category']['products'] as $networks) {
            foreach ($networks['variations'] as $plans) {
                $dcn = strtoupper($rep['data']['category']['name']);
                if (str_contains($dcn, "GIFTING") || str_contains($dcn, "Data Gifting")) {
                    $type = "DG";
                } elseif (str_contains($dcn, "CORPORATE") || str_contains($dcn, "CDG")) {
                    $type = "CG";
                } elseif (str_contains($dcn, "DIRECT COUPON")) {
                    $type = "DATA COUPONS";
                } elseif (str_contains($dcn, "SME2")) {
                    $type = "SME2";
                } elseif (str_contains($dcn, "SME")) {
                    $type = "SME";
                } elseif (str_contains($plans['name'], "AWOOF")) {
                    $type = "AWOOF";
                } elseif (str_contains($plans['name'], "XtraData") || str_contains($plans['name'], "Xtraspecial")) {
                    $type = "XTRASPECIAL";
                } elseif (str_contains($plans['name'], "XtraTalk")) {
                    $type = "TALK";
                } elseif (str_contains($plans['name'], "Broadband") || str_contains($plans['name'], "Unlimited")) {
                    $type = "BROADBAND";
                } else {
                    $type = $dcn;
                }

                if ($net == strtoupper($networks['code']) || $net == "") {
                    $this->item($plans, strtoupper($networks['code']), $type, $networks['id']);
                }
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

        $existing = CombineDataPlans::where([
            ['plan_id', $plans['code']],
            ['server', 7]
        ])->first();

        if($type == "DG"){
            $price=$plans['amount'] * 0.98;
        }else{
            if($plans['amount'] == 0) {
                $price = 0;
            }else{
                $price = $plans['amount'] + 10;
            }
        }

        if ($existing) {
            $updateData = ['status' => 1];
            if ($existing->price != $plans['amount']) {
                $updateData['price'] = $plans['amount'];
                $updateData['app_price'] = $price;
                $updateData['res_price'] = $price;
            }
            $existing->update($updateData);
        } else {
            CombineDataPlans::create([
                'name' => trim(explode("-",$plans['name'])[0]),
                'product_code' => $type,
                'dataplan' => $allowance,
                'network' => $network,
                'coded' => "7_" . $productId . "_" . $plans['code'],
                'plan_id' => $plans['code'],
                'duration' => strtolower($this->getDaysAndCategory($plans['name'])),
                'app_price' => $price,
                'res_price' => $price,
                'price' => $plans['amount'],
                'server' => 7,
                'status' => 1,
            ]);
        }
    }

    /**
     * Extracts the duration in days from a product name and categorizes it.
     *
     * @param string $productName The name of the product (e.g., "MTN 1GB 30days", "11GB Weekly", "90GB 2-Month").
     * @return array Contains 'days' (int|null) and 'category' (string).
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
