<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locker;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class LockerController extends Controller
{
    // Página aberta pelo QR Code
    public function show(int $id)
    {
        $locker = Locker::findOrFail($id);

        return view('lockers.show', compact('locker'));
    }

    // Reservar cacifo
    public function reserve(int $id)
    {
        $locker = Locker::findOrFail($id);

        if ($locker->status !== 'available') {
            return back()->with('error', 'Cacifo não disponível');
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'locker_id' => $locker->id,
            'starts_at' => now(),
            'status' => 'active'
        ]);

        $locker->update([
            'status' => 'reserved',
            'open_command' => true
        ]);

        return back()->with('success', 'Cacifo reservado! Vai abrir...');
    }

    // API para ESP32 ver se deve abrir
    public function getStatus(int $id)
    {
        $locker = Locker::findOrFail($id);

        return response()->json([
            'open_command' => $locker->open_command
        ]);
    }

    // ESP32 confirma que abriu
    public function confirmOpen(int $id)
    {
        $locker = Locker::findOrFail($id);

        $locker->update([
            'status' => 'open',
            'door_open' => true,
            'open_command' => false
        ]);

        return response()->json(['success' => true]);
    }
}
