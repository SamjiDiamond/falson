<?php

namespace App\Jobs;

use App\Mail\ExamTokenMail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEducationtoEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $jobi;

    public function __construct($jobi)
    {
        $this->jobi = $jobi;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->jobi;

        $tran = Transaction::where('ref', $input['transid'])->first();
        if (!$tran) {
            echo "No transaction found";
            return;
        }

        $u = User::where("user_name", $tran->user_name)->first();
        $input['email'] = $u->email;

//        if (env('APP_ENV') != "local") {
        Mail::to($u->email)->send(new ExamTokenMail($input));
//        }

        $tran->status = "delivered";
        $tran->server_response = $input['server_response'];
        $tran->save();

    }
}
