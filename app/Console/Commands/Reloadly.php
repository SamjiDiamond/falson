<?php

namespace App\Console\Commands;

use App\Models\AirtimeCountry;
use Illuminate\Console\Command;

class Reloadly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:reloadly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get things started on reloadly';

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
        $this->info("Fetching data from reloadly");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://topups.reloadly.com/countries',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('SERVER8_AUTH'),
                'Accept: application/com.reloadly.topups-v1+json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;

        $reps = json_decode($response, true);

        foreach ($reps as $rep) {
            AirtimeCountry::create([
                "isoName" => $rep['isoName'],
                "name" => $rep['name'],
                "currencyCode" => $rep['currencyCode'],
                "currencyName" => $rep['currencyName'],
                "flag" => $rep['flag'],
                "callingCodes" => json_encode($rep['callingCodes']),
            ]);
        }

        $this->info("Done inserting data from reloadly");

    }
}
