<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Jobs\ResellerPaymentLinkJob;
use App\Jobs\ResellerVNubanJob;
use App\Models\PndL;
use App\Models\ResellerPaymentLink;
use App\Models\Serverlog;
use App\Models\VirtualAccountClient;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class PaystackHookController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $data2 = json_encode($input);

        echo "52.31.139.75, 52.49.173.169, 52.214.14.220<br/>";

        try {
            DB::table('tbl_webhook_paystack')->insert(['payment_reference' => $input['data']['reference'], 'payment_id' => $input['data']['id'], 'status' => $input['data']['status'], 'amount' => $input['data']['amount'], 'fees' => $input['data']['fees'] ?? '', 'customer_code' => isset($input['data']['customer']) ? $input['data']['customer']['customer_code'] : " ", 'email' => isset($input['data']['customer']) ? $input['data']['customer']['email'] : " ", 'paystack_signature' => $request->header('X-Paystack-Signature'), 'paid_at' => $input['data']['paidAt'], 'channel' => $input['data']['channel'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);
        } catch (Exception $e) {
            Log::info("Paystack crashed. - " . $data2);
        }

//         only a post with paystack signature header gets our attention
        if (!$request->headers->has('X-Paystack-Signature')) {
            return "invalid request";
        }

        if ($input['event'] != "charge.success") {
            return "charge->success expected";
        }
        $domain = $input['data']['domain'];
        $status = $input['data']['status'];
        $reference = $input['data']['reference'];
        $amount = $input['data']['amount'] / 100;
        $fees = $input['data']['fees'] / 100;

        if ($domain != "live") {
            return "demo env";
        }

        if ($status != "success") {
            return "Success status expected";
        }

        //RECEIVING NUBAN TRANSFER
        if ($input['event'] == "charge.success") {
            if (isset($input['data']['authorization']['channel'])) {
                //check if payment is 4rm dedicated number
                if ($input['data']['authorization']['channel'] == "dedicated_nuban") {
                    return $this->d_nuban($input);
                }
            }
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

        $fun=Wallet::where('ref',$reference)->first();
        if ($fun) {
            if ($fun->status != "completed") {
                $fun->status = 'completed';
                $fun->save();

                $at = new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Paystack", $fees);
            }
        }

        $rpl = ResellerPaymentLink::where('reseller_reference', $reference)->first();
        if ($rpl) {
            if ($rpl->status == 0) {
                $rpl->status = 1;
                $rpl->save();

                ResellerPaymentLinkJob::dispatch($input)->delay(now()->addSecond());
            }
        }

        $findme = 'mcd_agent';
        $pos = strpos($reference, $findme);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos !== false) {
            $p = PndL::where('narration', $reference)->first();
            if (!$p) {
                $input["type"] = "income";
                $input["gl"] = "Agent Registration";
                $input["amount"] = $amount;
                $input["date"]=Carbon::now();
                $input["narration"]=$reference;
                PndL::create($input);

                $input["type"] = "expenses";
                $input["amount"] = $fees;
                PndL::create($input);
            }
        }


        return "success";
    }


    public function d_nuban($input)
    {
        $acct_number = $input['data']['authorization']['receiver_bank_account_number'];

        // find account number match
        $vac = VirtualAccountClient::where('account_number', $acct_number)->first();

        if ($vac) {
            ResellerVNubanJob::dispatch($input)->delay(now()->addSecond());
            return "success";
        }

        return "success but acct not found";
    }
}
