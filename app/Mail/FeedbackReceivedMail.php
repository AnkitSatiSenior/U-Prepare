<?php

// app/Mail/FeedbackReceivedMail.php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Feedback $feedback) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for reaching out to U-Prepare',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feedback.received',
        );
    }
}