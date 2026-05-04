<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Validação QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            width: 380px;
            text-align: center;
        }

        .ok { color: green; }
        .no { color: #c62828; }

        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1 class="{{ $success ? 'ok' : 'no' }}">
            {{ $success ? 'Sucesso' : 'Erro' }}
        </h1>

        <p>{{ $message }}</p>

        <a class="button" href="{{ route('locker.show', 1) }}">Voltar ao cacifo</a>
    </div>
</body>
</html>
