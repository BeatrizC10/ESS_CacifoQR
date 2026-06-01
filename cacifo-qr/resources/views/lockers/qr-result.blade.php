<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title data-pt="Validação QR" data-en="QR Validation">Validação QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); width: 380px; text-align: center; }
        .ok { color: green; }
        .no { color: #c62828; }
        a.button { display: inline-block; margin-top: 20px; padding: 12px 18px; background: #2563eb; color: white; text-decoration: none; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <h1 class="{{ $success ? 'ok' : 'no' }}">
            @if($success)
                <span data-pt="Sucesso" data-en="Success">Sucesso</span>
            @else
                <span data-pt="Erro" data-en="Error">Erro</span>
            @endif
        </h1>
        <p>{{ $message }}</p>
        <a class="button" href="{{ route('locker.show', 1) }}"
           data-pt="Voltar ao cacifo" data-en="Back to locker">Voltar ao cacifo</a>
    </div>

    {{-- Aplica a língua guardada --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lang = localStorage.getItem('lang') || 'pt';
            if (lang === 'en') {
                document.querySelectorAll('[data-pt][data-en]').forEach(el => {
                    el.textContent = el.getAttribute('data-en');
                });
            }
        });
    </script>
</body>
</html>
