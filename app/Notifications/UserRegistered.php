<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
         $this->user = $user;
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
        $greeting = match ($this->user->type_utilisateur) {
            'admin' => 'Bienvenue à l\'équipe administrative !',
            'medecin' => 'Bienvenue Docteur !',
            'patient' => 'Bienvenue sur Santé Plus !',
            default => 'Bienvenue sur notre plateforme !',
        };

        return (new MailMessage)
            ->subject('Bienvenue sur Santé Plus')
            ->greeting('Bonjour ' . $this->user->prenom . ' 👋')
            ->line($greeting)
            ->line('Votre compte a été créé avec succès.')
            ->action('Accéder à votre espace', url('/login'))
            ->line('Merci d’avoir rejoint Santé Plus !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
