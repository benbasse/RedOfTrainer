<?php

namespace App\Mail;

use App\Models\Facture;
use App\Models\Line_items;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $facture;

    /**
     * Create a new message instance.
     */
    public function __construct(Facture $facture)
    {
        $this->facture = $facture->load('line_items');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Facture ' . $this->facture->facture_number,
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
