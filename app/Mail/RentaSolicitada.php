<?php

namespace App\Mail;

use App\Models\Renta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentaSolicitada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Renta $renta) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu solicitud de renta fue recibida - Flash Car',
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('flashcar@rentadeautos.site', 'Flash Car'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.renta_solicitada',
            text: 'emails.renta_solicitada_text',
        );
    }
}
