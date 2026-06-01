<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locker  = $this->reservation->locker;
        $starts  = $this->reservation->starts_at->format('d/m/Y H:i');
        $ends    = $this->reservation->ends_at->format('d/m/Y H:i');
        $minutes = $this->reservation->starts_at->diffInMinutes($this->reservation->ends_at);
        $amount  = number_format($this->reservation->amount_paid ?? ($minutes * 0.15), 2, ',', '.');

        return (new MailMessage)
            ->subject('✅ Reserva confirmada — ' . $locker->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A tua reserva foi criada com sucesso!')
            ->line('**Cacifo:** ' . $locker->name)
            ->line('**Localização:** ' . $locker->location)
            ->line('**Início:** ' . $starts)
            ->line('**Fim:** ' . $ends)
            ->line('**Duração:** ' . $minutes . ' minutos')
            ->line('**Valor pago:** ' . $amount . '€')
            ->action('Ver cacifo', url('/locker/' . $locker->id))
            ->line('Durante o período da reserva, gera o QR Code para abrir o cacifo.')
            ->salutation('ESS Cacifo QR');
    }
}
