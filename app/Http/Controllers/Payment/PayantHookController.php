<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Serverlog;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayantHookController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $data2 = json_encode($input);

        $items = $input['data']['items'][0];

        DB::table('tbl_webhook_payant')->insert(['payment_reference' => $items['item'], 'reference_code' => $input['data']['reference_code'], 'status' => $input['data']['invoice_status'], 'amount' => $items['unit_cost'], 'description' => $items['description'], 'fees' => $input['data']['fees'], 'customer_id' => $input['data']['client_id'], 'email' => $input['data']['client']['email'], 'paid_at' => $input['data']['updated_at'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);


        $status = $input['data']['invoice_status'];
        $reference = $items['item'];
        $amount = $items['unit_cost'];
        $fee = $input['data']['fees'];

        if ($status != "paid") {
            return "Paid status expected";
        }

        $tra = Serverlog::where('transid', $reference)->first();
        if ($tra) {
            if ($tra->status != "completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm = new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun = Wallet::where('ref', $reference)->first();
        if ($fun) {
            if ($fun->status != "completed") {
                $fun->status = 'completed';
                $fun->save();

                $at = new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Payant", $fee);
            }
        }

        return "success";
    }

    public function verify(Request $request)
    {

        $input = $request->all();

        $data2 = json_encode($input);

        DB::table('test')->insert(['name' => 'webhook', 'request' => $request, 'data2' => $data2]);

        if ($input['type'] === 'subscribe' && $input['verify_token'] === env("PAYANT_VERIFY_TOKEN")) {
            return $input['challenge'];
        } else {
            return "Token validation failed. Make sure the validation tokens match.";
        }
    }
}
