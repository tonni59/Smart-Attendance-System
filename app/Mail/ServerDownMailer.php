<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServerDownMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $randomUUID;
    public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($randomUUID)
    {
        $this->randomUUID = $randomUUID;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Server Down for Maintenance',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $this->url = url('/' . $this->randomUUID);
        return new Content(
            markdown: 'emails.user.server-down',
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
