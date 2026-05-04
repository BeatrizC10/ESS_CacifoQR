<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $locker->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .content { max-width: 1100px; margin: 0 auto; padding: 32px 20px; }
        .hero {
            background: linear-gradient(135deg, #1d4ed8, #0f172a);
            color: white;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p { margin: 0; color: #dbeafe; }
        .grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 24px; }
        .panel {
            background: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .panel h2 { margin-top: 0; color: #0f172a; }
        .status-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 10px;
        }
        .status-available { background: #dcfce7; color: #166534; }
        .status-reserved  { background: #fef3c7; color: #92400e; }
        .status-open      { background: #dbeafe; color: #1d4ed8; }
        .status-closed    { background: #fee2e2; color: #b91c1c; }
        .status-maintenance { background: #e5e7eb; color: #374151; }
        .success { color: #166534; font-weight: bold; }
        .error { color: #b91c1c; font-weight: bold; }
        button.main-btn {
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }
        .small {
            font-size: 13px;
            color: #6b7280;
            word-break: break-all;
            margin-top: 10px;
        }
        .meta { line-height: 1.9; color: #374151; }
        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="content">
    <div style="margin-bottom: 18px;">
        <a href="{{ route('lockers.index') }}" style="color:#2563eb; text-decoration:none; font-weight:bold;">
            ← Voltar à home dos cacifos
        </a>
    </div>

    <div class="hero">
        <h1>{{ $locker->name }}</h1>
        <p>Sistema de reserva e acesso com QR Code dinâmico</p>
    </div>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <div class="grid">
        <div class="panel">
            <h2>Informação do cacifo</h2>

            <div class="meta">
                <div><strong>Nome:</strong> {{ $locker->name }}</div>
                <div><strong>Localização:</strong> {{ $locker->location }}</div>
                <div><strong>Porta aberta:</strong> {{ $locker->door_open ? 'Sim' : 'Não' }}</div>
                <div><strong>Comando de abertura:</strong> {{ $locker->open_command ? 'Sim' : 'Não' }}</div>
            </div>

            <div style="margin-top: 14px;">
                <span class="status-badge status-{{ $locker->status }}">
                    {{ strtoupper($locker->status) }}
                </span>
            </div>

            @auth
                @if(!$activeReservation && $locker->status === 'available')
                    <form method="POST" action="{{ route('locker.reserve', $locker->id) }}" style="margin-top:20px;">
                        @csrf

                        <div style="text-align:left; margin-bottom:12px;">
                            <label>Data da reserva</label><br>
                            <input type="date" name="reservation_date" required>
                        </div>

                        <div style="text-align:left; margin-bottom:12px;">
                            <label>Hora de início</label><br>
                            <input type="time" name="reservation_time" required>
                        </div>

                        <div style="text-align:left; margin-bottom:12px;">
                            <label>Duração (minutos)</label><br>
                            <input type="number" name="duration_minutes" min="1" max="240" required>
                        </div>

                        <button class="main-btn" type="submit">Gerar QR e reservar cacifo</button>
                    </form>
                @elseif($activeReservation)
                    <p style="margin-top:20px;">
                        Tens uma reserva ativa para este cacifo. Utiliza o QR Code para abrir o cacifo durante o período da reserva.
                    </p>
                    <p class="small">
                        Reserva de {{ $activeReservation->starts_at?->format('d/m/Y H:i') }} até {{ $activeReservation->ends_at?->format('d/m/Y H:i') }}
                    </p>
                @else
                    <p style="margin-top: 20px;">Cacifo indisponível.</p>
                @endif
            @else
                <p style="margin-top:20px;">Inicia sessão para reservar este cacifo.</p>
                <a href="{{ route('login') }}">
                    <button class="main-btn" type="button">Login</button>
                </a>
            @endauth
        </div>

        <div class="panel">
            <h2>QR Code dinâmico</h2>

            @auth
                @if($activeReservation)
                    @if(now()->lt($activeReservation->starts_at))
                        <p>A reserva ainda não começou.</p>
                        <p class="small">
                            O QR ficará disponível a partir de {{ $activeReservation->starts_at->format('d/m/Y H:i') }}.
                        </p>
                    @elseif($activeReservation->qr_token)
                        <div style="text-align:center;">
                            <img
                                src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('locker.qr.access', ['token' => $activeReservation->qr_token])) }}"
                                alt="QR Code do cacifo"
                            />

                            <p class="small">
                                {{ route('locker.qr.access', ['token' => $activeReservation->qr_token]) }}
                            </p>

                            <p class="small">
                                QR válido até: {{ $activeReservation->qr_expires_at?->format('d/m/Y H:i:s') }}
                            </p>

                            <p class="small">
                                A página atualiza automaticamente a cada 60 segundos para renovar o QR.
                            </p>

                            @if($activeReservation->qr_expires_at && now()->greaterThan($activeReservation->qr_expires_at))
                                <p class="error">Este QR expirou. A página será atualizada para gerar o novo QR.</p>
                            @endif
                        </div>
                    @else
                        <p>O QR ainda não está disponível.</p>
                    @endif
                @else
                    <p>Ainda não existe um QR ativo para este utilizador.</p>
                @endif
            @else
                <p>Inicia sessão para gerar e visualizar o QR Code.</p>
            @endauth
        </div>
    </div>
</div>

@auth
    @if($activeReservation)
        <script>
            setTimeout(function () {
                window.location.reload();
            }, 60000);
        </script>
    @endif
@endauth
</body>
</html>o do
