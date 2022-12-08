<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PushNotificationController extends Controller
{
    public function PushPersonal($user_name, $message, $title)
    {

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

//        echo $response;
    }

    public function PushNoti($user_name, $message, $title)
    {

        $user_name_tr = str_replace(" ", "", $user_name);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
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

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/samji\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"" . $title . "\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/videx\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"MCD Data Purchase Notification\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($uresponse, true);

        DB::table('tbl_pushnotiflog')->insert(
            ['user_name' => $user_name, 'message' => $message, 'response' => $json['message_id']]
        );

//        echo $response;
    }

    public function PushTopic($topic, $message, $title)
    {

        $topic = str_replace(" ", "", $topic);

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
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/" . $topic . "\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"" . $title . "\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $response = curl_exec($curl);

//        echo $response;
    }

    public function PushNotiAdmin($message, $title)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/samji\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"" . $title . "\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"to\": \"/topics/videx\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"MCD Data Purchase Notification\",\n\t\"body\":\"" . $message . "\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . env('PUSH_NOTIFICATION_KEY'),
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

//        echo $response;
    }

    public function vtpassSendSMS($username, $message, $title)
    {
        $user_name = User::where("user_name", $username)->first();
        $phone = $user_name->phoneno;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://messaging.vtpass.com/v2/api/sms/sendsms",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('sender' => env('SMS_SENDER'), 'recipient' => $phone, 'message' => $message, 'responsetype' => 'json'),
            CURLOPT_HTTPHEADER => array(
                "X-Token: " . env('SMS_VT_PK'),
                "X-Secret: " . env('SMS_VT_SK'),
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;

        DB::table('tbl_smslog')->insert(
            ['user_name' => $username, 'message' => $message, 'phoneno' => $phone, 'response' => $response]
        );

        return $response;
    }

    public function send_smsroute9ja($username, $message)
    {
        $user_name = User::where("user_name", $username)->first();
        $phone = $user_name->phoneno;

        $url = "https://smsroute9ja.com.ng/components/com_spc/smsapi.php?";
        $np = 'username=' . env("SMS9JA_USER") . '&password=' . env("SMS9JA_PASS") . '&sender=' . env("APP_NAME") . '&recipient=' . $phone . '&message=' . $message;
        $response = file_get_contents($url . urlencode($np));

        echo $response;

        DB::table('tbl_smslog')->insert(
            ['user_name' => $username, 'message' => $message, 'phoneno' => $phone, 'response' => $response]
        );

        return $response;
    }
}

//{
//    "responseCode": "TG00",
//    "response": "MESSAGE PROCESSED",
//    "batchId": 5463323,
//    "clientBatchId": null,
//    "sentDate": "2021-06-11 16:36:45",
//    "messages": [
//        {
//            "statusCode": "0000",
//            "recipient": "2347061933309",
//            "messageId": "1623425805438752507784251",
//            "status": "SENT",
//            "description": "MESSAGE SENT TO PROVIDER",
//            "network": "MTNNG",
//            "country": "NIGERIA\r",
//            "deliveryCode": "999",
//            "deliveryDate": "0000-00-00 00:00:00",
//            "bulkId": "1623425805465161413"
//        }
//    ]
//}
