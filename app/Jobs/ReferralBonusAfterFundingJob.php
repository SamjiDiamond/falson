<?php

namespace App\Jobs;

use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReferralBonusAfterFundingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $user_name;
    public $amount;

    public function __construct($user_name, $amount)
    {
        $this->user_name = $user_name;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $user = User::where("user_name", $this->user_name)->first();

        if ($user->referral != "") {
            $minFund = Settings::where('name', 'referral_bonus_min_funding')->first();
            $rfminamount = doubleval($minFund->value);

            $t = Transaction::where([['user_name', $this->user_name], ['name', 'wallet funding'], ['amount', '>=', $rfminamount]])->count();

            if ($t == 1) {
                //fund the person that referred him
                $ruser = User::where('user_name', $user->referral)->first();

                if ($ruser) {

                    $referralb = Settings::where('name', 'referral_bonus')->first();
                    $referral_bonus = $referralb->value;

                    if (str_contains($referral_bonus, '%')) {
                        $pc = ($referral_bonus / $this->amount) * 100;
                    } else {
                        $pc = $referral_bonus;
                    }

                    $tr['name'] = "ReferralBonus";
                    $tr['description'] = "PlanetF Referral Bonus on " . $this->user_name;
                    $tr['amount'] = $pc;
                    $tr['date'] = Carbon::now();
                    $tr['device_details'] = "system";
                    $tr['ip_address'] = '127.0.0.1';
                    $tr['user_name'] = $ruser->user_name;
                    $tr['ref'] = 'PLF_refbonus_' . time();
                    $tr['server'] = "";
                    $tr['server_response'] = "";
                    $tr['code'] = "mfunds";
                    $tr['status'] = "successful";
                    $tr['extra'] = "";
                    $tr['i_wallet'] = $ruser->bonus;
                    $tr['f_wallet'] = $tr['i_wallet'] + $pc;

                    Transaction::create($tr);

                    $ruser->bonus += $pc;
                    $ruser->save();

                }

            }

        }

    }
}
