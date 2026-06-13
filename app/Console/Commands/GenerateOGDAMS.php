<?php

namespace App\Console\Commands;

use App\Models\CombineDataPlans;
use Illuminate\Console\Command;

class GenerateOGDAMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:ogdams {--command= : <tv|data|electricity> command to execute} {--type= : <mtn|airtel|gotv|dstv> type to execute}';

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
        $this->info("Setting status to 0 for Combined Data plans records");

        CombineDataPlans::where('server', '4')->update(['status' => 0]);

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
            $this->item($plans);
        }


    }

    private function sDataPlans($types)
    {
        $this->info("Setting status to 0 for Combined Data plans records");

        CombineDataPlans::where([['server', '4'], ['network', $types]])->update(['status' => 0]);

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

            $network = "9MOBILE";
            if ($plans['networkId'] == 1) {
                $network = "MTN";
            } elseif ($plans['networkId'] == 2) {
                $network = "AIRTEL";
            } elseif ($plans['networkId'] == 3) {
                $network = "GLO";
            }

            if ($network == $types) {
                $this->item($plans);
            }
        }


    }


    private function item($plans)
    {
        if (str_contains($plans['name'], "MB")) {
//            $allowance = (explode("MB", $plans['name'])[0] / 1000);
            $allowance = 0;
        } elseif (str_contains($plans['name'], "TB")) {
//            $allowance = (explode("TB", $plans['name'])[0] * 1000);
            $allowance = 0;
        } else {
//            $allowance = explode("GB", $plans['name'])[0];
            $allowance = 0;
        }

        $type = str_replace("]", "", explode(" [", $plans['name'])[0]);

        if ($type == "GIFTING") {
            $type = "DG";
        }

        $network = "9MOBILE";
        if ($plans['networkId'] == 1) {
            $network = "MTN";
        } elseif ($plans['networkId'] == 2) {
            $network = "AIRTEL";
        } elseif ($plans['networkId'] == 3) {
            $network = "GLO";
        }

        if($type == "DG"){
            $price=$plans['price'] * 0.98;
        }else{
            $price=$plans['price'] + 10;
        }

        $existingCombine = CombineDataPlans::where([['plan_id', $plans['planId']], ['server', 4]])->first();
        if ($existingCombine) {
            $updateCombine = ['status' => 1];
            if ($existingCombine->price != $plans['price']) {
                $updateCombine['price'] = $plans['price'];
                $updateCombine['app_price'] = $price;
                $updateCombine['res_price'] = $price;
            }
            $existingCombine->update($updateCombine);
        } else {
            CombineDataPlans::create([
                'name' => $type == "DG" ? str_replace("GIFTING", "DG", $plans['name']) : $plans['name'],
                'product_code' => $type,
                'dataplan' => $allowance,
                'network' => $network,
                'coded' => "4_" . $plans['planId'],
                'plan_id' => $plans['planId'],
                'duration' => strtolower($this->getDaysAndCategory($plans['name'])),
                'app_price' => $price,
                'res_price' => $price,
                'price' => $plans['price'],
                'server' => 4,
                'status' => 1,
            ]);
        }
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
