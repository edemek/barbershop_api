<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
<<<<<<< Updated upstream
=======
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
>>>>>>> Stashed changes

class RegistrationStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage())
            ->subject('Statut de votre inscription')
            ->line('Votre inscription a été ' . $this->status . '.');

        if ($this->status === 'rejetée' && $this->reason) {
            $message->line('Raison du rejet : ' . $this->reason);
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'status' => $this->status,
            'reason' => $this->reason,
        ];
    }

<<<<<<< Updated upstream
    // public function notifications()
    // {
    //     return auth()->user()->notifications; // Récupère toutes les notifications pour l'utilisateur authentifié
    // }
=======
>>>>>>> Stashed changes
}
