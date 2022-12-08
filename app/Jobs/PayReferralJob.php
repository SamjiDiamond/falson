<?php

namespace App\Jobs;

use App\Models\PndL;
use App\Models\ReferralPlans;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PayReferralJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input, $tr, $userid, $owner;

    public function __construct($input, $tr, $userid, User $owner)
    {
        $this->input = $input;
        $this->tr = $tr;
        $this->userid = $userid;
        $this->owner = $owner;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->owner->referral_plan != "free") {
            echo "initiating referal bonus on " . $this->owner->user_name;
            $this->payUpgradedUser($this->owner, $this->tr, $this->input);
        }
    }

    function payUpgradedUser(User $owner, $tr, $input)
    {
        $rp = ReferralPlans::where('name', $owner->referral_plan)->first();

        if (!$rp) {
            echo "plan not recognized";
            return 0;
        }

        $data = $rp->data_bonus;
        $airtime = $rp->airtime_bonus;
        $paytv = $rp->tv_bonus;

        $price = $tr['amount'];

        $amount = 0;

        if ($input['service'] == "airtime") {
            $am = $price * $airtime;
            $amount = round($am / 100);
        } else if ($input['service'] == "data") {
            if ($input['coded'] == "m500" || $input['coded'] == "m1" || $input['coded'] == "m2" || $input['coded'] == "m3" || $input['coded'] == "m5" || $input['coded'] == "m10") {
                $amount = $data;
            }
        } else if ($input['service'] == "tv") {
            $am = $price * $paytv;
            $amount = round($am / 100);
        } else {
            echo "service not in list";
            return 0;
        }

        if ($amount == 0) {
            echo "bonus amount to zero and can not be credited";
            return 0;
        }


        $tr['name'] = $rp->name . " Bonus";
        $tr['description'] = "Being " . $rp->name . " plan bonus on " . $tr['ref'];
        $tr['code'] = "bonus_plan_" . $rp->name;
        $tr['amount'] = $amount;
        $tr['date'] = Carbon::now();
        $tr['status'] = "successful";
        $tr['user_name'] = $owner->user_name;
        $tr['i_wallet'] = $owner->bonus;
        $tr['f_wallet'] = $tr['i_wallet'] + $amount;
        Transaction::create($tr);

        $owner->bonus = $tr['f_wallet'];
        $owner->save();

        $input["type"] = "expenses";
        $input["gl"] = "Referral Bonus";
        $input["amount"] = $amount;
        $input['date'] = Carbon::now();
        $input["narration"] = "Being " . $rp->name . " Bonus paid to " . $owner->user_name . " on " . $tr['ref'];

        PndL::create($input);

        echo "Bonus credited";
        return 0;
    }

    function payReferredUser($ruser, $input)
    {
        $rp = ReferralPlans::where('name', $ruser->referral_plan)->first();
        $ruser = User::find($ruser);

        $data = $rp->data_bonus;
        $airtime = $rp->airtime_bonus;
        $paytv = $rp->tv_bonus;

        $price = $input['amount'];


        $amount = 0;

        if ($input['service'] == "airtime") {
            $am = $price * $airtime;
            $amount = round($am / 100);
        } else if ($input['service'] == "data") {
            $amount = $data;
        } else if ($input['service'] == "tv") {
            $am = $price * $paytv;
            $amount = round($am / 100);
        }


        if ($amount > 0) {
            $tr['name'] = "Referral Bonus";
            $tr['description'] = "Being referral bonus on " . $tr['description'];
            $tr['code'] = "rc_" . $input['service'];
            $tr['amount'] = $amount;
            $tr['date'] = Carbon::now();
            $tr['status'] = "successful";
            $tr['user_name'] = $ruser->user_name;
            $tr['i_wallet'] = $ruser->bonus;
            $tr['f_wallet'] = $ruser->bonus + $amount;
            Transaction::create($tr);

            $ruser->bonus = $tr['f_wallet'];
            $ruser->save();

            $input["type"] = "expenses";
            $input["gl"] = "Referral Bonus";
            $input["amount"] = $amount;
            $input['date'] = Carbon::now();
            $input["narration"] = "Being referral bonus paid to " . $ruser->user_name . " on " . $input['ref'];

            PndL::create($input);
        }
    }
}
