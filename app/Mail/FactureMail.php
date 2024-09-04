<?php

namespace App\Mail;

use App\Models\Facture;
use App\Models\Line_items;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $facture;

    public $emailFrom;

    /**
     * Create a new message instance.
     */
    public function __construct(Facture $facture, $email)
    {
        $this->facture = $facture->load('line_items');
        $this->emailFrom = $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Facture ' . $this->facture->facture_number,
            replyTo: [
                new Address($this->emailFrom),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.facture',
            with: [
                'facture' => $this->facture,
                'replyTo' => $this->emailFrom,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
