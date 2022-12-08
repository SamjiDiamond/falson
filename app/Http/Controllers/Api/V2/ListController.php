<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Airtime2CashSettings;
use App\Models\AirtimeCountry;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;

class ListController extends Controller
{
    public function airtime()
    {
        //get airtime discounts
        $airsets = AppAirtimeControl::where('name', '=', 'discount')->first();

        $uinfo['discount_mtn'] = $airsets->mtn;
        $uinfo['discount_glo'] = $airsets->glo;
        $uinfo['discount_etisalat'] = $airsets->etisalat;
        $uinfo['discount_airtel'] = $airsets->airtel;

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $uinfo]);
    }

    public function airtimeInt()
    {
        $airsets = AirtimeCountry::get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function data($network)
    {

        $datasets = AppDataControl::where([['network', '=', strtoupper($network)], ['status', 1]])->select('name', 'coded', 'pricing as price', 'network', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

    public function cabletv($network)
    {

        $datasets = AppCableTVControl::where([['type', '=', strtolower($network)], ['status', 1]])->select('name', 'coded', 'price', 'type', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

    public function jamb()
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "service-variations?serviceID=jamb",
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

            curl_close($curl);

        } else {
            $response = '{ "response_description": "000", "content": { "ServiceName": "Jamb", "serviceID": "jamb", "convinience_fee": "0 %", "varations": [ { "variation_code": "utme", "name": "UTME", "variation_amount": "4700.00", "fixedPrice": "Yes" }, { "variation_code": "de", "name": "Direct Entry (DE)", "variation_amount": "4700.00", "fixedPrice": "Yes" } ] } }';
        }

        $rep = json_decode($response, true);


        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $rep['content']['varations']]);
    }

    public function airtimeConverter()
    {
        $airsets = Airtime2CashSettings::where('status', 1)->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }
}
