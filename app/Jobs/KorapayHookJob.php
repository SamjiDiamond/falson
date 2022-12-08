<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\PndL;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class KorapayHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->input;

        $account_reference = $input['data']['virtual_bank_account_details']['virtual_bank_account']['account_reference'];
        $amount = $input['data']['amount'];
        $provider_charges = $input['data']['fee'];
        $transactionreference = $input['data']['reference'];
        $acct_number = $input['data']['virtual_bank_account_details']['virtual_bank_account']['account_number'];
        $from_acct_number = $input['data']['virtual_bank_account_details']['payer_bank_account']['account_number'];
        $from_acct_name = $input['data']['virtual_bank_account_details']['payer_bank_account']['account_number'];

        $vac = VirtualAccountClient::where('account_reference', $account_reference)->first();

        $charges = env('RESELLER_VACCT_CHARGES', 40);

        $u = User::find($vac->reseller_id);

        if (!$u) {
            echo "reseller not found";
            return;
        }
        $input['name'] = "Virtual Account Funding";
        $input['amount'] = $amount;
        $input['status'] = 'successful';
        $input['description'] = 'Payment of #' . $amount . ' received on ' . $acct_number . ' from ' . $from_acct_number . ' (' . $from_acct_name . ')';
        $notimssg = $input['description'];
        $input['user_name'] = $u->user_name;
        $input['code'] = 'rva';
        $input['i_wallet'] = $u->wallet;
        $wallet = $u->wallet + $amount;
        $input['f_wallet'] = $wallet;
        $input["ip_address"] = "127.0.0.1:A";
        $input["ref"] = $transactionreference;
        $input["date"] = Carbon::now();

        Transaction::create($input);

        $pl["type"] = "income";
        $pl["gl"] = "Reseller Virtual Account";
        $pl["amount"] = $charges;
        $pl['status'] = 'successful';
        $pl["narration"] = "Fee charges on " . $account_reference . " reseller virtual account funding from " . $u->user_name;

        PndL::create($pl);

        $pl["type"] = "expenses";
        $pl["amount"] = $provider_charges;
        $pl["narration"] = "Provider " . $pl["narration"];
        PndL::create($pl);

        $input["description"] = "Fee charges on reseller virtual account funding";
        $input["name"] = "Auto Charge";
        $input["code"] = "rvac";
        $input["i_wallet"] = $wallet;
        $input["f_wallet"] = $input["i_wallet"] - $charges;
        $wallet = $input["f_wallet"];

        Transaction::create($input);

        $u->wallet = $wallet;
        $u->save();


        $noti = new PushNotificationController();
        $noti->PushNoti($input['user_name'], $notimssg, "Virtual Account Funding");
    }
}
