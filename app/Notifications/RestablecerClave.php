<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestablecerClave extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Restablece tu contraseña - KUSI')
            ->view(['emails.restablecer', 'emails.restablecer-texto'], [
                'nombre' => $notifiable->name,
                'usuario' => $notifiable->usuario,
                'url' => $url,
                'minutos' => $minutos,
            ]);
    }
}
