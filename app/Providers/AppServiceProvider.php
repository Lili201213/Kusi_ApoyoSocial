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
                ->subject(__('Verifica tu correo electrónico - KUSI'))
                ->greeting(__('¡Hola, :nombre!', ['nombre' => $notifiable->name]))
                ->line(__('Haz clic en el botón para confirmar que este correo te pertenece y activar tu cuenta.'))
                ->action(__('Verificar correo'), $url)
                ->line(__('Si no solicitaste esto, puedes ignorar este mensaje.'))
                ->salutation(__('Sociedad de Apoyo Social KUSI'));
        });
    }
}
