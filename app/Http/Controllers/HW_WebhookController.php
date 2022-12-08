<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HW_WebhookController extends Controller
{
    public function index(Request $request){
//        {
//            "code": 300,
//  "message": "You are not sending to valid MTN number.",
//  "reference": "165273963",
//  "type": "airtime"
//}
        $input=$request->all();

        DB::table('tbl_webhook_hw')->insert(['code'=> $input['code'], 'message'=> $input['message'], 'reference'=>$input['reference'], 'type'=> $input['type'], 'ip'=>$_SERVER['REMOTE_ADDR'], 'extra'=> json_encode($input)]);


        $rules = array(
            'reference' => 'required',
            'message' => 'required',
            'code' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if(!$validator->passes()) {
            return response()->json(['message' => 'ok'], 400);
        }

        $tran=Transaction::where(['server_ref'=>$input['reference']])->first();

        if(!$tran){
            return response()->json(['message' => 'ok'], 404);
        }

        if($tran->status == "reversed") {
            return response()->json(['message' => 'ok'], 202);
        }

        if($input['code'] == 200){
            $tran->status = "delivered";
            $tran->save();
        }

        if($input['code'] == 300){
            $desc = "Being reversal of " . $tran->description;
            $user_name = $tran->user_name;

            $rtran = Transaction::where('ref', '=', $tran->ref)->get();

            foreach ($rtran as $tran) {
                $tran->status = "reversed";
                $tran->save();

                $amount = $tran->amount;

                $user = User::where("user_name", "=", $tran->user_name)->first();

                if ($tran->code == "tcommission") {
                    $nBalance = $user->agent_commision - $tran->amount;

                    $input["description"] = "Being reversal of " . $tran->description;
                    $input["name"] = "Reversal";
                    $input["status"] = "successful";
                    $input["code"] = "reversal";
                    $input["amount"] = $amount;
                    $input["user_name"] = $tran->user_name;
                    $input["i_wallet"] = $user->agent_commision;
                    $input["f_wallet"] = $nBalance;
                    $input["extra"] = 'Initiated by webhook';

                    $user->update(["agent_commision" => $nBalance]);
                    Transaction::create($input);
                } else {
                    $nBalance = $user->wallet + $tran->amount;

                    $input["description"] = "Being reversal of " . $tran->description;
                    $input["name"] = "Reversal";
                    $input["status"] = "successful";
                    $input["code"] = "reversal";
                    $input["amount"] = $amount;
                    $input["user_name"] = $tran->user_name;
                    $input["i_wallet"] = $user->wallet;
                    $input["f_wallet"] = $nBalance;
                    $input["ref"] = "refund_".$tran->ref;
                    $input["extra"] = 'Initiated by webhook';
                    $input["server_response"] = $input['message'];

                    $user->update(["wallet" => $nBalance]);
                    Transaction::create($input);
                }
            }

            try {
                $at = new PushNotificationController();
                $at->PushNoti($user_name, $desc, "Reversal");
            } catch (\Exception $e) {
                echo "error while sending notification";
            }
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
