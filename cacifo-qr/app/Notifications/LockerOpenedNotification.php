<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LockerOpenedNotification extends Notification
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
        $now    = now()->format('H:i:s');
        $ends   = $this->reservation->ends_at->format('H:i');

        return (new MailMessage)
            ->subject('🔓 Cacifo aberto — ' . $locker->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O teu cacifo foi aberto com sucesso às **' . $now . '**.')
            ->line('**Cacifo:** ' . $locker->name)
            ->line('**Localização:** ' . $locker->location)
            ->line('**Reserva termina às:** ' . $ends)
            ->action('Ver cacifo', url('/locker/' . $locker->id))
            ->line('Não te esqueças de fechar o cacifo quando terminares.')
            ->salutation('ESS Cacifo QR');
    }
}
