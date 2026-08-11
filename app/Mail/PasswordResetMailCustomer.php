<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMailCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;  // Nueva variable para el correo

    /**
     * Inyecta las dependencias requeridas por la clase.
     */
    public function __construct($name, $email) // Aceptamos el correo en el constructor
    {
        $this->name = $name;
        $this->email = $email;  // Asignamos el correo
    }

    /**
     * Ejecuta la operación envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablece tu contraseña',
        );
    }

    /**
     * Ejecuta la operación content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'name' => $this->name,
                'email' => $this->email,  // Pasamos el correo a la vista
            ],
        );
    }

    /**
     * Ejecuta la operación attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}