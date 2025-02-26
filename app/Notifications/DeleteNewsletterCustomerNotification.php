<?php

namespace App\Notifications;

use App\Models\NewsletterCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteNewsletterCustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $customer;

    public function __construct(NewsletterCustomer $customer)
    {
        $this->customer = $customer;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Você foi removido da nossa newsletter')
            ->greeting('Olá,')
            ->line('Lamentamos informar que o seu e-mail (' . $this->customer->email . ') foi removido da nossa lista de newsletter.')
            ->line('Se isso foi um erro ou se deseja se reinscrever, por favor, visite nosso site e inscreva-se novamente.')
            ->action('Visitar o Site', url('/'))
            ->line('Agradecemos por ter feito parte da nossa comunidade.')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
