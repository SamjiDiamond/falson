<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutosyncngWebhookController extends Controller
{
    public function index(Request $request)
    {

//        {
//            "hash": "e050ac4b18449566ab06aa2f2af37cca4f252e8334c8be3b5a7524bb757ffba9",
//    "transaction": {
//            "id": 883368,
//        "reference": "95e813f7-4e33-4363-8481-446e4109473f",
//        "user_id": 4,
//        "amount": "50.00",
//        "discount": "0.00",
//        "convenience_fee": "50.00",
//        "balance_before": "0.00",
//        "balance_after": "50.00",
//        "payment_method": "wallet",
//        "details": "An AutoSyncNG test payment",
//        "status": "completed",
//        "type": "qr payment",
//        "transact_type": "credit",
//        "remarks": null,
//        "paid_by": "95e81779-6d6f-4b8b-95bd-557b0d9f84fc",
//        "request_ref": null,
//        "created_at": "2022-03-25T20:26:39.000000Z",
//        "updated_at": "2022-03-25T20:36:27.000000Z",
//        "actual_amount": 50,
//        "full_actual_amount": 100,
//        "is_e_pin": false
//    }
//}
        $input = $request->all();

        DB::table('tbl_webhook_hw')->insert(['code' => $input['hash'], 'message' => $input['transaction']['details'], 'reference' => $input['transaction']['request_ref'], 'type' => $input['transaction']['type'], 'ip' => $_SERVER['REMOTE_ADDR'], 'extra' => json_encode($input)]);


        $rules = array(
            'hash' => 'required',
            'transaction' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['message' => 'Payload not ok'], 400);
        }

        $tran = Transaction::where(['ref' => $input['transaction']['request_ref']])->first();

        if (!$tran) {
            return response()->json(['message' => 'Trans not found'], 404);
        }

        if ($tran->status == "reversed") {
            return response()->json(['message' => 'ok'], 202);
        }

        if ($input['transaction']['status'] == "completed" || $input['transaction']['status'] == "successful") {
            $tran->status = "delivered";
            $tran->save();
        }

        if ($input['transaction']['status'] == "failed") {
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
                    $input["ref"] = "refund_" . $tran->ref;
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
