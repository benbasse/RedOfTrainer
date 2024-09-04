<?php

namespace App\Mail;

use App\Models\Devis;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevisMail extends Mailable
{
    use Queueable, SerializesModels;

    public $devis;

    public $emailFrom;


    /**
     * Create a new message instance.
     */
    public function __construct(Devis $devis, $email)
    {
        $this->devis = $devis->load('devis_line_items');
        $this->emailFrom = $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devis ' . $this->devis->devis_number,
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
            view: 'Mail.devis',
            with: [
                'devis' => $this->devis,
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
