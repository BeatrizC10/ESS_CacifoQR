<!DOCTYPE html>
<html lang="pt">
<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Cacifos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

<<<<<<< Updated upstream
        h1, h2 {
=======
        h1,
        h2 {
>>>>>>> Stashed changes
            color: #1f2937;
        }

        .msg {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
<<<<<<< Updated upstream
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
=======
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
>>>>>>> Stashed changes
        }

        .status {
            font-weight: bold;
            margin: 10px 0;
        }

        .buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        button {
            padding: 10px 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }

<<<<<<< Updated upstream
        .open-btn {
            background: #16a34a;
        }

=======
>>>>>>> Stashed changes
        .close-btn {
            background: #dc2626;
        }

        .reset-btn {
            background: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 14px;
            overflow: hidden;
<<<<<<< Updated upstream
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }

        th, td {
=======
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        th,
        td {
>>>>>>> Stashed changes
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
        }

        .small {
            font-size: 13px;
            color: #6b7280;
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<<<<<<< Updated upstream
<body>
@include('partials.navbar')

<div class="container">
    <h1>Painel de Administração dos Cacifos</h1>

    @if(session('success'))
        <div class="msg success">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('locker.show', 1) }}">Ir para Cacifo 1</a>
    </div>

    <div class="grid">
        @foreach($lockers as $locker)
            <div class="card">
                <h2>{{ $locker->name }}</h2>
                <p><strong>Localização:</strong> {{ $locker->location }}</p>
                <p class="status">Estado: {{ strtoupper($locker->status) }}</p>
                <p><strong>Porta aberta:</strong> {{ $locker->door_open ? 'Sim' : 'Não' }}</p>
                <p><strong>Comando de abertura:</strong> {{ $locker->open_command ? 'Sim' : 'Não' }}</p>

                @php
                    $activeReservation = $locker->reservations->where('status', 'active')->sortByDesc('id')->first();
                @endphp

                @if($activeReservation)
                    <div class="small">
                        <p><strong>Reserva ativa:</strong> #{{ $activeReservation->id }}</p>
                        <p><strong>QR expira em:</strong> {{ optional($activeReservation->qr_expires_at)->format('d/m/Y H:i:s') }}</p>
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
</body>
=======

<body>
    @include('partials.navbar')

    <div class="container">
        <h1>Painel de Administração dos Cacifos</h1>

        @if (session('success'))
            <div class="msg success">{{ session('success') }}</div>
        @endif

        <div style="margin-bottom: 20px;">
            <a href="{{ route('locker.show', 1) }}">Ir para Cacifo 1</a>
        </div>

        <div class="grid">
            @foreach ($lockers as $locker)
                <div class="card">
                    <h2>{{ $locker->name }}</h2>
                    <p><strong>Localização:</strong> {{ $locker->location }}</p>
                    <p class="status">Estado: {{ strtoupper($locker->status) }}</p>
                    <p><strong>Porta aberta:</strong> {{ $locker->door_open ? 'Sim' : 'Não' }}</p>
                    <p><strong>Comando de abertura:</strong> {{ $locker->open_command ? 'Sim' : 'Não' }}</p>

                    @php
                        $activeReservation = $locker->reservations
                            ->where('status', 'active')
                            ->sortByDesc('id')
                            ->first();
                    @endphp

                    @if ($activeReservation)
                        <div class="small">
                            <p><strong>Reserva ativa:</strong> #{{ $activeReservation->id }}</p>
                            <p><strong>QR expira em:</strong>
                                {{ optional($activeReservation->qr_expires_at)->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Usado:</strong> {{ $activeReservation->used ? 'Sim' : 'Não' }}</p>
                        </div>
                    @else
                        <p class="small">Sem reserva ativa.</p>
                    @endif

                    <div class="buttons">
                        <form method="POST" action="{{ route('admin.lockers.open', $locker->id) }}">
                            @csrf
                            <button type="submit" style="background:#16a34a;">Abrir cacifo</button>
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
</body>

>>>>>>> Stashed changes
</html>
