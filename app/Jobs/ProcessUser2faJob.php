<?php

namespace App\Jobs;

use App\Mail\TwofaNotificationMail;
use App\Models\CodeRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessUser2faJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $user;
    public $type;
    public $datas;

    public function __construct(User $user, $type, $datas)
    {
        $this->user = $user;
        $this->type = $type;
        $this->datas = $datas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $code = substr(rand(), 0, 6);

        CodeRequest::create([
            'mobile' => trim($this->user->email),
            'code' => $code,
            'status' => 0,
            'type' => $this->type
        ]);


        $data = $this->datas;
        $data['user_name'] = $this->user->user_name;
        $data['email'] = $this->user->email;
        $data['code'] = $code;


        if (env('APP_ENV') != "local") {
            Log::info("sending device email");
            Mail::to($this->user->email)->send(new TwofaNotificationMail($data));
        }
    }
}
