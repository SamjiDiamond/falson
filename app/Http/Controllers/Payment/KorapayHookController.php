<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Jobs\KorapayHookJob;
use App\Models\Serverlog;
use App\Models\VirtualAccountClient;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KorapayHookController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $data2 = json_encode($input);

        DB::table('tbl_webhook_korapay')->insert(['payment_reference' => $input['data']['payment_reference'] ?? '', 'payment_id' => $input['data']['reference'], 'status' => $input['data']['status'], 'amount' => $input['data']['amount'], 'fees' => $input['data']['fee'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);


        if ($input['event'] != "charge.success") {
            return "charge->success expected";
        }

        if (isset($input['data']['virtual_bank_account_details']['virtual_bank_account']['account_reference'])) {
            $vac = VirtualAccountClient::where('account_reference', $input['data']['virtual_bank_account_details']['virtual_bank_account']['account_reference'])->first();

            if ($vac) {
                KorapayHookJob::dispatch($input)->delay(now()->addSecond());
                return "success";
            }
        }

        $status = $input['data']['status'];
        $reference = $input['data']['payment_reference'];
        $amount = int($input['data']['amount'] - $input['data']['fee']);

        if ($status != "success") {
            return "Success status expected";
        }

        $tra = Serverlog::where('transid', $reference)->first();
        if($tra){
            if ($tra->status!="completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm=new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun=Wallet::where('ref',$reference)->first();
        if($fun){
            if ($fun->status!="completed") {
                $fun->status='completed';
                $fun->save();

                $at=new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Korapay", $input['data']['fee']);
            }
        }

        return "success";
    }
}
