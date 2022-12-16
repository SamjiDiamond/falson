<?php

namespace App\Mail;

use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewDeviceLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info("sending device email mailer");
        $adminE=Settings::where('name', 'transaction_email_copy')->first();
        return $this->view('mail.newdevicelogin')
            ->bcc(explode(',',$adminE->value))
            ->subject("New Device Login Code")
            ->with(['data' => $this->data]);
    }
}
