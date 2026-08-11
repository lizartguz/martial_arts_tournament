<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Construye el correo enviado por la notificación.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Restablece tu contraseña en Senvatec')
            ->view('emails.password-reset-senvatec', [
                'resetUrl' => $resetUrl,
                'userName' => trim(($notifiable->name ?? '') . ' ' . ($notifiable->lastname ?? '')),
                'supportEmail' => config('mail.from.address'),
            ]);
    }

    /**
     * Construye la URL segura para restablecer contraseña.
     */
    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
