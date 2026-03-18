<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\AppAirtimeControl;
use App\Models\AppDataControl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BulkController extends Controller
{

    function valUser(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required|email',
            'pin' => 'required|min:4|max:6'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

        if(!Hash::check($request->pin,$user->pin)){
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }

        return response()->json(['success' => 1, 'message' => 'Account Validated Successfully', 'balance' => $user->wallet]);
    }

    function buyairtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'email' => 'required|email',
            'pin' => 'required|min:4|max:6'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $airtime = AppAirtimeControl::where("network", $input['provider'])->first();

        if (!$airtime) {
            return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are  MTN, 9MOBILE, GLO, AIRTEL.']);
        }

        if ($airtime->status == 0) {
            return response()->json(['success' => 0, 'message' => 'Network currently not available']);
        }


        $server = $airtime->server;
        $discount = $airtime->discount;

        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 5000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #5000']);
        }

        $dis = explode("%", $discount);
        $discount = $input['amount'] * ($dis[0] / 100);

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

        if(!Hash::check($request->pin,$user->pin)){
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }


        $ref="BULK_".rand().time();
        return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref]);
    }

        function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'pin' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $user=User::where('email',$input['email'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid Account Provided']);
        }

//        if(!Hash::check($request->pin,$user->pin)){
//            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
//        }

        if ($request->get('pin') != $user->pin) {
            return response()->json(['success' => 0, 'message' => 'Incorrect Pin Supplied']);
        }

        $ref="BULK_".rand().time();
        return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref]);
    }

}
