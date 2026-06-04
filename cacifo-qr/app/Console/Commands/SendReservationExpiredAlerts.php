<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReservationExpiredNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendReservationExpiredAlerts extends Command
{
    protected $signature   = 'reservations:send-expired-alerts';
    protected $description = 'Envia email quando a reserva terminou e o cacifo foi bloqueado';

    public function handle(): void
    {
        $now    = Carbon::now();
        $window = $now->copy()->subMinutes(2);

        // Reservas que terminaram nos últimos 2 minutos
        // e ainda não tiveram o alerta de expiração enviado
        $reservations = Reservation::with(['user', 'locker'])
            ->where('status', 'expired')
            ->whereBetween('ends_at', [$window, $now])
            ->where('expired_alert_sent', false)
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new ReservationExpiredNotification($reservation));

            $reservation->update(['expired_alert_sent' => true]);

            $this->info("Email enviado para {$reservation->user->email} — {$reservation->locker->name}");
        }

        if ($reservations->isEmpty()) {
            $this->info('Nenhuma reserva expirada recentemente.');
        }
    }
}
