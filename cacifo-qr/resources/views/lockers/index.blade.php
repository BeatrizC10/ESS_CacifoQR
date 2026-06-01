<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title data-pt="Home - Cacifos" data-en="Home - Lockers">Home - Cacifos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .container { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }
        .hero { background: linear-gradient(135deg, #1d4ed8, #0f172a); color: white; border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p { margin: 0; color: #dbeafe; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 18px; padding: 22px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; font-weight: bold; font-size: 13px; }
        .available { background: #dcfce7; color: #166534; }
        .reserved  { background: #fef3c7; color: #92400e; }
        .open      { background: #dbeafe; color: #1d4ed8; }
        .closed    { background: #fee2e2; color: #b91c1c; }
        .maintenance { background: #e5e7eb; color: #374151; }
        a.btn { display: inline-block; margin-top: 15px; background: #2563eb; color: white; padding: 10px 14px; border-radius: 10px; text-decoration: none; }
    </style>

    @auth
    <script>
        let estadosPortas = {
            @foreach ($lockers as $locker)
                "{{ $locker->id }}": {{ $locker->door_open ? 'true' : 'false' }},
            @endforeach
        };
        setInterval(function() {
            Object.keys(estadosPortas).forEach(id => {
                fetch(`/api/locker/${id}/status`)
                    .then(r => r.json())
                    .then(data => { if (data.door_open !== estadosPortas[id]) window.location.reload(); })
                    .catch(e => console.error(e));
            });
        }, 2000);
    </script>
    @endauth
</head>
<body>
    @include('partials.navbar')

    <div class="container">
        <div class="hero">
            <h1 data-pt="Home - Cacifos" data-en="Home - Lockers">Home - Cacifos</h1>
            <p data-pt="Escolhe um cacifo disponível e efetua a tua reserva com QR dinâmico"
               data-en="Choose an available locker and make your reservation with a dynamic QR">
                Escolhe um cacifo disponível e efetua a tua reserva com QR dinâmico
            </p>
        </div>

        <div class="grid">
            @foreach ($lockers as $locker)
                <div class="card" id="locker-card-{{ $locker->id }}">
                    <div style="text-align: center; margin-bottom: 20px;">
                        @if ($locker->door_open)
                            <img src="{{ asset('images/cacifo_aberto.png') }}" alt="Cacifo Aberto" style="max-width: 150px;">
                            <h3 style="color: #16a34a;" data-pt="Cacifo Aberto!" data-en="Locker Open!">Cacifo Aberto!</h3>
                        @else
                            <img src="{{ asset('images/cacifo_fechado.png') }}" alt="Cacifo Fechado" style="max-width: 150px;">
                        @endif
                    </div>

                    {{-- Nome do cacifo traduzível --}}
                    <h2 class="locker-name" data-locker-name="{{ $locker->name }}">{{ $locker->name }}</h2>

                    <p>
                        <strong data-pt="Localização:" data-en="Location:">Localização:</strong>
                        {{ $locker->location }}
                    </p>
                    <p>
                        <strong data-pt="Porta aberta:" data-en="Door open:">Porta aberta:</strong>
                        @if($locker->door_open)
                            <span data-pt="Sim" data-en="Yes">Sim</span>
                        @else
                            <span data-pt="Não" data-en="No">Não</span>
                        @endif
                    </p>

                    <span class="badge {{ $locker->status }}">{{ strtoupper($locker->status) }}</span>

                    <br>
                    <a class="btn" href="{{ route('locker.show', $locker->id) }}"
                       data-pt="Ver cacifo" data-en="View locker">Ver cacifo</a>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
