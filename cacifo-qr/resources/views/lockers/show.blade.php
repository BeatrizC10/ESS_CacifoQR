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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            width: 350px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .status {
            margin: 15px 0;
            font-weight: bold;
        }

        .available { color: green; }
        .reserved { color: orange; }
        .open { color: blue; }
        .closed { color: red; }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #3490dc;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #2779bd;
        }

        .msg {
            margin-top: 15px;
            font-weight: bold;
        }

        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="card">
    <h1>{{ $locker->name }}</h1>
    <p>{{ $locker->location }}</p>

    <div class="status {{ $locker->status }}">
        Estado: {{ strtoupper($locker->status) }}
    </div>

    @auth
        @if($locker->status === 'available')
            <form method="POST" action="{{ route('locker.reserve', $locker->id) }}">
                @csrf
                <button type="submit">Reservar Cacifo</button>
            </form>
        @else
            <p>Cacifo indisponível</p>
        @endif
    @else
        <p>Faz login para reservar</p>
        <a href="{{ route('login') }}">
            <button>Login</button>
        </a>
    @endauth

    @if(session('success'))
        <div class="msg success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="msg error">{{ session('error') }}</div>
    @endif
</div>

</body>
</html>
