<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $message, public string $type) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'إشعار جديد - وصل');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.general_notification');
    }

    public function attachments(): array
    {
        return [];
    }
}
