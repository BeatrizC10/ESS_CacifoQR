<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locker;
use App\Models\LockerLog;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class LockerAdminController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $lockers = Locker::with([
            'reservations',
            'logs' => function ($query) {
                $query->latest();
            }
        ])->get();

        $logs = LockerLog::with(['locker', 'user'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.lockers.index', compact('lockers', 'logs'));
    }

    public function open(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);

        $locker->update([
            'status' => 'open',
            'door_open' => true,
            'open_command' => true,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'locker_opened',
            'description' => 'Cacifo aberto manualmente pelo administrador.',
        ]);

        return redirect()->route('admin.lockers.index')
            ->with('success', 'Cacifo aberto com sucesso.');
    }

    public function close(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);

        $locker->update([
            'status' => 'closed',
            'door_open' => false,
            'open_command' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'locker_closed',
            'description' => 'Cacifo fechado manualmente pelo administrador.',
        ]);

        return redirect()->route('admin.lockers.index')
            ->with('success', 'Cacifo fechado com sucesso.');
    }

    public function reset(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);

        Reservation::where('locker_id', $locker->id)
            ->where('status', 'active')
            ->update([
                'status' => 'finished',
            ]);

        $locker->update([
            'status' => 'available',
            'door_open' => false,
            'open_command' => false,
        ]);

        LockerLog::create([
            'locker_id' => $locker->id,
            'user_id' => Auth::id(),
            'event' => 'locker_reset',
            'description' => 'Cacifo reposto manualmente para disponível.',
        ]);

        return redirect()->route('admin.lockers.index')
            ->with('success', 'Cacifo reposto para disponível.');
    }
}
