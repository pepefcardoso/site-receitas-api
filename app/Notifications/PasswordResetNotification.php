<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;  // Add token property
    public $userName;

    public function __construct($userName, $token)
    {
        $this->userName = $userName;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(config('app.frontend_url') . '/reset-password?' . http_build_query([
                'token' => $this->token,
                'email' => $notifiable->email,
            ]));

        return (new MailMessage)
            ->subject('Redefinição de Senha - LeveSabor')
            ->greeting('Olá ' . $this->userName . '!')
            ->line('Clique no botão abaixo para redefinir sua senha:')
            ->action('Redefinir Senha', $resetUrl)
            ->line('Este link expirará em 60 minutos.')
            ->salutation('Atenciosamente, Equipe LeveSabor');
    }
}
