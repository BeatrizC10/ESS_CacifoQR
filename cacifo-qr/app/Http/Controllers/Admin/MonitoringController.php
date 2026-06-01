<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locker;
use App\Models\LockerLog;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()?->role === 'admin', 403);

        $lockers = Locker::with([
            'reservations' => fn($q) => $q->where('status', 'active')
        ])->orderBy('id')->get();

        $logs = LockerLog::with(['locker', 'user'])
            ->latest()
            ->take(60)
            ->get();

        return view('admin.panel', compact('lockers', 'logs'));
    }
}
