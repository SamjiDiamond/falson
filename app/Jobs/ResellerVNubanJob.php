<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\PndL;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResellerVNubanJob implements ShouldQueue
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

        $amount = $input['data']['amount'] / 100;
        $provider_charges = $input['data']['fees'] / 100;
        $from_acct_name = $input['data']['authorization']['sender_name'];
        $sender_bank = $input['data']['authorization']['sender_bank'];
        $from_acct_number = $input['data']['authorization']['sender_bank_account_number'];
        $narration = $input['data']['authorization']['narration'];
        $receiver_bank = $input['data']['authorization']['receiver_bank'];
        $acct_number = $input['data']['authorization']['receiver_bank_account_number'];
        $transactionreference = $input['data']['reference'];


        $vac = VirtualAccountClient::where('account_number', $acct_number)->first();

        $charges = (env('RESELLER_VACCT_CHARGES', 1) / 100) * $amount;

        $u = User::find($vac->reseller_id);

        if (!$u) {
            echo "reseller not found";
            return;
        }

        echo $u->user_name;

        $tcheck = Transaction::where('ref', $transactionreference)->first();

        if ($tcheck) {
            echo "transaction reference already exist";
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
        $input["device_details"] = $acct_number;
        $input["extra"] = '';

        Transaction::create($input);

        $pl["type"] = "income";
        $pl["gl"] = "Reseller Virtual Account";
        $pl["amount"] = $charges;
        $pl['status'] = 'successful';
        $pl["date"] = Carbon::now();
        $pl["narration"] = "Fee charges on " . $transactionreference . " reseller virtual account funding from " . $u->user_name;

        PndL::create($pl);

        $pl["type"] = "expenses";
        $pl["amount"] = $provider_charges;
        $pl["narration"] = "Provider " . $pl["narration"];
        PndL::create($pl);

        $input["amount"] = $charges;
        $input["description"] = "Fee charges on reseller virtual account funding";
        $input["name"] = "Auto Charge";
        $input["code"] = "rvac";
        $input["i_wallet"] = $wallet;
        $input["f_wallet"] = $input["i_wallet"] - $charges;
        $wallet = $input["f_wallet"];

        Transaction::create($input);

        $u->wallet = $wallet;
        $u->save();

        $data['account_number'] = $acct_number;
        $data['account_reference'] = $vac->account_reference;
        $data['amount'] = $amount;
        $data['fees'] = $charges;
        $data['narration'] = $narration;
        $data['ref'] = $transactionreference;
        $data['from_account_name'] = $from_acct_name;
        $data['from_account_number'] = $from_acct_number;
        $data['paid_at'] = $input["date"];

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $vac->webhook_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            echo $response;

            $noti = new PushNotificationController();
            $noti->PushNoti($input['user_name'], $notimssg, "Virtual Account Funding");
        } catch (Exception $e) {
            echo "I crash because of =>" . $e;
        }
    }
}


//{
//    ...
//    "authorization": {
//    "authorization_code": "AUTH_0ozsafcpdf",
//    "bin": "413XXX",
//    "last4": "X011",
//    "exp_month": "01",
//    "exp_year": "2020",
//    "channel": "dedicated_nuban",
//    "card_type": "transfer",
//    "bank": "First City Monument Bank",
//    "country_code": "NG",
//    "brand": "Managed Account",
//    "reusable": false,
//    "signature": null,
//    "sender_bank": "First City Monument Bank",
//    "sender_bank_account_number": "XXXXXX0011",
//    "sender_country": "NG",
//    "sender_name": "RANDALL AKANBI HORTON",
//    "narration": "NIP: RHODA CHURCH -1123344343/44343231232",
//    "receiver_bank_account_number": "9930000902",
//    "receiver_bank": "Wema Bank"
//  },
//   ...
// }
