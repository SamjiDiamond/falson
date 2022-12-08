<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveawayNotificationJob implements ShouldQueue
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

        echo "Initializing notification";

        try {
            $message = substr($input['user_name'], 0, 3) . " just created Giveaway of " . $input['type'] . " #" . $input['amount'] . " come and claim it now.";
            $title = $input['type'] . " Giveaway 🎉 !!";

            echo "Sending notification to giveaway subscribers";
            $u = new PushNotificationController();
            $u->PushTopic('giveaway', $message, $title);
        } catch (Exception $e) {
            echo $e;
        }
    }
}
