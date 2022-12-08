<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\PndL;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithdrawalPayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $with;

    public function __construct(Withdraw $with)
    {
        $this->with = $with;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sf = $this->with;

        echo "i am about to credit";

        $desc = "Being MCD withdrawal request on " . $sf->ref;

        $url = "https://api.paystack.co/transferrecipient";
        $fields = [
            "type" => "nuban",
            "name" => "mcdwithdrawal-" . $sf->ref,
            "description" => $desc,
            "account_number" => $sf->account_number,
            "bank_code" => $sf->bank_code,
            "currency" => "NGN"
        ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //execute post
        $result = curl_exec($ch);
        echo $result;

        $respt = json_decode($result, true);


        $url = "https://api.paystack.co/transfer";
        $fields = [
            "source" => "balance",
            "reason" => $desc,
            "amount" => $sf->amount * 100,
            "recipient" => $respt['data']['recipient_code']
        ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //execute post
        $result = curl_exec($ch);
        echo $result;

        $sf->status = 1;
        $sf->save();


        $input["type"] = "expenses";
        $input['date'] = Carbon::now();
        $input["gl"] = "Withdrawal Fee";
        $input["amount"] = env('WITHDRAWAL_FEE');
        $input["narration"] = "Being withdrawal payout fee on $sf->ref";

        PndL::create($input);

        $noti = new PushNotificationController();
        $noti->PushNoti($sf['user_name'], "Your withdrawal with reference $sf->ref has been paid to your bank account.", "Withdrawal Request Completed");

    }
}
