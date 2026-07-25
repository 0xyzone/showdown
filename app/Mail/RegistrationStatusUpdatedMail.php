<?php

namespace App\Mail;

use App\Models\TournamentRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TournamentRegistration $registration,
        public string $reasonNotes = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Status Update: '.strtoupper($this->registration->status).' — Outlaw Showdown 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registrations.status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
