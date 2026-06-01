<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReservationExpiringNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendReservationExpiringAlerts extends Command
{
    protected $signature   = 'reservations:send-expiring-alerts';
    protected $description = 'Envia email de alerta quando a reserva falta 2 minutos para terminar';

    public function handle(): void
    {
        $now     = Carbon::now();
        $window  = $now->copy()->addMinutes(2);

        // Reservas que terminam entre agora e daqui a 2 minutos
        // e ainda não tiveram o alerta enviado (alert_sent = false)
        $reservations = Reservation::with(['user', 'locker'])
            ->where('status', 'active')
            ->whereBetween('ends_at', [$now, $window])
            ->where('alert_sent', false)
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new ReservationExpiringNotification($reservation));

            // Marca como enviado para não enviar duas vezes
            $reservation->update(['alert_sent' => true]);

            $this->info("Alerta enviado para {$reservation->user->email} — {$reservation->locker->name}");
        }

        if ($reservations->isEmpty()) {
            $this->info('Nenhuma reserva a expirar nos próximos 2 minutos.');
        }
    }
}
