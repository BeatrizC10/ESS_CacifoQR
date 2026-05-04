<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $locker->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
        }

        .page {
            min-height: 100vh;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
<<<<<<< Updated upstream
            width: 460px;
=======
            width: 420px;
>>>>>>> Stashed changes
            text-align: center;
        }

        .status {
            font-weight: bold;
            margin: 10px 0 20px;
<<<<<<< Updated upstream
        }

        .success {
            color: green;
        }

        .error {
            color: #c62828;
        }

        button.main-btn {
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-size: 15px;
        }

        .qr-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fafafa;
        }

        .small {
            font-size: 13px;
            color: #555;
            word-break: break-all;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
=======
        }

        .success { color: green; }
        .error { color: #c62828; }

        button {
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-size: 15px;
        }

        .qr-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fafafa;
        }

        .small {
            font-size: 13px;
            color: #555;
            word-break: break-all;
>>>>>>> Stashed changes
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="page">
    @include('partials.navbar')

    <div class="content">
        <div class="card">
            <h1>{{ $locker->name }}</h1>
            <p>{{ $locker->location }}</p>

            <div class="status">Estado: {{ strtoupper($locker->status) }}</div>

            @if(session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif

            @if(session('error'))
                <p class="error">{{ session('error') }}</p>
            @endif

            @auth
                @if(!$activeReservation && $locker->status === 'available')
                    <form method="POST" action="{{ route('locker.reserve', $locker->id) }}">
                        @csrf
<<<<<<< Updated upstream
                        <button class="main-btn" type="submit">Gerar QR e reservar cacifo</button>
                    </form>

                @elseif($activeReservation && $activeReservation->qr_token)
=======
                        <button type="submit">Gerar QR e reservar cacifo</button>
                    </form>
                @elseif($activeReservation)
>>>>>>> Stashed changes
                    <div class="qr-box">
                        <h3>QR Code dinâmico</h3>

                        <img
<<<<<<< Updated upstream
                            src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('locker.qr.access', ['token' => $activeReservation->qr_token])) }}"
=======
                            src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('locker.qr.access', $activeReservation->qr_token)) }}"
>>>>>>> Stashed changes
                            alt="QR Code do cacifo"
                        />

                        <p class="small">
<<<<<<< Updated upstream
                            {{ route('locker.qr.access', ['token' => $activeReservation->qr_token]) }}
=======
                            {{ route('locker.qr.access', $activeReservation->qr_token) }}
>>>>>>> Stashed changes
                        </p>

                        <p class="small">
                            Válido até: {{ $activeReservation->qr_expires_at?->format('d/m/Y H:i:s') }}
                        </p>
<<<<<<< Updated upstream

                        <div class="action-buttons">
                            <form method="POST" action="{{ route('locker.open', $locker->id) }}">
                                @csrf
                                <button class="main-btn" type="submit" style="background:#16a34a;">Abrir cacifo</button>
                            </form>

                            <form method="POST" action="{{ route('locker.close', $locker->id) }}">
                                @csrf
                                <button class="main-btn" type="submit" style="background:#dc2626;">Fechar cacifo</button>
                            </form>
                        </div>
                    </div>

                @elseif($activeReservation)
                    <p>Existe uma reserva ativa, mas o QR ainda não está disponível.</p>

=======
                    </div>
>>>>>>> Stashed changes
                @else
                    <p>Cacifo indisponível.</p>
                @endif
            @else
                <p>Faz login para reservar.</p>
                <br>
                <a href="{{ route('login') }}">
<<<<<<< Updated upstream
                    <button class="main-btn" type="button">Login</button>
=======
                    <button>Login</button>
>>>>>>> Stashed changes
                </a>
            @endauth
        </div>
    </div>
</div>
</body>
</html>
