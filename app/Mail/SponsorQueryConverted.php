<?php

namespace App\Mail;

use App\Models\SponsorQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SponsorQueryConverted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SponsorQuery $query,
        public string $type,
        public string $levelOrTitle
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Outlaw Showdown 2026 as '.$this->type.'!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sponsors.query-converted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
