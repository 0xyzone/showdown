<?php

namespace App\Mail;

use App\Models\SponsorQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SponsorQueryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SponsorQuery $query
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sponsorship Inquiry Received — Outlaw Showdown 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sponsors.query-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
