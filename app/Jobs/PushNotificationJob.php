<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user_name, $message, $title;
    public function __construct($user_name, $message, $title)
    {
        $this->user_name=$user_name;
        $this->message=$message;
        $this->title=$title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_name=$this->user_name;
        $message=$this->message;
        $title=$this->title;

        $user_name_tr = str_replace(" ", "", $user_name);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/" . $user_name_tr . "\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"" . $title . "\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $uresponse = curl_exec($curl);

        $json = json_decode($uresponse, true);

        DB::table('tbl_pushnotiflog')->insert(
            ['user_name' => $user_name, 'message' => $message, 'response' => $json['message_id']]
        );

    }
}
