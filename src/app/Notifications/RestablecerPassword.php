<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestablecerPassword extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);
        $minutos = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Restablece tu contraseña · COMVERZA')
            ->view('emails.restablecer-password', [
                'nombre' => $notifiable->name,
                'url' => $url,
                'minutos' => $minutos,
            ]);
    }
}
