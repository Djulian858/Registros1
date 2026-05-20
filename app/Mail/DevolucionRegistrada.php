<?php

namespace App\Mail;

use App\Models\Prestamo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevolucionRegistrada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Prestamo $prestamo,
        public string $destinatario // 'colaborador' | 'admin'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->destinatario === 'colaborador'
            ? 'Confirmacion de devolucion de equipo'
            : "Confirmacion: Equipo devuelto por {$this->prestamo->colaborador}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.devolucion-registrada',
        );
    }
}
