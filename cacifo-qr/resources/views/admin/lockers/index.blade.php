<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title data-pt="Painel Admin - Cacifos" data-en="Admin Panel - Lockers">Painel Admin - Cacifos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            color: #1a1a1a;
            min-height: 100vh;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1d4ed8, #0f172a);
            border: 1px solid #1e3a5f;
            color: white;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero h1 {
            margin: 0 0 6px 0;
            font-size: 28px;
        }

        .hero p {
            margin: 0;
            color: #93c5fd;
            font-size: 14px;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 10px 18px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .clock {
            font-family: monospace;
            font-size: 18px;
            color: #94a3b8;
        }

        /* Flash */
        .msg {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .msg-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* Back link */
        .back-link {
            margin-bottom: 20px;
        }

        .back-link a {
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        select {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
            color: #111827;
            font-size: 14px;
        }

        .filter-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: #16a34a;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        /* Stats bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-total {
            color: #a78bfa;
        }

        .stat-available {
            color: #22c55e;
        }

        .stat-reserved {
            color: #f59e0b;
        }

        .stat-open {
            color: #38bdf8;
        }

        .stat-closed {
            color: #f87171;
        }

        /* Section title */
        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
        }

        /* Locker cards */
        .lockers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }

        .locker-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            transition: border-color 0.3s;
            position: relative;
            overflow: hidden;
        }

        .locker-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .locker-card.s-available {
            border-color: #166534;
        }

        .locker-card.s-available::before {
            background: #22c55e;
        }

        .locker-card.s-reserved {
            border-color: #92400e;
        }

        .locker-card.s-reserved::before {
            background: #f59e0b;
        }

        .locker-card.s-open {
            border-color: #075985;
        }

        .locker-card.s-open::before {
            background: #38bdf8;
            box-shadow: 0 0 10px #38bdf8;
        }

        .locker-card.s-closed {
            border-color: #7f1d1d;
        }

        .locker-card.s-closed::before {
            background: #f87171;
        }

        .locker-card.s-maintenance {
            border-color: #374151;
        }

        .locker-card.s-maintenance::before {
            background: #6b7280;
        }

        .locker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .locker-name-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .status-badge-card {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .b-available {
            background: #14532d;
            color: #86efac;
        }

        .b-reserved {
            background: #451a03;
            color: #fde68a;
        }

        .b-open {
            background: #0c4a6e;
            color: #7dd3fc;
        }

        .b-closed {
            background: #450a0a;
            color: #fca5a5;
        }

        .b-maintenance {
            background: #1f2937;
            color: #9ca3af;
        }

        .locker-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .info-row span:first-child {
            color: #6b7280;
        }

        .info-row span:last-child {
            color: #111827;
            font-weight: 500;
        }

        .door-open-txt {
            color: #38bdf8;
        }

        .door-closed-txt {
            color: #94a3b8;
        }

        /* Reservation info box */
        .reservation-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .reservation-box p {
            color: #4b5563;
            margin: 3px 0;
        }

        .reservation-box strong {
            color: #111827;
        }

        /* Progress bar */
        .progress-wrap {
            margin-top: 8px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .progress-bg {
            background: #e5e7eb;
            border-radius: 4px;
            height: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }

        .p-green {
            background: #22c55e;
        }

        .p-yellow {
            background: #f59e0b;
        }

        .p-red {
            background: #f87171;
        }

        /* Action buttons */
        .locker-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            flex: 1;
            min-width: 80px;
            padding: 9px 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            color: white;
            transition: opacity 0.15s;
        }

        .action-btn:hover {
            opacity: 0.85;
        }

        .btn-open {
            background: #16a34a;
        }

        .btn-close {
            background: #dc2626;
        }

        .btn-reset {
            background: #2563eb;
        }

        /* Logs table */
        .logs-section {
            margin-top: 8px;
        }

        .table-wrap {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
        }

        .table-wrap table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-scroll-body {
            max-height: 420px;
            overflow-y: auto;
        }

        .table-header {
            padding: 14px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 11px 16px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
            font-size: 13px;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        tr:hover td {
            background: #f8fafc;
        }

        td {
            color: #374151;
        }

        td:first-child {
            font-family: monospace;
            color: #6b7280;
        }

        td.td-locker {
            color: #2563eb;
            font-weight: 600;
        }

        td.td-user {
            color: #7c3aed;
        }

        @media (max-width: 700px) {
            .lockers-grid {
                grid-template-columns: 1fr;
            }

            .stats-bar {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="container">

        {{-- Hero --}}
        <div class="hero">
            <div>
                <h1 data-pt="Painel de Administração" data-en="Administration Panel">Painel de Administração</h1>
                <p data-pt="Gestão de estados, reservas e histórico dos cacifos"
                    data-en="Manage locker states, reservations and history">
                    Gestão de estados, reservas e histórico dos cacifos
                </p>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="msg msg-success flash-msg" data-original="{{ session('success') }}">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-value stat-total">{{ $lockers->count() }}</div>
                <div class="stat-label" data-pt="Total" data-en="Total">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value stat-available">{{ $lockers->where('status', 'available')->count() }}</div>
                <div class="stat-label" data-pt="Disponíveis" data-en="Available">Disponíveis</div>
            </div>
            <div class="stat-card">
                <div class="stat-value stat-reserved">{{ $lockers->where('status', 'reserved')->count() }}</div>
                <div class="stat-label" data-pt="Reservados" data-en="Reserved">Reservados</div>
            </div>
            <div class="stat-card">
                <div class="stat-value stat-open">{{ $lockers->where('status', 'open')->count() }}</div>
                <div class="stat-label" data-pt="Abertos" data-en="Open">Abertos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value stat-closed">{{ $lockers->where('status', 'closed')->count() }}</div>
                <div class="stat-label" data-pt="Fechados" data-en="Closed">Fechados</div>
            </div>
        </div>


        {{-- ── Gráficos ── --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 28px;">

            {{-- Gráfico 1: Reservas por dia --}}
            <div
                style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="font-size: 13px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;"
                    data-pt="Reservas — Últimos 7 dias" data-en="Reservations — Last 7 days">
                    Reservas — Últimos 7 dias
                </div>
                <canvas id="chartReservations" height="180"></canvas>
            </div>

            {{-- Gráfico 2: Eventos mais frequentes --}}
            <div
                style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="font-size: 13px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;"
                    data-pt="Eventos mais frequentes" data-en="Most frequent events">
                    Eventos mais frequentes
                </div>
                <canvas id="chartEvents" height="180"></canvas>
            </div>

            {{-- Gráfico 3: Distribuição de estados --}}
            <div
                style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="font-size: 13px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;"
                    data-pt="Distribuição de estados" data-en="Status distribution">
                    Distribuição de estados
                </div>
                <canvas id="chartStatus" height="180"></canvas>
            </div>
        </div>

        {{-- Script dos gráficos --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Gráfico 1 — Reservas por dia
                new Chart(document.getElementById('chartReservations'), {
                    type: 'bar',
                    data: {
                        labels: @json($reservationLabels),
                        datasets: [{
                            label: 'Reservas',
                            data: @json($reservationsByDay),
                            backgroundColor: 'rgba(37, 99, 235, 0.7)',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Gráfico 2 — Eventos mais frequentes
                new Chart(document.getElementById('chartEvents'), {
                    type: 'bar',
                    data: {
                        labels: @json($eventLabels),
                        datasets: [{
                            label: 'Total',
                            data: @json($eventData),
                            backgroundColor: [
                                'rgba(34,197,94,0.7)',
                                'rgba(59,130,246,0.7)',
                                'rgba(245,158,11,0.7)',
                                'rgba(239,68,68,0.7)',
                                'rgba(167,139,250,0.7)',
                                'rgba(20,184,166,0.7)',
                            ],
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#6b7280',
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Gráfico 3 — Distribuição de estados (donut)
                const statusData = @json($statusDistribution);
                new Chart(document.getElementById('chartStatus'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Available', 'Reserved', 'Open', 'Closed'],
                        datasets: [{
                            data: [
                                statusData.available,
                                statusData.reserved,
                                statusData.open,
                                statusData.closed,
                            ],
                            backgroundColor: ['#22c55e', '#f59e0b', '#38bdf8', '#f87171'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#6b7280',
                                    font: {
                                        size: 11
                                    },
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            });
        </script>

        {{-- Locker cards --}}
        <div class="section-title" data-pt="Estado dos Cacifos" data-en="Locker Status">Estado dos Cacifos</div>
        <div class="lockers-grid">
            @foreach ($lockers as $locker)
                @php
                    $activeReservation = $locker->reservations->where('status', 'active')->sortByDesc('id')->first();
                    $progressPct = 0;
                    $progressColor = 'p-green';
                    $timeLeft = '';
                    if ($activeReservation && $activeReservation->starts_at && $activeReservation->ends_at) {
                        $total = max(1, $activeReservation->starts_at->diffInSeconds($activeReservation->ends_at));
                        $elapsed = $activeReservation->starts_at->diffInSeconds(now());
                        $progressPct = min(100, round(($elapsed / $total) * 100));
                        $remaining = (int) now()->diffInMinutes($activeReservation->ends_at, false);
                        $progressColor = $remaining <= 2 ? 'p-red' : ($remaining <= 5 ? 'p-yellow' : 'p-green');
                        $timeLeft = max(0, $remaining) . ' min';
                    }
                @endphp
                <div class="locker-card s-{{ $locker->status }}" id="locker-card-{{ $locker->id }}"
                    data-locker-id="{{ $locker->id }}" data-status="{{ $locker->status }}">

                    <div class="locker-header">
                        <div class="locker-name-title locker-name" data-locker-name="{{ $locker->name }}">
                            {{ $locker->name }}
                        </div>
                        <span class="status-badge-card b-{{ $locker->status }} status-badge-el">
                            {{ strtoupper($locker->status) }}
                        </span>
                    </div>

                    <div class="locker-info">
                        <div class="info-row">
                            <span data-pt="Localização" data-en="Location">Localização</span>
                            <span>{{ $locker->location }}</span>
                        </div>
                        <div class="info-row">
                            <span data-pt="Porta" data-en="Door">Porta</span>
                            <span class="{{ $locker->door_open ? 'door-open-txt' : 'door-closed-txt' }}">
                                {{ $locker->door_open ? '🔓' : '🔒' }}
                                @if ($locker->door_open)
                                    <span data-pt="Aberta" data-en="Open">Aberta</span>
                                @else
                                    <span data-pt="Fechada" data-en="Closed">Fechada</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    @if ($activeReservation)
                        <div class="reservation-box">
                            <p>
                                <strong data-pt="Reserva #" data-en="Reservation #">Reserva
                                    #</strong>{{ $activeReservation->id }}
                                — {{ $activeReservation->user->name ?? '—' }}
                            </p>
                            <p>
                                <strong data-pt="De:" data-en="From:">De:</strong>
                                {{ $activeReservation->starts_at?->format('d/m H:i') }}
                                &nbsp;<strong data-pt="Até:" data-en="Until:">Até:</strong>
                                {{ $activeReservation->ends_at?->format('H:i') }}
                            </p>
                            @if ($activeReservation->qr_expires_at)
                                <p>
                                    <strong data-pt="QR expira:" data-en="QR expires:">QR expira:</strong>
                                    {{ $activeReservation->qr_expires_at->format('H:i:s') }}
                                    &nbsp;|&nbsp;
                                    <strong data-pt="Usado:" data-en="Used:">Usado:</strong>
                                    @if ($activeReservation->used)
                                        <span data-pt="Sim" data-en="Yes">Sim</span>
                                    @else
                                        <span data-pt="Não" data-en="No">Não</span>
                                    @endif
                                </p>
                            @endif
                            @if ($timeLeft)
                                <div class="progress-wrap">
                                    <div class="progress-label">
                                        <span data-pt="Tempo restante" data-en="Time remaining">Tempo restante</span>
                                        <span>{{ $timeLeft }}</span>
                                    </div>
                                    <div class="progress-bg">
                                        <div class="progress-fill {{ $progressColor }}"
                                            style="width:{{ $progressPct }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p style="font-size:13px; color:#6b7280; margin-bottom:14px;" data-pt="Sem reserva ativa."
                            data-en="No active reservation.">
                            Sem reserva ativa.
                        </p>
                    @endif

                    <div class="locker-actions">
                        <form method="POST" action="{{ route('admin.lockers.open', $locker->id) }}"
                            style="flex:1;">
                            @csrf
                            <button type="submit" class="action-btn btn-open" style="width:100%" data-pt="Abrir"
                                data-en="Open">Abrir</button>
                        </form>
                        <form method="POST" action="{{ route('admin.lockers.close', $locker->id) }}"
                            style="flex:1;">
                            @csrf
                            <button type="submit" class="action-btn btn-close" style="width:100%" data-pt="Fechar"
                                data-en="Close">Fechar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.lockers.reset', $locker->id) }}"
                            style="flex:1;">
                            @csrf
                            <button type="submit" class="action-btn btn-reset" style="width:100%" data-pt="Repor"
                                data-en="Reset">Repor</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.lockers.index') }}" class="filters">
            <select name="status">
                <option value="" data-pt="Todos os estados" data-en="All statuses">Todos os estados</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="event">
                <option value="" data-pt="Todos os eventos" data-en="All events">Todos os eventos</option>
                <option value="reservation_created">reservation_created</option>
                <option value="reservation_denied_conflict">reservation_denied_conflict</option>
                <option value="qr_refreshed">qr_refreshed</option>
                <option value="qr_expired">qr_expired</option>
                <option value="reservation_started">reservation_started</option>
                <option value="reservation_ended">reservation_ended</option>
                <option value="locker_opened">locker_opened</option>
                <option value="locker_closed">locker_closed</option>
                <option value="locker_reset">locker_reset</option>
            </select>
            <select name="user_id">
                <option value="" data-pt="Todos os utilizadores" data-en="All users">Todos os utilizadores
                </option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn" data-pt="Filtrar" data-en="Filter">Filtrar</button>
        </form>

        {{-- Logs --}}
        <div class="logs-section">
            <div class="table-wrap">
                <div class="table-header">
                    <h2 data-pt="Histórico / Logs" data-en="History / Logs">Histórico / Logs</h2>
                </div>
                <div class="table-scroll-body">

                    <table>
                        <thead>
                            <tr>
                                <th data-pt="Data / Hora" data-en="Date / Time">Data / Hora</th>
                                <th data-pt="Cacifo" data-en="Locker">Cacifo</th>
                                <th data-pt="Evento" data-en="Event">Evento</th>
                                <th data-pt="Descrição" data-en="Description">Descrição</th>
                                <th data-pt="Utilizador" data-en="User">Utilizador</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="td-locker locker-name"
                                        data-locker-name="{{ $log->locker->name ?? '' }}">
                                        {{ $log->locker->name ?? '-' }}
                                    </td>
                                    <td>{{ $log->event }}</td>
                                    <td class="log-description" data-description="{{ $log->description }}">
                                        {{ $log->description }}
                                    </td>
                                    <td class="td-user">{{ $log->user->name ?? 'Sistema' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:20px; color:#9ca3af;"
                                        data-pt="Ainda não existem logs." data-en="No logs yet.">
                                        Ainda não existem logs.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Relógio
        function updateClock() {
            document.getElementById('live-clock').textContent = new Date().toLocaleTimeString('pt-PT');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Polling de estado (a cada 3s)
        const lockerIds = @json($lockers->pluck('id'));
        let lastStates = {};
        document.querySelectorAll('.locker-card').forEach(c => {
            lastStates[c.dataset.lockerId] = c.dataset.status;
        });

        async function pollLockers() {
            for (const id of lockerIds) {
                try {
                    const data = await fetch(`/api/locker/${id}/status`).then(r => r.json());
                    const card = document.getElementById(`locker-card-${id}`);
                    if (!card || lastStates[id] === data.status) continue;

                    // Atualiza classe de cor
                    card.className = card.className.replace(/s-\w+/, `s-${data.status}`);
                    card.dataset.status = data.status;

                    // Atualiza badge
                    const badge = card.querySelector('.status-badge-el');
                    if (badge) {
                        badge.className = `status-badge-card b-${data.status} status-badge-el`;
                        badge.textContent = data.status.toUpperCase();
                    }

                    // Atualiza porta
                    const doorEl = card.querySelector('.door-open-txt, .door-closed-txt');
                    if (doorEl) {
                        doorEl.className = data.door_open ? 'door-open-txt' : 'door-closed-txt';
                        const lang = localStorage.getItem('lang') || 'pt';
                        const doorLabel = data.door_open ?
                            (lang === 'en' ? '🔓 Open' : '🔓 Aberta') :
                            (lang === 'en' ? '🔒 Closed' : '🔒 Fechada');
                        doorEl.textContent = doorLabel;
                    }

                    lastStates[id] = data.status;
                } catch (e) {
                    console.error(e);
                }
            }
        }
        setInterval(pollLockers, 3000);

        // WebSocket Reverb
        if (typeof window.Echo !== 'undefined') {
            lockerIds.forEach(id => {
                window.Echo.channel(`locker.${id}`)
                    .listen('.LockerStatusChanged', (e) => {
                        const card = document.getElementById(`locker-card-${id}`);
                        if (!card) return;
                        card.className = card.className.replace(/s-\w+/, `s-${e.status}`);
                        card.dataset.status = e.status;
                        const badge = card.querySelector('.status-badge-el');
                        if (badge) {
                            badge.className = `status-badge-card b-${e.status} status-badge-el`;
                            badge.textContent = e.status.toUpperCase();
                        }
                        lastStates[id] = e.status;
                    });
            });
        }

        // Aplica língua ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const lang = localStorage.getItem('lang') || 'pt';
            if (lang === 'en' && typeof window._applyFullLang === 'function') window._applyFullLang('en');
        });
    </script>
</body>

</html>
