<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\CGWallets;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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


    public function uniqueId(): string
    {
        $ref = $this->tran->ref ?? '';
        $userName = $this->tran->user_name ?? '';

        return $userName.'|'.$ref;
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

        try {
            $this->processReversal($tran, $initiator);
            Log::alert("ReverseTransactionJob: Processed");
        } catch (\Throwable $e) {
            Log::alert("Error on ReverseTransactionJob: ".$e);
        }
    }

    private function processReversal($tran, $initiator): void
    {
        $tran->refresh();

        $userName = $tran->user_name;
        $ref = $tran->ref;

        DB::beginTransaction();
        try {
            $rows = Transaction::where('ref', $ref)
                ->where('user_name', $userName)
                ->whereBetween('created_at', [
                    Carbon::now()->subMonths(3)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ])
                ->lockForUpdate()
                ->limit(2)
                ->get(['id', 'ref', 'created_at', 'status', 'description', 'user_name', 'amount', 'device_details', 'code', 'name']);

            if ($rows->isEmpty()) {
                Log::warning('ReverseTransactionJob: Transaction not found or is more than', ['user_name' => $userName, 'ref' => $ref]);
                DB::rollBack();

                return;
            }

            if ($rows->every(fn ($t) => $t->status === 'reversed')) {
                DB::rollBack();

                return;
            }

            $user = User::where('user_name', $userName)->lockForUpdate()->first();
            if (! $user) {
                DB::rollBack();
                Log::warning('ReverseTransactionJob: user not found', ['user_name' => $userName, 'ref' => $ref]);

                return;
            }

            Log::info('ReverseTransactionJob: processing', ['ref' => $ref, 'count' => $rows->count(), 'initiator' => $initiator]);

            foreach ($rows as $row) {
                if ($row->status !== 'reversed') {
                    $row->status = 'reversed';
                    $row->save();
                }

                $reversalRef = 'reversal_'.$row->ref.'_'.$row->id;
                $already = Transaction::where('ref', $reversalRef)->where('user_name', $row->user_name)->where('code', 'reversal')->exists();
                if ($already) {
                    continue;
                }

                $input = [];
                $amount = (float) $row->amount;

                $input['ref'] = $reversalRef;

                if ($row->code == 'tcommission') {
                    $nBalance = $user->agent_commision - $amount;

                    $input['description'] = 'Being reversal of '.$row->description;
                    $input['name'] = 'Reversal';
                    $input['status'] = 'successful';
                    $input['code'] = 'reversal';
                    $input['amount'] = $amount;
                    $input['user_name'] = $row->user_name;
                    $input['i_wallet'] = $user->agent_commision;
                    $input['f_wallet'] = $nBalance;
                    $input['extra'] = 'Initiated by '.$initiator;

                    $user->agent_commision = $nBalance;
                    $user->save();
                    Transaction::create($input);
                } else {
                    $fee = 0;
                    $nBalance = $user->wallet;

                    if ($tran->name == "data") {
                        $extra = explode("|", $tran->extra);

                        if (isset($extra[2])) {
                            $cg = CGWallets::where([["user_id", $extra[2]], ['name', $extra[1]]])->first();

                            if (!$cg) {
                                echo "Invalid payment selected encounter while reversing " . $tran->ref;
                                return;
                            }

                            $cg->balance += doubleval($extra[0]);
                            $cg->save();
                        } else {
                            $refundAmount = $amount + $fee;
                            $nBalance = $user->wallet + $refundAmount;
                        }
                    }else{
                        $refundAmount = $amount + $fee;
                        $nBalance = $user->wallet + $refundAmount;
                    }

                    $input['description'] = 'Being reversal of '.$row->description;
                    $input['name'] = 'Reversal';
                    $input['status'] = 'successful';
                    $input['code'] = 'reversal';
                    $input['amount'] = $refundAmount;
                    $input['user_name'] = $row->user_name;
                    $input['i_wallet'] = $user->wallet;
                    $input['f_wallet'] = $nBalance;
                    $input['extra'] = 'Initiated by '.$initiator;

                    $user->wallet = $nBalance;
                    $user->save();
                    Transaction::create($input);
                }
            }

            DB::commit();

            try {
                $at = new PushNotificationController();
                $at->PushNoti($userName, "A reversal of your transaction has been initiated", "Reversal");
            } catch (\Exception $e) {
                echo "error while sending notification";
                Log::info("ReverseTransactionJob:processReversal PushNotification".$e);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info("ReverseTransactionJob:processReversal ".$e);
            throw $e;
        }
    }
}
