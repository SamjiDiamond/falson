<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InsufficientBalanceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contentMessage;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param string $contentMessage
     * @param string $subject
     */
    public function __construct($contentMessage, $subject = 'Insufficient Balance Notification')
    {
        $this->contentMessage = $contentMessage;
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.admin_notification',
            with: [
                'message' => $this->contentMessage,
                'url' => config('app.url'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
