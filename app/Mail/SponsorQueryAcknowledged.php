<?php

namespace App\Mail;

use App\Models\SponsorQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SponsorQueryAcknowledged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SponsorQuery $query,
        public string $customMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Follow-up regarding your Sponsorship Inquiry — Outlaw Showdown 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sponsors.query-acknowledged',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
