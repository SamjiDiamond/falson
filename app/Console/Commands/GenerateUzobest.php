<?php

namespace App\Console\Commands;

use App\Models\CombineDataPlans;
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
        $this->info("Setting status to 0 for Combined Data plans table");

        if ($type == "all") {
            $this->info("Setting status to 0 for All Data plans table for s5");
            CombineDataPlans::where("server", 5)->update(['status' => 0]);
        } else {
            $this->info("Setting status to 0 for $type Data plans table for s5");
            CombineDataPlans::where([["server", 5], ["network", strtoupper($type)]])->update(['status' => 0]);
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

            $existingCombine = CombineDataPlans::where([['plan_id', $plans['dataplan_id']], ['server', 5]])->first();

            if($type == "DG"){
                $price=$plans['plan_amount'] * 0.98;
            }else{
                $price=$plans['plan_amount'] + 10;
            }

            if ($existingCombine) {
                $updateCombine = ['status' => 1];
                if ($existingCombine->price != $plans['plan_amount']) {
                    $updateCombine['price'] = $plans['plan_amount'];
                    $updateCombine['app_price'] = $price;
                    $updateCombine['res_price'] = $price;
                }
                $existingCombine->update($updateCombine);
            } else {
                CombineDataPlans::create([
                    'name' => $plans['plan'],
                    'product_code' => $type,
                    'dataplan' => $allowance,
                    'network' => $plans['plan_network'],
                    'coded' => "5_" . $plans['dataplan_id'],
                    'plan_id' => $plans['dataplan_id'],
                    'duration' => strtolower($this->getDaysAndCategory($plans['month_validate'])),
                    'app_price' => $price,
                    'res_price' => $price,
                    'price' => $plans['plan_amount'],
                    'server' => 5,
                    'status' => 1,
                ]);
            }
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
