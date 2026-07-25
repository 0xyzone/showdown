<?php

namespace App\Mail;

use App\Models\TournamentRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TournamentRegistration $registration
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tournament Entry Submitted — Outlaw Showdown 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registrations.submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
