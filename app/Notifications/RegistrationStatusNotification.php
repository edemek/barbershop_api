<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationStatusNotification extends Notification
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
    public function via($notifiable)
    {
        return ['mail']; // On utilise l'e-mail pour cette notification
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirmation de votre inscription')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre inscription a reussi.')
            ->line('Merci pour votre inscription.');
    }

    public function toArray($notifiable)
    {
        return [
            // Si tu veux stocker des infos dans la base pour d'autres canaux
        ];
    }
}
