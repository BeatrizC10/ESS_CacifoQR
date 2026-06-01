<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $locker->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .content { max-width: 1100px; margin: 0 auto; padding: 32px 20px; }
        .hero { background: linear-gradient(135deg, #1d4ed8, #0f172a); color: white; border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p { margin: 0; color: #dbeafe; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: stretch; }
        .panel { background: white; border-radius: 18px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); display: flex; flex-direction: column; }
        .panel h2 { margin-top: 0; color: #0f172a; }
        .status-badge { display: inline-block; padding: 8px 14px; border-radius: 999px; font-weight: bold; font-size: 13px; margin-top: 10px; }
        .status-available { background: #dcfce7; color: #166534; }
        .status-reserved  { background: #fef3c7; color: #92400e; }
        .status-open      { background: #dbeafe; color: #1d4ed8; }
        .status-closed    { background: #fee2e2; color: #b91c1c; }
        .status-maintenance { background: #e5e7eb; color: #374151; }
        .success { color: #166534; font-weight: bold; }
        .error   { color: #b91c1c; font-weight: bold; }
        button.main-btn { padding: 12px 18px; border: none; border-radius: 10px; background: #2563eb; color: white; cursor: pointer; font-size: 15px; font-weight: bold; }
        .small { font-size: 13px; color: #6b7280; word-break: break-all; margin-top: 10px; }
        .meta { line-height: 1.9; color: #374151; }
        input, select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }
        @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="content">
        <div style="margin-bottom: 18px;">
            <a href="{{ route('lockers.index') }}" style="color:#2563eb; text-decoration:none; font-weight:bold;"
               data-pt="← Voltar à página inicial" data-en="← Back to home">← Voltar à página inicial</a>
        </div>

        <div class="hero">
            <h1 class="locker-name" data-locker-name="{{ $locker->name }}">{{ $locker->name }}</h1>
            <p data-pt="Sistema de reserva de cacifo" data-en="Locker reservation system">Sistema de reserva de cacifo</p>
        </div>

        @if (session('success'))
            <p class="success flash-msg" data-original="{{ session('success') }}">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="error flash-msg" data-original="{{ session('error') }}">{{ session('error') }}</p>
        @endif

        <div class="grid">
            <div class="panel">
                <h2 data-pt="Informação do cacifo" data-en="Locker information">Informação do cacifo</h2>

                <div class="meta">
                    <div>
                        <strong data-pt="Nome:" data-en="Name:">Nome:</strong>
                        {{ $locker->name }}
                    </div>
                    <div>
                        <strong data-pt="Localização:" data-en="Location:">Localização:</strong>
                        {{ $locker->location }}
                    </div>
                    <div>
                        <strong data-pt="Porta aberta:" data-en="Door open:">Porta aberta:</strong>
                        @if($locker->door_open)
                            <span data-pt="Sim" data-en="Yes">Sim</span>
                        @else
                            <span data-pt="Não" data-en="No">Não</span>
                        @endif
                    </div>
                </div>

                <div style="text-align: center; margin: 16px 0;">
                    @if ($locker->door_open)
                        <img src="{{ asset('images/cacifo_aberto.png') }}" alt="Aberto" style="max-width: 150px;">
                    @else
                        <img src="{{ asset('images/cacifo_fechado.png') }}" alt="Fechado" style="max-width: 150px;">
                    @endif
                </div>

                <div>
                    <span class="status-badge status-{{ $locker->status }}">
                        {{ strtoupper($locker->status) }}
                    </span>
                </div>

                @auth
                    @if (!$activeReservation && $locker->status === 'available')
                        <form method="POST" action="{{ route('locker.reserve', $locker->id) }}" style="margin-top:20px;">
                            @csrf
                            <div style="margin-bottom:12px;">
                                <label data-pt="Data da reserva" data-en="Reservation date">Data da reserva</label>
                                <input type="date" name="reservation_date" required>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label data-pt="Hora de início" data-en="Start time">Hora de início</label>
                                <input type="time" name="reservation_time" required>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label data-pt="Duração (minutos)" data-en="Duration (minutes)">Duração (minutos)</label>
                                <input type="number" name="duration_minutes" min="1" max="240" required>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label data-pt="Método de Pagamento" data-en="Payment Method">Método de Pagamento</label>
                                <select name="payment_method" required>
                                    <option value="mastercard">Mastercard</option>
                                    <option value="mbway">MB Way</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="visa">VISA</option>
                                </select>
                                <p class="small" data-pt="Tarifa: 0.15€ por minuto." data-en="Rate: €0.15 per minute.">Tarifa: 0.15€ por minuto.</p>
                            </div>
                            <button class="main-btn" type="submit" data-pt="Reservar cacifo" data-en="Reserve locker">Reservar cacifo</button>
                        </form>

                    @elseif($activeReservation)
                        <p style="margin-top:20px;" data-pt="Tens uma reserva ativa para este cacifo." data-en="You have an active reservation for this locker.">
                            Tens uma reserva ativa para este cacifo.
                        </p>
                        <p class="small">
                            {{ $activeReservation->starts_at?->format('d/m/Y H:i') }}
                            —
                            {{ $activeReservation->ends_at?->format('d/m/Y H:i') }}
                        </p>

                        @if (now()->between($activeReservation->starts_at, $activeReservation->ends_at))
                            <div style="display:flex; flex-direction:column; gap:10px; margin-top:15px;">
                                @if (!$locker->door_open)
                                    <form method="POST" action="{{ route('locker.generateQr', $locker->id) }}">
                                        @csrf
                                        <button class="main-btn" type="submit" style="width:100%;"
                                            data-pt="Abrir cacifo (Gerar QR)" data-en="Open locker (Generate QR)">
                                            Abrir cacifo (Gerar QR)
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('locker.close', $locker->id) }}">
                                        @csrf
                                        <button class="main-btn" type="submit" style="background:#4b5563; width:100%;"
                                            data-pt="Fechar cacifo" data-en="Close locker">
                                            Fechar cacifo
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('locker.endReservation', $activeReservation->id) }}" id="form-terminar-reserva">
                                    @csrf
                                    <button class="main-btn" type="button" style="background:#dc2626; width:100%;"
                                        onclick="confirmarTerminarReserva()"
                                        data-pt="Terminar reserva" data-en="End reservation">
                                        Terminar reserva
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="small" data-pt="Os botões ficam disponíveis apenas durante o período da reserva."
                               data-en="Buttons are only available during the reservation period.">
                                Os botões ficam disponíveis apenas durante o período da reserva.
                            </p>
                        @endif

                    @else
                        <p style="margin-top:20px;" data-pt="Cacifo indisponível." data-en="Locker unavailable.">Cacifo indisponível.</p>
                    @endif
                @else
                    <p style="margin-top:20px;" data-pt="Inicia sessão para reservar este cacifo." data-en="Log in to reserve this locker.">
                        Inicia sessão para reservar este cacifo.
                    </p>
                    <a href="{{ route('login') }}">
                        <button class="main-btn" type="button" data-pt="Login" data-en="Login">Login</button>
                    </a>
                @endauth
            </div>

            <div class="panel">
                <h2 data-pt="QR Code" data-en="QR Code">QR Code</h2>

                @auth
                    @if ($activeReservation)
                        @if (now()->lt($activeReservation->starts_at))
                            <p data-pt="A reserva ainda não começou." data-en="The reservation has not started yet.">A reserva ainda não começou.</p>
                        @elseif($activeReservation->qr_token)
                            <div style="text-align:center;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('locker.qr.access', ['token' => $activeReservation->qr_token])) }}"
                                    alt="QR Code" />
                                <p class="small">{{ route('locker.qr.access', ['token' => $activeReservation->qr_token]) }}</p>
                                <p class="small">
                                    QR válido até: {{ $activeReservation->qr_expires_at?->format('d/m/Y H:i:s') }}
                                </p>
                            </div>
                        @else
                            <p data-pt="Ainda não existe QR ativo." data-en="No active QR yet.">Ainda não existe QR ativo.</p>
                        @endif
                    @else
                        <p data-pt="Ainda não existe um QR ativo para este utilizador." data-en="No active QR for this user.">
                            Ainda não existe um QR ativo para este utilizador.
                        </p>
                    @endif
                @else
                    <p data-pt="Inicia sessão para gerar e visualizar o QR Code." data-en="Log in to generate and view the QR Code.">
                        Inicia sessão para gerar e visualizar o QR Code.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    @auth
    <script>
        @if (session('success'))
            Swal.fire({ icon:'success', title:'Sucesso!', text:"{{ session('success') }}", timer:3000, showConfirmButton:false });
        @endif
        @if (session('error'))
            Swal.fire({ icon:'error', title:'Erro!', text:"{{ session('error') }}" });
        @endif

        let portaAtualmenteAberta = {{ $locker->door_open ? 'true' : 'false' }};
        setInterval(function() {
            fetch('/api/locker/{{ $locker->id }}/status')
                .then(r => r.json())
                .then(data => { if (data.door_open !== portaAtualmenteAberta) window.location.reload(); })
                .catch(e => console.error(e));
        }, 2000);

        @if ($activeReservation && now()->between($activeReservation->starts_at, $activeReservation->ends_at))
            setInterval(function() {
                fetch("{{ route('locker.generateQr', $locker->id) }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                }).then(() => window.location.reload());
            }, 60000);
        @endif

        function confirmarTerminarReserva() {
            const lang = localStorage.getItem('lang') || 'pt';
            Swal.fire({
                title: lang === 'en' ? 'End Reservation?' : 'Terminar Reserva?',
                text: lang === 'en' ? "The locker will be freed for other users. Are you sure?" : "O cacifo será libertado para outros alunos. Tens a certeza?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#4b5563',
                confirmButtonText: lang === 'en' ? 'Yes, end it!' : 'Sim, terminar!',
                cancelButtonText: lang === 'en' ? 'Cancel' : 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: lang === 'en' ? 'Freeing locker...' : 'A libertar cacifo...', didOpen: () => Swal.showLoading(), allowOutsideClick: false, showConfirmButton: false });
                    document.getElementById('form-terminar-reserva').submit();
                }
            });
        }

        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('locker.{{ $locker->id }}')
                .listen('.LockerStatusChanged', () => window.location.reload());
        }
    </script>
    @endauth
</body>
</html>
