<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use App\Models\LockerLog;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LockerController extends Controller
{
    public function index()
    {
        $this->syncLockerStatuses();

        $lockers = Locker::orderBy('id')->get();

        return view('lockers.index', compact('lockers'));
    }

    public function show(int $id)
    {
        $this->syncLockerStatuses();

        $locker = Locker::findOrFail($id);

        $activeReservation = null;

        if (Auth::check()) {
            $activeReservation = Reservation::where('locker_id', $locker->id)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($activeReservation) {
                $activeReservation = $this->refreshQrTokenIfNeeded($activeReservation);
            }
        }

        return view('lockers.show', compact('locker', 'activeReservation'));
    }

    public function reserve(Request $request, int $id)
    {
        $this->syncLockerStatuses();

        $locker = Locker::findOrFail($id);

        $request->validate([
            'reservation_date' => ['required', 'date'],
            'reservation_time' => ['required'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:240'],
        ]);

        $startsAt = Carbon::parse(
            $request->reservation_date . ' ' . $request->reservation_time
        );

        $endsAt = (clone $startsAt)->addMinutes((int) $request->duration_minutes);

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            return back()->with('error', 'A duração da reserva é inválida.');
        }

        if ($endsAt->lessThanOrEqualTo(now())) {
            return back()->with('error', 'A reserva tem de terminar no futuro.');
        }

        $lockerConflict = Reservation::where('locker_id', $locker->id)
            ->whereIn('status', ['active'])
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();

        if ($lockerConflict) {
            LockerLog::create([
                'locker_id' => $locker->id,
                'user_id' => Auth::id(),
                'event' => 'reservation_denied_conflict',
                'description' => 'Reserva rejeitada por conflito no mesmo intervalo de tempo.',
            ]);

            return back()->with('error', 'Este cacifo já está reservado nesse período.');
        }

        $userConflict = Reservation::where('user_id', Auth::id())
            ->whereIn('status', ['active'])
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();

        if ($userConflict) {
            LockerLog::create([
                'locker_id' => $locker->id,
                'user_id' => Auth::id(),
                'event' => 'reservation_denied_conflict',
                'description' => 'Reserva rejeitada porque o utilizador já tem outra reserva nesse período.',
            ]);

            return back()->with('error', 'Já tens uma reserva ativa nesse período.');
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'locker_id' => $locker->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'qr_token' => null,
            'qr_expires_at' => null,
            'used' => false,
        ]);

        $locker->update([
            'status' => $startsAt->lte(now()) ? 'reserved' : 'available',
            'door_open' => false,
            'open_command' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'reservation_created',
            'description' => "Reserva criada de {$startsAt->format('d/m/Y H:i')} até {$endsAt->format('d/m/Y H:i')}.",
        ]);

        return redirect()->route('locker.show', $locker->id)
            ->with('success', 'Reserva criada com sucesso.');
    }

    public function qrAccess(string $token)
    {
        $this->syncLockerStatuses();

        $reservation = Reservation::where('qr_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        $now = now();

        if ($reservation->starts_at && $now->lt($reservation->starts_at)) {
            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'access_denied_outside_time_window',
                'description' => 'Tentativa de acesso antes do início da reserva.',
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'A reserva ainda não começou.',
            ]);
        }

        if ($reservation->ends_at && $now->gt($reservation->ends_at)) {
            $reservation->update(['status' => 'finished']);

            $reservation->locker?->update([
                'status' => 'available',
                'door_open' => false,
                'open_command' => false,
            ]);

            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'reservation_ended',
                'description' => 'Reserva terminada. Tentativa de acesso após o fim do período.',
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'A reserva já terminou.',
            ]);
        }

        if ($reservation->qr_expires_at && $now->greaterThan($reservation->qr_expires_at)) {
            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'qr_expired',
                'description' => 'Tentativa de uso de QR expirado.',
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code expirou. Volta à página do cacifo para gerar o novo QR.',
            ]);
        }

        if ($reservation->used) {
            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'qr_expired',
                'description' => 'Tentativa de uso de QR já utilizado.',
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code já foi utilizado. Aguarda a renovação automática.',
            ]);
        }

        $locker = $reservation->locker;

        $reservation->update([
            'used' => true,
        ]);

        $locker->update([
            'status' => 'open',
            'door_open' => true,
            'open_command' => true,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => $reservation->user_id,
            'event' => 'reservation_started',
            'description' => 'Reserva em utilização. QR validado com sucesso e cacifo aberto.',
        ]);

        return view('lockers.qr-result', [
            'success' => true,
            'message' => 'QR válido. O cacifo foi aberto com sucesso.',
        ]);
    }

    public function getStatus(int $id)
    {
        $this->syncLockerStatuses();

        $locker = Locker::findOrFail($id);

        return response()->json([
            'open_command' => $locker->open_command,
            'status' => $locker->status,
            'door_open' => $locker->door_open,
        ]);
    }

    public function confirmOpen(int $id)
    {
        $locker = Locker::findOrFail($id);

        $locker->update([
            'status' => 'open',
            'door_open' => true,
            'open_command' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => null,
            'event' => 'open_confirmed',
            'description' => 'Abertura confirmada pela simulação/API.',
        ]);

        return response()->json(['success' => true]);
    }

    private function refreshQrTokenIfNeeded(Reservation $reservation): Reservation
    {
        $now = now();

        if ($reservation->status !== 'active') {
            return $reservation;
        }

        // Ainda não começou: não gerar QR
        if ($reservation->starts_at && $now->lt($reservation->starts_at)) {
            $reservation->update([
                'qr_token' => null,
                'qr_expires_at' => null,
                'used' => false,
            ]);

            return $reservation->fresh();
        }

        // Já terminou
        if ($reservation->ends_at && $now->gt($reservation->ends_at)) {
            $reservation->update([
                'status' => 'finished',
                'qr_token' => null,
                'qr_expires_at' => null,
                'used' => false,
            ]);

            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'reservation_ended',
                'description' => 'Reserva terminada por fim do período.',
            ]);

            return $reservation->fresh();
        }

        // Durante a reserva: gerar/renovar QR de 1 em 1 minuto
        if (!$reservation->qr_expires_at || $now->greaterThanOrEqualTo($reservation->qr_expires_at)) {
            $reservation->update([
                'qr_token' => (string) Str::uuid(),
                'qr_expires_at' => $now->copy()->addMinute(),
                'used' => false,
            ]);

            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'qr_refreshed',
                'description' => 'QR renovado automaticamente por mais 1 minuto.',
            ]);
        }

        return $reservation->fresh();
    }

    private function syncLockerStatuses(): void
    {
        $now = now();

        // 1. Terminar reservas expiradas
        $endedReservations = Reservation::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', $now)
            ->get();

        foreach ($endedReservations as $reservation) {
            $reservation->update([
                'status' => 'finished',
                'qr_token' => null,
                'qr_expires_at' => null,
                'used' => false,
            ]);

            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'reservation_ended',
                'description' => 'Reserva terminada automaticamente por expiração do período.',
            ]);
        }

        // 2. Atualizar apenas cacifos que estão em estado automático
        $lockers = Locker::with(['reservations' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        foreach ($lockers as $locker) {
            $currentActiveReservation = $locker->reservations
                ->filter(function ($reservation) use ($now) {
                    return $reservation->starts_at <= $now && $reservation->ends_at > $now;
                })
                ->sortBy('starts_at')
                ->first();

            /*
         * IMPORTANTE:
         * Se o admin colocou o cacifo em "open" ou "closed",
         * não vamos sobrescrever automaticamente.
         */
            if (in_array($locker->status, ['open', 'closed'], true)) {
                continue;
            }

            if ($currentActiveReservation) {
                if ($locker->status !== 'reserved') {
                    $locker->update([
                        'status' => 'reserved',
                        'door_open' => false,
                        'open_command' => false,
                    ]);
                }
            } else {
                if ($locker->status === 'reserved') {
                    $locker->update([
                        'status' => 'available',
                        'door_open' => false,
                        'open_command' => false,
                    ]);
                }
            }
        }
    }
}
