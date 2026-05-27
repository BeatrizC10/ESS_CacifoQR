<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Cacifos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .container { max-width: 1180px; margin: 0 auto; padding: 30px 20px; }
        .hero {
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            color: white;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p { margin: 0; color: #dbeafe; }
        .msg { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; }
        .success { background: #dcfce7; color: #166534; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: white; border-radius: 18px; padding: 22px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .card h2 { margin-top: 0; color: #0f172a; }
        .status-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 13px;
            margin: 10px 0;
        }
        .status-available { background: #dcfce7; color: #166534; }
        .status-reserved  { background: #fef3c7; color: #92400e; }
        .status-open      { background: #dbeafe; color: #1d4ed8; }
        .status-closed    { background: #fee2e2; color: #b91c1c; }
        .status-maintenance { background: #e5e7eb; color: #374151; }
        .buttons { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        button {
            padding: 10px 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
        .open-btn { background: #16a34a; }
        .close-btn { background: #dc2626; }
        .reset-btn { background: #2563eb; }
        .small { font-size: 13px; color: #6b7280; }
        .table-wrap {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }
        th { background: #f8fafc; }
        select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="container">
    <div class="hero">
        <h1>Painel de Administração</h1>
        <p>Gestão de estados, reservas e histórico dos cacifos</p>
    </div>

    @if(session('success'))
        <div class="msg success">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('lockers.index') }}">Voltar à pagina principal</a>
    </div>

    <form method="GET" action="{{ route('admin.lockers.index') }}" style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">
        <select name="status">
            <option value="">Todos os estados</option>
            <option value="available">Available</option>
            <option value="reserved">Reserved</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
        </select>

        <select name="event">
            <option value="">Todos os eventos</option>
            <option value="reservation_created">reservation_created</option>
            <option value="reservation_denied_conflict">reservation_denied_conflict</option>
            <option value="qr_refreshed">qr_refreshed</option>
            <option value="qr_expired">qr_expired</option>
            <option value="reservation_started">reservation_started</option>
            <option value="reservation_ended">reservation_ended</option>
            <option value="access_denied_outside_time_window">access_denied_outside_time_window</option>
            <option value="locker_opened">locker_opened</option>
            <option value="locker_closed">locker_closed</option>
            <option value="locker_reset">locker_reset</option>
        </select>

        <select name="user_id">
            <option value="">Todos os utilizadores</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="open-btn">Filtrar</button>
    </form>

    <div class="grid">
        @foreach($lockers as $locker)
            <div class="card">
                <h2>{{ $locker->name }}</h2>
                <p><strong>Localização:</strong> {{ $locker->location }}</p>

                <span class="status-badge status-{{ $locker->status }}">
                    {{ strtoupper($locker->status) }}
                </span>

                <p><strong>Porta aberta:</strong> {{ $locker->door_open ? 'Sim' : 'Não' }}</p>
                @php
                    $activeReservation = $locker->reservations->where('status', 'active')->sortByDesc('id')->first();
                @endphp

                @if($activeReservation)
                    <div class="small">
                        <p><strong>Reserva ativa:</strong> #{{ $activeReservation->id }}</p>
                        <p><strong>De:</strong> {{ $activeReservation->starts_at?->format('d/m/Y H:i') }}</p>
                        <p><strong>Até:</strong> {{ $activeReservation->ends_at?->format('d/m/Y H:i') }}</p>
                        <p><strong>QR expira em:</strong> {{ $activeReservation->qr_expires_at?->format('d/m/Y H:i:s') }}</p>
                        <p><strong>Usado:</strong> {{ $activeReservation->used ? 'Sim' : 'Não' }}</p>
                    </div>
                @else
                    <p class="small">Sem reserva ativa.</p>
                @endif

                <div class="buttons">
                    <form method="POST" action="{{ route('admin.lockers.open', $locker->id) }}">
                        @csrf
                        <button type="submit" class="open-btn">Abrir cacifo</button>
                    </form>

                    <form method="POST" action="{{ route('admin.lockers.close', $locker->id) }}">
                        @csrf
                        <button type="submit" class="close-btn">Fechar cacifo</button>
                    </form>

                    <form method="POST" action="{{ route('admin.lockers.reset', $locker->id) }}">
                        @csrf
                        <button type="submit" class="reset-btn">Repor disponível</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <h2>Histórico / Logs</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Cacifo</th>
                    <th>Evento</th>
                    <th>Descrição</th>
                    <th>Utilizador</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->locker->name ?? '-' }}</td>
                        <td>{{ $log->event }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Ainda não existem logs.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
