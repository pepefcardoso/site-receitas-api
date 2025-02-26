<?php

namespace App\Notifications;

use App\Models\NewsletterCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateNewsletterCustomerNotification extends Notification implements ShouldQueue
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
            ->subject('Bem-vindo à nossa newsletter!')
            ->greeting('Olá,')
            ->line('Obrigado por se inscrever na nossa newsletter!')
            ->line('A partir de agora, você receberá as últimas novidades, promoções e atualizações diretamente no seu e-mail (' . $this->customer->email . ').')
            ->line('Se você não se inscreveu ou deseja cancelar a assinatura, clique no link abaixo:')
            ->action('Cancelar Inscrição', url('/api/newsletter/delete' . $this->customer->id))
            ->line('Estamos felizes em tê-lo conosco!')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
