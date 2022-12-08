<?php

namespace App\Jobs;

use App\Models\Airtime2Cash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Airtime2CashNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $ref;

    public function __construct($ref)
    {
        $this->ref = $ref;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ref = $this->ref;

        $ac = Airtime2Cash::where('ref', $ref)->select(['ref', 'network', 'amount', 'phoneno', 'status', 'receiver', 'created_at', 'updated_at', 'webhook_url'])->first();

        $data = json_encode($ac);

        echo $data;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ac->webhook_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;
    }
}
