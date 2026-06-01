<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locker = $this->reservation->locker;
        $endsAt = $this->reservation->ends_at->format('H:i');

        return (new MailMessage)
            ->subject('⚠️ A tua reserva termina em 2 minutos!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A tua reserva do **' . $locker->name . '** termina às **' . $endsAt . '** (em 2 minutos).')
            ->line('Localização: ' . $locker->location)
            ->action('Ver cacifo', url('/locker/' . $locker->id))
            ->line('Certifica-te de que retiras os teus objetos a tempo.')
            ->salutation('ESS Cacifo QR');
    }
}
