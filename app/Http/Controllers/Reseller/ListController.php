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
                $plans = ResellerDataPlans::where([["network", "LIKE", "mtn%"], ["status", 1]])->get();
                break;
            case "a":
                $plans = ResellerDataPlans::where([["network", "LIKE", "airtel%"], ["status", 1]])->get();
                break;
            case "9":
                $plans = ResellerDataPlans::where([["network", "LIKE", "9mobile%"], ["status", 1]])->get();
                break;
            case "g":
                $plans = ResellerDataPlans::where([["network", "LIKE", "glo%"], ["status", 1]])->get();
                break;
            default:
                $plans = "";
        }

        if ($plans == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $plans->makeHidden(['price','product_code','plan_id','network','level2','level3','level4','level5'])]);
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
