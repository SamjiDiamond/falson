<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataTransactionController extends Controller
{
    public function verifysmile(Request $request){
        $url=env("SERVER1_TV")."user_check".env("SERVER1_CRED")."&service=SMILE_DATA&number=".$request->number;
        $result = file_get_contents($url);

        $findme   = 'details';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $j=json_decode($result, true);
            return response()->json(['status'=> 1, 'message'=>'Validated successfully', 'data'=>$j['details']['lastName'] . " ". $j['details']['firstName']]);
        }else {
            return response()->json(['status'=> 0, 'message'=>'Error validating']);
        }

    }

    public function buysmile($price, $productcode, $network, $phone, $transid, $input){
        $url=env("SERVER1_TV")."smile_data_test".env("SERVER1_CRED")."&smartno=".$phone."&product_code=".$productcode."&price=".$price."&trans_id=".$transid;
        $result = file_get_contents($url);

        $findme   = 'service';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        $src=new ServeRequestController();
        if ($pos !== false) {
            $src->addtrans("server1", $result,$price,1,$transid,$input);
        }else {
            $src->addtrans("server1",$result,$price,0,$transid,$input);
        }

    }
}
