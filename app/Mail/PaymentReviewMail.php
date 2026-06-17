<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $status, public float $amount) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'تحديث حالة الدفع - وصل');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment_review');
    }

    public function attachments(): array
    {
        return [];
    }
}
