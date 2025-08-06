<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class PaymentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nova Fatura Gerada para sua Assinatura')
                    ->markdown('emails.created_payment', [
                        'greetingName' => $notifiable->name,
                        'amount' => number_format($this->payment->amount, 2, ',', '.'),
                        'dueDate' => Carbon::parse($this->payment->due_date)->format('d/m/Y'),
                        'actionText' => 'Ver Fatura',
                        'actionUrl' => url('/dashboard/billing'),
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        //
    }
}
