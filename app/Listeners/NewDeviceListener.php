<?php

namespace App\Listeners;

use App\Events\NewDeviceEvent;
use App\Mail\NewDeviceLoginMail;
use App\Models\NewDevice;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewDeviceListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NewDeviceEvent $event
     * @return void
     */
    public function handle(NewDeviceEvent $event)
    {
        $user = $event->user;
        $datas = $event->datas;

        $tr['code'] = str_shuffle(substr(date('sydmM') . rand() . $user->user_name, 0, 4));
        $tr['email'] = $user->email;
        $tr['user_name'] = $user->user_name;
        $tr['expired'] = Carbon::now()->addMinutes(30);
        $tr['device'] = $datas['device'];
        $tr['ip'] = $datas['ip'];

        NewDevice::create($tr);

        if (env('APP_ENV') != "local") {
            Mail::to($user->email)->send(new NewDeviceLoginMail($tr));
        }
    }
}
