<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CustomerContact;

class CustomerContactNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $customerContact;

    public function __construct(CustomerContact $customerContact)
    {
        $this->customerContact = $customerContact;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Agradecemos por entrar em contato!')
            ->greeting('Olá ' . $this->customerContact->name . ',')
            ->line('Agradecemos por entrar em contato conosco. Recebemos sua mensagem e retornaremos em breve.')
            ->line('**Seus dados de contato:**')
            ->line('E-mail: ' . $this->customerContact->email)
            ->line('Telefone: ' . $this->customerContact->phone)
            ->line('Mensagem: ' . $this->customerContact->message)
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
