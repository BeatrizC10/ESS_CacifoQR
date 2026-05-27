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
            'payment_method' => ['required', 'string', 'in:stripe,mbway,card,paypal,visa,mastercard'],
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

        // --- SIMULAÇÃO DE PAGAMENTO ---
        $pricePerMinute = 0.15;
        $totalAmount = (int)$request->duration_minutes * $pricePerMinute;

        // Simulação da chamada à Gateway (metodos de pagamento)
        $paymentSuccess = $this->simulateGatewayCharge($request->payment_method, $totalAmount);

        if (!$paymentSuccess) {
            LockerLog::create([
                'locker_id' => $locker->id,
                'user_id' => Auth::id(),
                'event' => 'payment_failed',
                'description' => "Falha no pagamento de {$totalAmount}€ via " . strtoupper($request->payment_method),
            ]);

            return back()->with('error', 'O pagamento falhou ou foi recusado. Tenta novamente.');
        }


        //Verfiicar e descontar saldo
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->wallet_balance < $totalAmount) {
            return back()->with('error', 'Saldo insuficiente. Carrega a tua carteira antes de reservar.');
        }

        $user->decrement('wallet_balance', $totalAmount);

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

    private function simulateGatewayCharge(string $method, float $amount): bool
    {
        // Condição 1: Se o valor ultrapassar 13.50€ (equivale a 0,15*90min), simula falta de saldo
        if ($amount > 13.50) {
            return false;
        }

        // Condição 2: Falha aleatória controlada (ex: 15% de probabilidade de falha de rede)
        if (rand(1, 100) <= 15) {
            return false;
        }

        return true;
    }

    public function endReservation(Reservation $reservation)
    {
        // 1. Validar se o utilizador autenticado é o dono da reserva
        if ($reservation->user_id !== Auth::id()) {
            return back()->with('error', 'Não tens permissão para terminar esta reserva.');
        }

        // 2. Libertar o cacifo associado
        $locker = $reservation->locker;
        if ($locker) {
            $locker->update([
                'status' => 'available',
                'door_open' => false // Garante segurança ao fechar virtualmente
            ]);
        }

        // 3. Registar o evento no log antes de remover/finalizar
        LockerLog::create([
            'locker_id' => $reservation->locker_id,
            'user_id' => Auth::id(),
            'event' => 'reservation_ended_manually',
            'description' => 'Reserva terminada antecipadamente pelo utilizador.',
        ]);

        // 4. Finalizar a reserva (usar delete ou mudar status para 'finished')
        $reservation->delete();

        return redirect()->route('lockers.index')
            ->with('success', 'Reserva terminada e cacifo libertado com sucesso.');
    }

    public function qrAccess(string $token)
    {
        $this->syncLockerStatuses();

        $reservation = Reservation::where('qr_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        $now = now();

        // 1. Verificar se a reserva ainda não começou
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

        // 2. Verificar se a reserva já terminou
        if ($reservation->ends_at && $now->gt($reservation->ends_at)) {
            $reservation->update(['status' => 'finished']);

            // Aqui apenas atualizamos o estado do cacifo para dizer que está disponível
            $reservation->locker?->update([
                'status' => 'available',
                'door_open' => false,
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'A reserva já terminou.',
            ]);
        }

        // 3. Verificar se o QR Code expirou (tempo de validade do token)
        if ($reservation->qr_expires_at && $now->greaterThan($reservation->qr_expires_at)) {
            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'qr_expired',
                'description' => 'Tentativa de uso de QR expirado.',
            ]);

            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code expirou. Terá que gerar um novo QR.',
            ]);
        }

        // 4. Verificar se o QR já foi utilizado
        if ($reservation->used) {
            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code já foi utilizado. Terá que gerar um novo QR.',
            ]);
        }

        // --- SUCESSO: O QR é válido e está no tempo correto ---

        $locker = $reservation->locker;

        // Marcamos o QR como usado para não poder ser reutilizado
        $reservation->update(['used' => true]);

        // ATUALIZAÇÃO CRÍTICA: Abrir o cacifo virtualmente
        $locker->update([
            'status' => 'open',
            'door_open' => true,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => $reservation->user_id,
            'event' => 'reservation_started',
            'description' => 'QR validado com sucesso. Cacifo aberto.',
        ]);

        broadcast(new LockerOpened($locker->id))->toOthers();

        return view('lockers.qr-result', [
            'success' => true,
            'message' => 'QR válido. O cacifo foi aberto com sucesso.',
        ]);
    }

    public function getStatus(int $id)
    {
        $locker = Locker::findOrFail($id);

        return response()->json([
            'door_open' => (bool)$locker->door_open,
            'status' => $locker->status
        ]);
    }

    public function confirmOpen(int $id)
    {
        $locker = Locker::findOrFail($id);

        $locker->update([
            'status' => 'open',
            'door_open' => true,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => null,
            'event' => 'open_confirmed',
            'description' => 'Abertura confirmada pela simulação/API.',
        ]);

        return response()->json(['success' => true]);
    }

    public function generateQr(int $id)
    {

        $locker = Locker::findOrFail($id);

        $reservation = Reservation::where('locker_id', $locker->id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($reservation) {
            $reservation->update([
                'qr_token' => (string) \Illuminate\Support\Str::uuid(),
                'qr_expires_at' => now()->addSeconds(10), // Expira em 10s para ser mesmo dinâmico
                'used' => false,
            ]);
        }

        // Se for um pedido AJAX (do JavaScript), respondemos com JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // Se for um clique manual no botão, faz o redirect normal
        return back()->with('success', 'QR atualizado.');
    }

    public function closeLocker(int $id)
    {
        $locker = Locker::findOrFail($id);

        $reservation = Reservation::where('locker_id', $locker->id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        if (!$reservation) {
            return redirect()->route('locker.show', $locker->id)
                ->with('error', 'Não tens uma reserva ativa para este cacifo.');
        }

        $locker->update([
            'status' => 'reserved',
            'door_open' => false,
        ]);

        $reservation->update([
            'qr_token' => null,
            'qr_expires_at' => null,
            'used' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'locker_closed_by_user',
            'description' => 'Cacifo fechado pelo utilizador e QR removido.',
        ]);

        return redirect()->route('locker.show', $locker->id)
            ->with('success', 'Cacifo fechado. O QR foi removido.');
    }

    public function getQrData(int $id)
    {
        $locker = Locker::findOrFail($id);

        $reservation = Reservation::where('locker_id', $locker->id)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        if (!$reservation || !$reservation->qr_token) {
            return response()->json([
                'has_qr' => false,
                'qr_url' => null,
                'qr_token' => null,
            ]);
        }

        return response()->json([
            'has_qr' => true,
            'qr_url' => url('/qr-access/' . $reservation->qr_token),
            'qr_token' => $reservation->qr_token,
        ]);
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

            $reservation->locker?->update([
                'status' => 'available',
                'door_open' => false,
            ]);

            LockerLog::create([
                'locker_id' => $reservation->locker_id,
                'user_id' => $reservation->user_id,
                'event' => 'reservation_ended',
                'description' => 'Reserva terminada por fim do período.',
            ]);

            return $reservation->fresh();
        }

        // Durante a reserva: não gerar QR automaticamente
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
                    ]);
                }
            } else {
                if ($locker->status === 'reserved') {
                    $locker->update([
                        'status' => 'available',
                        'door_open' => false,
                    ]);
                }
            }
        }
    }
}
