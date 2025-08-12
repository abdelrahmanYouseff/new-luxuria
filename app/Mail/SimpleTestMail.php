<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SimpleTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;

    public function __construct($userName = null, $userEmail = null)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Simple Test Email - Luxuria UAE',
            from: config('mail.from.address', 'noreply@rentluxuria.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.simple-test',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
