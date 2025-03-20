<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreatedUser extends Notification implements ShouldQueue
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
            ->subject('Conta criada com sucesso!')
            ->greeting('Olá ' . $this->user->name . ',')
            ->line('Sua conta no ' . config('app.name') . ' foi criada com sucesso.')
            ->line('**Detalhes da conta:**')
            ->line('E-mail: ' . $this->user->email)
            ->line('Data de criação: ' . $this->user->created_at->format('d/m/Y H:i'))
            ->action('Acessar sua conta', url('/login'))
            ->line('Se você não reconhece esta ação, por favor entre em contato conosco.')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
