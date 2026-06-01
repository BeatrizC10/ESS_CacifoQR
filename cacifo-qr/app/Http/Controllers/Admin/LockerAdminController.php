<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locker;
use App\Models\LockerLog;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LockerAdminController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $lockersQuery = Locker::with([
            'reservations',
            'logs' => function ($query) { $query->latest(); }
        ]);

        if ($request->filled('status')) {
            $lockersQuery->where('status', $request->status);
        }

        $lockers = $lockersQuery->orderBy('id')->get();

        $logsQuery = LockerLog::with(['locker', 'user'])->latest();

        if ($request->filled('event')) {
            $logsQuery->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $logsQuery->where('user_id', $request->user_id);
        }

        $logs  = $logsQuery->take(50)->get();
        $users = User::orderBy('name')->get();

        // ── Dados para os gráficos ────────────────────────────────────

        // Gráfico 1: Reservas por dia (últimos 7 dias)
        $reservationsByDay = [];
        $reservationLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $reservationLabels[] = $date->format('d/m');
            $reservationsByDay[] = Reservation::whereDate('created_at', $date->toDateString())->count();
        }

        // Gráfico 2: Top eventos nos logs
        $eventCounts = LockerLog::selectRaw('event, COUNT(*) as total')
            ->groupBy('event')
            ->orderByDesc('total')
            ->take(6)
            ->get();
        $eventLabels = $eventCounts->pluck('event')->toArray();
        $eventData   = $eventCounts->pluck('total')->toArray();

        // Gráfico 3: Distribuição de estados dos cacifos (baseado nos logs)
        $statusDistribution = [
            'available'   => LockerLog::where('event', 'locker_reset')->count()
                           + LockerLog::where('event', 'reservation_ended')->count(),
            'reserved'    => LockerLog::where('event', 'reservation_created')->count(),
            'open'        => LockerLog::where('event', 'reservation_started')->count()
                           + LockerLog::where('event', 'locker_opened')->count(),
            'closed'      => LockerLog::where('event', 'locker_closed')->count()
                           + LockerLog::where('event', 'locker_closed_by_user')->count(),
        ];

        return view('admin.lockers.index', compact(
            'lockers', 'logs', 'users',
            'reservationsByDay', 'reservationLabels',
            'eventLabels', 'eventData',
            'statusDistribution'
        ));
    }

    public function open(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);
        $locker->update(['status' => 'open', 'door_open' => true]);

        LockerLog::create([
            'locker_id'   => $locker->id,
            'user_id'     => Auth::id(),
            'event'       => 'locker_opened',
            'description' => 'Cacifo aberto manualmente pelo administrador.',
        ]);

        return redirect()->route('admin.lockers.index')->with('success', 'Cacifo aberto com sucesso.');
    }

    public function close(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);
        $locker->update(['status' => 'closed', 'door_open' => false]);

        LockerLog::create([
            'locker_id'   => $locker->id,
            'user_id'     => Auth::id(),
            'event'       => 'locker_closed',
            'description' => 'Cacifo fechado manualmente pelo administrador.',
        ]);

        return redirect()->route('admin.lockers.index')->with('success', 'Cacifo fechado com sucesso.');
    }

    public function reset(int $id)
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $locker = Locker::findOrFail($id);

        Reservation::where('locker_id', $locker->id)
            ->where('status', 'active')
            ->update(['status' => 'finished']);

        $locker->update(['status' => 'available', 'door_open' => false]);

        LockerLog::create([
            'locker_id'   => $locker->id,
            'user_id'     => Auth::id(),
            'event'       => 'locker_reset',
            'description' => 'Cacifo reposto manualmente para disponível.',
        ]);

        return redirect()->route('admin.lockers.index')->with('success', 'Cacifo reposto para disponível.');
    }
}
