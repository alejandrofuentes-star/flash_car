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
            subject: '¡Solicitud de renta recibida! — Flash Car',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.renta_solicitada',
        );
    }
}
