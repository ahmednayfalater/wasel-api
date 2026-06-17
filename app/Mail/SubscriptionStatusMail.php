<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $status, public string $generatorType) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'تحديث حالة الاشتراك - وصل');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription_status');
    }

    public function attachments(): array
    {
        return [];
    }
}
