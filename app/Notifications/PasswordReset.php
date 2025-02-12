<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    public $newPassword;
    public $userName;

    public function __construct($userName, $newPassword)
    {
        $this->newPassword = $newPassword;
        $this->userName = $userName;

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Sua senha foi redefinida - LeveSabor')
            ->greeting('Olá ' . $this->userName . '!')
            ->line('Sua senha foi atualizada com sucesso:')
            ->line(new HtmlString('<strong>' . $this->newPassword . '</strong>'))
            ->line('Por segurança, recomendamos que altere esta senha temporária após o login.')
            ->salutation(new HtmlString('Atenciosamente,<br>Equipe LeveSabor'));
    }
}
