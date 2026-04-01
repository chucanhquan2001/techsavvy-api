<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $contactId,
        public string $contentText
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact #' . $this->contactId
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-created'
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
