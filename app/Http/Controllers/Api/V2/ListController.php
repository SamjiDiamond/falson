<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Airtime2CashSettings;
use App\Models\AirtimeCountry;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\AppEducationControl;
use App\Models\AppElectricityControl;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerBetting;
use App\Models\ResellerCableTV;
use App\Models\ResellerElecticity;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

class ListController extends Controller
{
    public function airtime()
    {
        //get airtime discounts
        $airsets = AppAirtimeControl::get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function airtimeInt()
    {
        $airsets = AirtimeCountry::where('status', 1)->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function airtimeCountry($country)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('MCD_BASEURL') . '/foreign_airtime/' . $country,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . env('MCD_KEY')
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);

            Log::info("MCD Foreign Airtime. - " . $country);
            Log::info($response);

            $rep = json_decode($response, true);

            try {
                return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $rep['data']]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Unable to validate'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => 0, 'message' => 'Unable to fetch at this time']);
        }

    }

    public function electricity()
    {
        $airsets = AppElectricityControl::get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function education()
    {
        $airsets = AppEducationControl::where('status', 1)->select('id', 'name', 'price', 'code', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function betting()
    {
        $airsets = ResellerBetting::where('status', 1)->select('id', 'name', 'code', 'discount', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $airsets]);
    }

    public function data($network)
    {

        $datasets = AppDataControl::where([['network', '=', strtoupper($network)], ['status', 1]])->select('name', 'coded', 'pricing as price', 'network', 'status')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

    public function cabletv($network)
    {


        $sett = Settings::where('name', 'tv_server')->first();

        if ($sett->value == "RINGO" || $sett->value == "2") {
            if (strtolower($network) == "startimes") {
                $server = 1;
            } else {
                $server = "2";
            }
        } else {
            $server = $sett->value;
        }

        if (strtolower($network) == "showmax") {
            $server = 7;
        }

        $datasets = AppCableTVControl::where([['type', '=', strtolower($network)], ['status', 1], ['server', $server]])->select('name', 'coded', 'price', 'type', 'status','discount')->get();

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

    public function availableCommissions()
    {
        $air = AppAirtimeControl::select('network', 'discount')->get();
        $elec = AppElectricityControl::select('name', 'discount')->get();
        $a2c = Airtime2CashSettings::select('network', 'discount')->get();
        $gotv = AppCableTVControl::select('type', 'discount')->where('type', 'gotv')->first();
        $dstv = AppCableTVControl::select('type', 'discount')->where('type', 'dstv')->first();
        $start = AppCableTVControl::select('type', 'discount')->where('type', 'startimes')->first();

        $r_air = ResellerAirtimeControl::select('network', 'level1')->get();
        $r_elec = ResellerElecticity::select('name', 'discount')->get();
        $r_gotv = ResellerCableTV::select('type', 'level1')->where('type', 'gotv')->first();
        $r_dstv = ResellerCableTV::select('type', 'level1')->where('type', 'dstv')->first();
        $r_start = ResellerCableTV::select('type', 'level1')->where('type', 'startimes')->first();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => ['airtime' => $air, 'electricity' => $elec, 'airtime2cash' => $a2c, 'cabletv' => [$gotv, $dstv, $start]], 'reseller' => ['airtime' => $r_air, 'electricity' => $r_elec, 'cabletv' => [$r_gotv, $r_dstv, $r_start]]]);
    }
}
