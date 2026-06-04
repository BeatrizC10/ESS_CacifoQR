<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationExpiredNotification extends Notification
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
        $endedAt = $this->reservation->ends_at->format('H:i');

        return (new MailMessage)
            ->subject('🔒 Reserva terminada — ' . $locker->name . ' bloqueado')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A tua reserva do **' . $locker->name . '** terminou às **' . $endedAt . '**.')
            ->line('**Localização:** ' . $locker->location)
            ->line('O cacifo foi bloqueado automaticamente.')
            ->action('Ver histórico', url('/reservations'))
            ->line('Esperamos ver-te em breve!')
            ->salutation('ESS Cacifo QR');
    }
}
