<?php

namespace App\Mail;

use App\Models\Bilan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BilanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bilan;

    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct(Bilan $bilan)
    {
        $this->bilan =  $bilan ;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bilan Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.bilan',
            with: [
                'bilan' => $this->bilan
            ],
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
