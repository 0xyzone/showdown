<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MatchScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public mixed $series,
        public string $lobbyDetails = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Upcoming Match Schedule Alert — Outlaw Showdown 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.matches.scheduled',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
