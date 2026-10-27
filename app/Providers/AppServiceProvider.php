<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Correo de verificación de correo electrónico, en español y con la marca de KUSI
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifica tu correo electrónico - KUSI')
                ->greeting('¡Hola, ' . $notifiable->name . '!')
                ->line('Haz clic en el botón para confirmar que este correo te pertenece y activar tu cuenta.')
                ->action('Verificar correo', $url)
                ->line('Si no solicitaste esto, puedes ignorar este mensaje.')
                ->salutation('Sociedad de Apoyo Social KUSI');
        });
    }
}
