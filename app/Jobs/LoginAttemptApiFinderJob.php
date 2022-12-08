<?php

namespace App\Jobs;

use App\Models\LoginAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoginAttemptApiFinderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $id;
    public function __construct($id)
    {
        $this->id=$id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $la = LoginAttempt::find($this->id);

        echo "-- Running IPcheck for " . $la->user_name;

        $loc2 = file_get_contents('http://ip-api.com/json/' . $la->ip_address);
        echo $loc2;
        $obj = json_decode($loc2);

        if ($obj->status == "success") {
            $la->city = $obj->city;
            $la->region = $obj->regionName;
            $la->country = $obj->country;
            $la->provider = $obj->isp;
            $la->response = $loc2;
            $la->save();
            return;
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ipapi.co/'.$la->ip_address.'/json/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: __cfduid=d0b20fcb9ff3482fed12db99b8b64bf211606221322; csrftoken=etQQ13wP90xcf3IquiIg5Y0g4ZtGdjtJAPptc8C3QydViO1iYoW0ZFDkbtYGZH05'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;


//        $loc1 = file_get_contents('https://ipapi.co/'.$la->ip_address.'/json/');
//        echo $loc1;
        $obj = json_decode($response);

        if(!isset($obj->error)) {
            $la->city = $obj->city;
            $la->region = $obj->region;
            $la->country = $obj->country_name;
            $la->provider = $obj->org;
            $la->response = $response;
            $la->save();
            return;
        }

            $la->response = "ipapi.co: ".$response. " | ip-api.com: ".$loc2 ;
            $la->save();
    }
}
