<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerControl;
use App\Models\ResellerDataPlans;
use App\Models\ResellerElecticity;
use Illuminate\Http\Request;

class ListController extends Controller
{

    public function all()
    {
        $st = ResellerControl::get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }


    public function airtime()
    {
        $st = ResellerAirtimeControl::get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

    public function data(Request $request)
    {
        $input = $request->all();

        if (!isset($input['coded'])) {
            return response()->json(['success' => 0, 'message' => 'Coded not supplied']);
        }

        switch (strtolower($input['coded'])) {
            case "m":
                $plans = ResellerDataPlans::where("type", "mtn-data")->get();
                break;
            case "a":
                $plans = ResellerDataPlans::where("type", "airtel-data")->get();
                break;
            case "9":
                $plans = ResellerDataPlans::where("type", "etisalat-data")->get();
                break;
            case "g":
                $plans = ResellerDataPlans::where("type", "glo-data")->get();
                break;
            default:
                $plans = "";
        }

        if ($plans == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $plans->makeHidden(['price'])]);
    }

    public function electricity()
    {
        $st = ResellerElecticity::get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

    public function tv()
    {
        $st = ResellerCableTV::get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

}
