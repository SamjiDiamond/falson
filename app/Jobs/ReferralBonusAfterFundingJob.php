<?php

namespace App\Jobs;

use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\UserNotification;
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

            $t = Transaction::where([['user_name', $user->referral], ['name', 'wallet funding'], ['amount', '>=', $rfminamount]])->count();

            if ($t >= 1) {
                //fund the person that referred him
                $ruser = User::where('user_name', $user->referral)->first();

                if ($ruser) {

                    //check if the user has been credited before for the user referred
                    $rbc = Transaction::where([['extra', "Referral Bonus|" . $this->user_name], ['user_name', $ruser->user_name]])->count();

                    if ($rbc == 0) {
                        $referralb = Settings::where('name', 'referral_bonus')->first();
                        $referral_bonus = $referralb->value;

                        if (str_contains($referral_bonus, '%')) {
                            $pc = ($referral_bonus / $this->amount) * 100;
                        } else {
                            $pc = $referral_bonus;
                        }

                        $tr['name'] = "Bonus";
                        $tr['description'] = "PlanetF Referral Bonus on " . $this->user_name;
                        $tr['amount'] = $pc;
                        $tr['date'] = Carbon::now();
                        $tr['device_details'] = "system";
                        $tr['ip_address'] = '127.0.0.1';
                        $tr['user_name'] = $ruser->user_name;
                        $tr['ref'] = 'PLF_refbonus_' . time();
                        $tr['server'] = "";
                        $tr['server_response'] = "";
                        $tr['code'] = "rbonus";
                        $tr['status'] = "successful";
                        $tr['extra'] = "Referral Bonus|" . $this->user_name;
                        $tr['i_wallet'] = $ruser->bonus;
                        $tr['f_wallet'] = $tr['i_wallet'] + $pc;

                        Transaction::create($tr);

                        $ruser->bonus += $pc;
                        $ruser->save();

                        $ruser->notify(new UserNotification($tr['description'] . " has been credited to your wallet", "Referral Bonus"));
                    }

                }

            }

        }

    }
}
