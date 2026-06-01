<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title data-pt="Monitorização em Tempo Real" data-en="Real-Time Monitoring">Monitorização em Tempo Real</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; }

        .topbar {
            background: #1e293b;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #334155;
        }
        .topbar h1 { font-size: 18px; color: #38bdf8; display: flex; align-items: center; gap: 10px; }
        .topbar h1 span.dot { width: 10px; height: 10px; border-radius: 50%; background: #22c55e; display: inline-block; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        .clock { font-family: monospace; font-size: 20px; color: #94a3b8; }

        .content { max-width: 1300px; margin: 0 auto; padding: 28px 20px; }

        /* Stats bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 18px 20px;
            text-align: center;
        }
        .stat-card .stat-value { font-size: 36px; font-weight: bold; margin-bottom: 4px; }
        .stat-card .stat-label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-available  { color: #22c55e; }
        .stat-reserved   { color: #f59e0b; }
        .stat-open       { color: #38bdf8; }
        .stat-closed     { color: #f87171; }
        .stat-total      { color: #a78bfa; }

        /* Locker grid */
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
        }
        .lockers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .locker-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 16px;
            padding: 20px;
            transition: border-color 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        .locker-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .locker-card.status-available { border-color: #166534; }
        .locker-card.status-available::before { background: #22c55e; }
        .locker-card.status-reserved  { border-color: #92400e; }
        .locker-card.status-reserved::before  { background: #f59e0b; }
        .locker-card.status-open      { border-color: #075985; }
        .locker-card.status-open::before      { background: #38bdf8; box-shadow: 0 0 12px #38bdf8; }
        .locker-card.status-closed    { border-color: #7f1d1d; }
        .locker-card.status-closed::before    { background: #f87171; }

        .locker-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .locker-name { font-size: 18px; font-weight: 700; }
        .locker-status-badge {
            font-size: 11px; font-weight: 700; padding: 4px 10px;
            border-radius: 999px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .badge-available { background: #14532d; color: #86efac; }
        .badge-reserved  { background: #451a03; color: #fde68a; }
        .badge-open      { background: #0c4a6e; color: #7dd3fc; }
        .badge-closed    { background: #450a0a; color: #fca5a5; }

        .locker-info { display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #94a3b8; }
        .locker-info-row { display: flex; justify-content: space-between; align-items: center; }
        .locker-info-row span:last-child { color: #e2e8f0; font-weight: 500; }

        .door-indicator {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 600;
        }
        .door-open-icon  { color: #38bdf8; }
        .door-closed-icon { color: #94a3b8; }

        /* Reservation progress bar */
        .reservation-progress { margin-top: 12px; }
        .progress-label { font-size: 11px; color: #64748b; margin-bottom: 5px; display: flex; justify-content: space-between; }
        .progress-bar-bg { background: #334155; border-radius: 4px; height: 6px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 4px; transition: width 1s linear; }
        .progress-green  { background: #22c55e; }
        .progress-yellow { background: #f59e0b; }
        .progress-red    { background: #f87171; }

        /* Admin actions */
        .locker-actions { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
        .action-btn {
            flex: 1; min-width: 80px;
            padding: 8px 10px;
            border: none; border-radius: 8px;
            cursor: pointer; font-size: 12px; font-weight: 700;
            color: white; transition: opacity 0.15s;
        }
        .action-btn:hover { opacity: 0.85; }
        .btn-open  { background: #16a34a; }
        .btn-close { background: #dc2626; }
        .btn-reset { background: #2563eb; }

        /* Live log */
        .live-log {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            overflow: hidden;
        }
        .live-log-header {
            padding: 14px 20px;
            border-bottom: 1px solid #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .live-log-header h2 { font-size: 14px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; }
        .log-count { font-size: 12px; color: #64748b; }
        .live-log-body { max-height: 340px; overflow-y: auto; }
        .log-row {
            display: grid;
            grid-template-columns: 140px 100px 1fr 120px;
            gap: 12px;
            padding: 10px 20px;
            border-bottom: 1px solid #1e293b;
            font-size: 13px;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn { from { opacity:0; transform: translateY(-4px); } to { opacity:1; transform: translateY(0); } }
        .log-row:hover { background: #243148; }
        .log-row:nth-child(odd) { background: #172032; }
        .log-row:nth-child(odd):hover { background: #243148; }
        .log-time { color: #64748b; font-family: monospace; font-size: 12px; }
        .log-locker { color: #38bdf8; font-weight: 600; }
        .log-event { color: #94a3b8; }
        .log-user { color: #a78bfa; text-align: right; }

        /* Alert banner */
        .alert-banner {
            display: none;
            background: #7f1d1d;
            border: 1px solid #f87171;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
            color: #fca5a5;
            font-size: 14px;
            font-weight: 600;
            animation: fadeIn 0.3s ease;
        }
        .alert-banner.show { display: flex; gap: 10px; align-items: center; }

        @media (max-width: 700px) {
            .log-row { grid-template-columns: 1fr 1fr; }
            .log-time, .log-user { display: none; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="topbar">
    <h1>
        <span class="dot"></span>
        <span data-pt="Monitorização em Tempo Real" data-en="Real-Time Monitoring">Monitorização em Tempo Real</span>
    </h1>
    <div class="clock" id="live-clock">--:--:--</div>
</div>

<div class="content">

    {{-- Alert banner (aparece quando cacifo fica aberto inesperadamente) --}}
    <div class="alert-banner" id="alert-banner">
        ⚠️ <span id="alert-text"></span>
    </div>

    {{-- Stats --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-value stat-total" id="stat-total">{{ $lockers->count() }}</div>
            <div class="stat-label" data-pt="Total de Cacifos" data-en="Total Lockers">Total de Cacifos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-available" id="stat-available">
                {{ $lockers->where('status','available')->count() }}
            </div>
            <div class="stat-label" data-pt="Disponíveis" data-en="Available">Disponíveis</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-reserved" id="stat-reserved">
                {{ $lockers->where('status','reserved')->count() }}
            </div>
            <div class="stat-label" data-pt="Reservados" data-en="Reserved">Reservados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-open" id="stat-open">
                {{ $lockers->where('status','open')->count() }}
            </div>
            <div class="stat-label" data-pt="Abertos" data-en="Open">Abertos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-closed" id="stat-closed">
                {{ $lockers->where('status','closed')->count() }}
            </div>
            <div class="stat-label" data-pt="Fechados" data-en="Closed">Fechados</div>
        </div>
    </div>

    {{-- Locker cards --}}
    <div class="section-title" data-pt="Estado dos Cacifos" data-en="Locker Status">Estado dos Cacifos</div>
    <div class="lockers-grid" id="lockers-grid">
        @foreach($lockers as $locker)
            @php
                $reservation = $locker->reservations()
                    ->where('status','active')
                    ->where('starts_at','<=', now())
                    ->where('ends_at','>', now())
                    ->latest()->first();
                $progressPct = 0;
                $progressColor = 'progress-green';
                $timeLeft = '';
                if ($reservation) {
                    $total   = $reservation->starts_at->diffInSeconds($reservation->ends_at);
                    $elapsed = $reservation->starts_at->diffInSeconds(now());
                    $progressPct = min(100, round($elapsed / $total * 100));
                    $remaining = now()->diffInMinutes($reservation->ends_at);
                    $progressColor = $remaining <= 2 ? 'progress-red' : ($remaining <= 5 ? 'progress-yellow' : 'progress-green');
                    $timeLeft = $remaining . ' min';
                }
            @endphp
            <div class="locker-card status-{{ $locker->status }}"
                 id="locker-card-{{ $locker->id }}"
                 data-locker-id="{{ $locker->id }}"
                 data-status="{{ $locker->status }}">

                <div class="locker-header">
                    <div class="locker-name locker-name-el" data-locker-name="{{ $locker->name }}">{{ $locker->name }}</div>
                    <span class="locker-status-badge badge-{{ $locker->status }} status-badge-el">
                        {{ strtoupper($locker->status) }}
                    </span>
                </div>

                <div class="locker-info">
                    <div class="locker-info-row">
                        <span data-pt="Localização" data-en="Location">Localização</span>
                        <span>{{ $locker->location }}</span>
                    </div>
                    <div class="locker-info-row">
                        <span data-pt="Porta" data-en="Door">Porta</span>
                        <span class="door-indicator door-el">
                            @if($locker->door_open)
                                <span class="door-open-icon">🔓</span>
                                <span data-pt="Aberta" data-en="Open">Aberta</span>
                            @else
                                <span class="door-closed-icon">🔒</span>
                                <span data-pt="Fechada" data-en="Closed">Fechada</span>
                            @endif
                        </span>
                    </div>
                    @if($reservation)
                        <div class="locker-info-row">
                            <span data-pt="Utilizador" data-en="User">Utilizador</span>
                            <span>{{ $reservation->user->name }}</span>
                        </div>
                        <div class="locker-info-row">
                            <span data-pt="Termina às" data-en="Ends at">Termina às</span>
                            <span>{{ $reservation->ends_at->format('H:i') }}</span>
                        </div>
                        <div class="reservation-progress">
                            <div class="progress-label">
                                <span data-pt="Tempo de reserva" data-en="Reservation time">Tempo de reserva</span>
                                <span class="time-left-el">{{ $timeLeft }}</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill {{ $progressColor }}"
                                     style="width: {{ $progressPct }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="locker-actions">
                    <form method="POST" action="{{ route('admin.lockers.open', $locker->id) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="action-btn btn-open" style="width:100%"
                            data-pt="Abrir" data-en="Open">Abrir</button>
                    </form>
                    <form method="POST" action="{{ route('admin.lockers.close', $locker->id) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="action-btn btn-close" style="width:100%"
                            data-pt="Fechar" data-en="Close">Fechar</button>
                    </form>
                    <form method="POST" action="{{ route('admin.lockers.reset', $locker->id) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="action-btn btn-reset" style="width:100%"
                            data-pt="Repor" data-en="Reset">Repor</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Live log --}}
    <div class="live-log">
        <div class="live-log-header">
            <h2 data-pt="Log em Tempo Real" data-en="Real-Time Log">Log em Tempo Real</h2>
            <span class="log-count" id="log-count">
                {{ $logs->count() }} <span data-pt="entradas" data-en="entries">entradas</span>
            </span>
        </div>
        <div class="live-log-body" id="live-log-body">
            <div class="log-row" style="font-weight:700; font-size:12px; color:#475569; background:#0f172a; position:sticky; top:0;">
                <span data-pt="Data / Hora" data-en="Date / Time">Data / Hora</span>
                <span data-pt="Cacifo" data-en="Locker">Cacifo</span>
                <span data-pt="Descrição" data-en="Description">Descrição</span>
                <span data-pt="Utilizador" data-en="User" style="text-align:right;">Utilizador</span>
            </div>
            @foreach($logs as $log)
                <div class="log-row">
                    <span class="log-time">{{ $log->created_at->format('d/m H:i:s') }}</span>
                    <span class="log-locker locker-name" data-locker-name="{{ $log->locker->name ?? '' }}">
                        {{ $log->locker->name ?? '-' }}
                    </span>
                    <span class="log-event log-description" data-description="{{ $log->description }}">
                        {{ $log->description }}
                    </span>
                    <span class="log-user">{{ $log->user->name ?? 'Sistema' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// ── Relógio em tempo real ─────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('live-clock').textContent =
        now.toLocaleTimeString('pt-PT');
}
setInterval(updateClock, 1000);
updateClock();

// ── Polling de estado dos cacifos (a cada 3s) ─────────────────────────
const lockerIds = @json($lockers->pluck('id'));
let lastStates  = {};

// Guarda o estado inicial
document.querySelectorAll('.locker-card').forEach(card => {
    lastStates[card.dataset.lockerId] = card.dataset.status;
});

function updateStats(states) {
    const counts = { available:0, reserved:0, open:0, closed:0 };
    Object.values(states).forEach(s => { if (counts[s] !== undefined) counts[s]++; });
    document.getElementById('stat-available').textContent = counts.available;
    document.getElementById('stat-reserved').textContent  = counts.reserved;
    document.getElementById('stat-open').textContent      = counts.open;
    document.getElementById('stat-closed').textContent    = counts.closed;
}

function showAlert(msg) {
    const banner = document.getElementById('alert-banner');
    document.getElementById('alert-text').textContent = msg;
    banner.classList.add('show');
    setTimeout(() => banner.classList.remove('show'), 8000);
}

async function pollLockers() {
    for (const id of lockerIds) {
        try {
            const res  = await fetch(`/api/locker/${id}/status`);
            const data = await res.json();
            const card = document.getElementById(`locker-card-${id}`);
            if (!card) continue;

            const prev = lastStates[id];
            const curr = data.status;

            if (prev !== curr) {
                // Atualiza a classe de cor do card
                card.className = card.className.replace(/status-\w+/, `status-${curr}`);
                card.dataset.status = curr;

                // Atualiza o badge
                const badge = card.querySelector('.status-badge-el');
                if (badge) {
                    badge.className = `locker-status-badge badge-${curr} status-badge-el`;
                    badge.textContent = curr.toUpperCase();
                }

                // Atualiza o indicador de porta
                const doorEl = card.querySelector('.door-el');
                if (doorEl) {
                    doorEl.innerHTML = data.door_open
                        ? '<span class="door-open-icon">🔓</span> <span>Aberta</span>'
                        : '<span class="door-closed-icon">🔒</span> <span>Fechada</span>';
                }

                // Alerta se ficar open inesperadamente
                const lockerName = card.querySelector('.locker-name-el')?.textContent || `Cacifo ${id}`;
                if (curr === 'open' && prev !== 'open') {
                    showAlert(`${lockerName} foi aberto!`);
                }

                lastStates[id] = curr;
                updateStats(lastStates);
            }
        } catch(e) { console.error(`Erro ao verificar cacifo ${id}:`, e); }
    }
}

setInterval(pollLockers, 3000);

// ── WebSocket (Reverb) para atualização instantânea ───────────────────
if (typeof window.Echo !== 'undefined') {
    lockerIds.forEach(id => {
        window.Echo.channel(`locker.${id}`)
            .listen('.LockerStatusChanged', (e) => {
                const card = document.getElementById(`locker-card-${id}`);
                if (!card) return;
                const badge = card.querySelector('.status-badge-el');
                if (badge) {
                    badge.className = `locker-status-badge badge-${e.status} status-badge-el`;
                    badge.textContent = e.status.toUpperCase();
                }
                card.className = card.className.replace(/status-\w+/, `status-${e.status}`);
                card.dataset.status = e.status;
                lastStates[id] = e.status;
                updateStats(lastStates);
            });
    });
}

// ── Aplica língua ao carregar ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const lang = localStorage.getItem('lang') || 'pt';
    if (lang === 'en' && typeof window._applyFullLang === 'function') {
        window._applyFullLang('en');
    }
});
</script>
</body>
</html>
