<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use App\Models\LockerLog;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LockerController extends Controller
{
    public function show(int $id)
    {
        $locker = Locker::findOrFail($id);

        $activeReservation = null;

        if (Auth::check()) {
            $activeReservation = Reservation::where('locker_id', $locker->id)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->whereNotNull('qr_token')
                ->latest()
                ->first();
        }

        return view('lockers.show', compact('locker', 'activeReservation'));
    }

    public function reserve(int $id)
    {
        $locker = Locker::findOrFail($id);

        if ($locker->status !== 'available') {
            return back()->with('error', 'Cacifo não disponível.');
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'locker_id' => $locker->id,
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(10),
            'status' => 'active',
            'qr_token' => (string) Str::uuid(),
            'qr_expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);

        $locker->update([
            'status' => 'reserved',
            'door_open' => false,
            'open_command' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'reservation_created',
            'description' => 'Reserva criada com QR dinâmico.',
        ]);

        return redirect()->route('locker.show', $locker->id)
            ->with('success', 'Reserva criada com sucesso.');
    }

    public function qrAccess(string $token)
    {
        $reservation = Reservation::where('qr_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        if ($reservation->used) {
            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code já foi utilizado.',
            ]);
        }

        if ($reservation->qr_expires_at && now()->greaterThan($reservation->qr_expires_at)) {
            return view('lockers.qr-result', [
                'success' => false,
                'message' => 'Este QR Code expirou.',
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
            'event' => 'qr_validated',
            'description' => 'QR validado com sucesso. Cacifo aberto.',
        ]);

        return view('lockers.qr-result', [
            'success' => true,
            'message' => 'QR válido. O cacifo foi aberto com sucesso.',
        ]);
    }

    public function getStatus(int $id)
    {
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

    public function openFromUser(int $id)
{
    $locker = Locker::findOrFail($id);

    $reservation = Reservation::where('locker_id', $locker->id)
        ->where('user_id', Auth::id())
        ->where('status', 'active')
        ->latest()
        ->first();

    if (!$reservation) {
        return back()->with('error', 'Não tens uma reserva ativa para este cacifo.');
    }

    $locker->update([
        'status' => 'open',
        'door_open' => true,
        'open_command' => true,
    ]);

    LockerLog::create([
        'locker_id' => $locker->id,
        'user_id' => Auth::id(),
        'event' => 'locker_opened_by_user',
        'description' => 'Cacifo aberto manualmente pelo utilizador com reserva ativa.',
    ]);

    return back()->with('success', 'Cacifo aberto com sucesso.');
}

public function closeFromUser(int $id)
{
    $locker = Locker::findOrFail($id);

    $reservation = Reservation::where('locker_id', $locker->id)
        ->where('user_id', Auth::id())
        ->where('status', 'active')
        ->latest()
        ->first();

    if (!$reservation) {
        return back()->with('error', 'Não tens uma reserva ativa para este cacifo.');
    }

    $locker->update([
        'status' => 'closed',
        'door_open' => false,
        'open_command' => false,
    ]);

    LockerLog::create([
        'locker_id' => $locker->id,
        'user_id' => Auth::id(),
        'event' => 'locker_closed_by_user',
        'description' => 'Cacifo fechado manualmente pelo utilizador com reserva ativa.',
    ]);

    return back()->with('success', 'Cacifo fechado com sucesso.');
}

}
