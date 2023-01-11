<?php

namespace App\Jobs;

use App\Models\CGWallets;
use App\Models\PndL;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ReverseTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tran;
    public $initiator;

    public function __construct(Transaction $tran, $initiator)
    {
        $this->tran = $tran;
        $this->initiator = $initiator;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $tran = $this->tran;
        $initiator = $this->initiator;

        $desc = "Being reversal of " . $tran->description;
        $user_name = $tran->user_name;

        $tran->refresh();

        if ($tran->status == "reversed") {
            echo "Transaction already reversed " . $tran->ref;
            return;
        }

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
                $input["extra"] = 'Initiated by ' . $initiator;

                $user->update(["agent_commision" => $nBalance]);
                Transaction::create($input);
            } else {
                if ($tran->name == "data") {
                    $extra=explode("|",$tran->extra);

                    if(isset($extra[2])){
                        $cg=CGWallets::where([["user_id", $extra[2]], ['name', $extra[1]]])->first();

                        if(!$cg){
                            echo "Invalid payment selected encounter while reversing " . $tran->ref;
                            return;
                        }

                        $cg->balance+=doubleval($extra[0]);
                        $cg->save();
                        $nBalance = $user->wallet;
                    }else{
                        $amount = $tran->amount;
                        $nBalance = $user->wallet + $amount;
                    }

                } else {
                    $nBalance = $user->wallet + $tran->amount;
                }

                $input["description"] = "Being reversal of " . $tran->description;
                $input["name"] = "Reversal";
                $input["status"] = "successful";
                $input["code"] = "reversal";
                $input["amount"] = $amount;
                $input["user_name"] = $tran->user_name;
                $input["i_wallet"] = $user->wallet;
                $input["f_wallet"] = $nBalance;
                $input["extra"] = 'Initiated by ' . $initiator;

                $user->update(["wallet" => $nBalance]);
                Transaction::create($input);

            }
        }

        try {
            PushNotificationJob::dispatch($user_name, $desc, "Reversal")->onQueue('low');
        } catch (\Exception $e) {
            echo "error while sending notification";
        }

    }
}
