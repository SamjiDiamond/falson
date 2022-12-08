<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\ResellerPaymentLink;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResellerPaymentLinkJob implements ShouldQueue
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
        $transactionreference = $input['data']['reference'];


        $vac = ResellerPaymentLink::where('reseller_reference', $transactionreference)->first();

        $u = User::find($vac->reseller_id);

        if (!$u) {
            echo "reseller not found";
            return;
        }

        $tcheck = Transaction::where('ref', $transactionreference)->first();

        if ($tcheck) {
            echo "transaction reference already exist";
            return;
        }

        $input['name'] = "Payment Link Funding";
        $input['amount'] = $amount;
        $input['status'] = 'successful';
        $input['description'] = 'Payment of #' . $amount . ' received via payment link.';
        $notimssg = $input['description'];
        $input['user_name'] = $u->user_name;
        $input['code'] = 'rpl';
        $input['i_wallet'] = $u->wallet;
        $wallet = $u->wallet + $amount;
        $input['f_wallet'] = $wallet;
        $input["ip_address"] = "127.0.0.1:A";
        $input["ref"] = $transactionreference;
        $input["date"] = Carbon::now();
        $input["extra"] = '';

        Transaction::create($input);


        $input["description"] = "Fee charges on reseller virtual account funding";
        $input["name"] = "Auto Charge";
        $input["code"] = "rplc";
        $input["amount"] = $provider_charges;
        $input["i_wallet"] = $wallet;
        $input["f_wallet"] = $input["i_wallet"] - $provider_charges;
        $wallet = $input["f_wallet"];

        Transaction::create($input);

        $u->wallet = $wallet;
        $u->save();

        $data['email'] = $vac->email;
        $data['reseller_reference'] = $vac->reseller_reference;
        $data['amount'] = $amount;
        $data['fees'] = $provider_charges;
        $data['ref'] = $transactionreference;
        $data['created_at'] = $vac->created_at;
        $data['paid_at'] = $vac->updated_at;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $vac->callback_url,
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
            $noti->PushNoti($input['user_name'], $notimssg, "Payment Link Funding");
        } catch (Exception $e) {
            echo "I crash because of =>" . $e;
        }
    }
}
