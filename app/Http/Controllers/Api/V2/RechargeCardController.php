<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PndL;
use App\Models\RCPricing;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RechargeCardController extends Controller
{
    public function rcplans()
    {
        $data = RCPricing::get();

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => $data, 'current' => Auth::user()->rc_price_plan_id]);
    }

    public function rcpurchase(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $pricing = RCPricing::where([['id', $input['id']], ['status', 1]])->first();

        if (!$pricing) {
            return response()->json(['success' => 0, 'message' => 'Invalid Plan ID supplied or currently disabled']);
        }

        $user = Auth::user();

        if ($user->rc_price_plan_id == $input['id']) {
            return response()->json(['success' => 0, 'message' => 'You are already on this plan. Kindly choose another plan']);
        }

        if ($user->wallet < $pricing->amount) {
            return response()->json(['success' => 0, 'message' => 'Insufficient balance']);
        }

        $input['name'] = "RechargeCard Upgrade";
        $input['amount'] = $pricing->amount;
        $input['status'] = 'successful';
        $input['description'] = "Being fee charged for RechargeCard upgrade";
        $input['user_name'] = $user->user_name;
        $input['code'] = 'rc_setup';
        $input['i_wallet'] = $user->wallet;
        $input['f_wallet'] = $input['i_wallet'] - $pricing->amount;
        $input["ip_address"] = "127.0.0.1:A";
        $input["date"] = date("y-m-d H:i:s");
        $input["extra"] = $pricing->plan . "|" . $pricing->id;

        $t = Transaction::create($input);

        $inputa["type"] = "income";
        $inputa["gl"] = "rechargecard_upgrade";
        $inputa["amount"] = $pricing->amount;
        $inputa["narration"] = "Being amount charged for rechargecard upgrade from " . $user->user_name;
        $inputa['date'] = Carbon::now();

        PndL::create($inputa);

        $user->rc_price_plan_id = $pricing->id;
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Payment successful', 'data' => $t, 'current' => $pricing->id]);
    }
}
