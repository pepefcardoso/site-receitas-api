<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeletedUser extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua conta foi removida')
            ->greeting('Prezado(a) ' . $this->user->name . ',')
            ->line('Informamos que sua conta no ' . config('app.name') . ' foi removida de nossos sistemas.')
            ->line('**Detalhes da conta removida:**')
            ->line('E-mail: ' . $this->user->email)
            ->line('Data de remoção: ' . now()->format('d/m/Y H:i'))
            ->line('Todos os dados associados à esta conta foram permanentemente excluídos.')
            ->line('Caso isto tenha sido um engano, entre em contato conosco imediatamente.')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
