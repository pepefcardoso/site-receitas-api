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
            ->subject('Thank You for Contacting Us!')
            ->greeting('Hello ' . $this->customerContact->name . ',')
            ->line('Thank you for reaching out to us. We have received your message and will get back to you shortly.')
            ->line('**Your Contact Details:**')
            ->line('Email: ' . $this->customerContact->email)
            ->line('Phone: ' . $this->customerContact->phone)
            ->line('Message: ' . $this->customerContact->message)
            ->salutation('Regards, ' . config('app.name'));
    }
}
