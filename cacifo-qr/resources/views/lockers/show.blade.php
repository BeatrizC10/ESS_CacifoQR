<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>{{ $locker->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
        }

        .content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .hero {
            background: linear-gradient(135deg, #1d4ed8, #0f172a);
            color: white;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .hero h1 {
            margin: 0 0 8px 0;
            font-size: 30px;
        }

        .hero p {
            margin: 0;
            color: #dbeafe;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: stretch;
        }

        .panel {
            background: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
        }

        .actions-group [ display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: auto;
        padding-top: 20px;

        ] .panel h2 {
            margin-top: 0;
            color: #0f172a;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 10px;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-reserved {
            background: #fef3c7;
            color: #92400e;
        }

        .status-open {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-closed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-maintenance {
            background: #e5e7eb;
            color: #374151;
        }

        .success {
            color: #166534;
            font-weight: bold;
        }

        .error {
            color: #b91c1c;
            font-weight: bold;
        }

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

        .meta {
            line-height: 1.9;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="content">
        <div style="margin-bottom: 18px;">
            <a href="{{ route('lockers.index') }}" style="color:#2563eb; text-decoration:none; font-weight:bold;">
                ← Voltar à página inicial
            </a>
        </div>

        <div class="hero">
            <h1>{{ $locker->name }}</h1>
            <p>Sistema de reserva de cacifo</p>
        </div>

        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <div class="grid">
            <div class="panel">
                <h2>Informação do cacifo</h2>

                <div class="meta">
                    <div><strong>Nome:</strong> {{ $locker->name }}</div>
                    <div><strong>Localização:</strong> {{ $locker->location }}</div>
                    <div><strong>Porta aberta:</strong> {{ $locker->door_open ? 'Sim' : 'Não' }}</div>
                </div>

                <div style="text-align: center; margin-bottom: 20px;">
                    @if ($locker->door_open)
                        <img src="{{ asset('images/cacifo_aberto.png') }}" alt="Aberto" style="max-width: 150px;">
                    @else
                        <img src="{{ asset('images/cacifo_fechado.png') }}" alt="Fechado" style="max-width: 150px;">
                    @endif
                </div>

                <div style="margin-top: 14px;">
                    <span class="status-badge status-{{ $locker->status }}">
                        {{ strtoupper($locker->status) }}
                    </span>
                </div>

                @auth
                    @if (!$activeReservation && $locker->status === 'available')
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

                            <div class="mt-4">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de
                                    Pagamento</label>
                                <select name="payment_method" id="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="mastercard">Mastercard</option>
                                    <option value="mbway">MB Way</option>
                                    <option value="paypal">Paypal</option>
                                    <option value="visa">VISA</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Tarifa: 0.15€ por minuto.</p>
                            </div>

                            <button class="main-btn" type="submit">Reservar cacifo</button>
                        </form>
                    @elseif($activeReservation)
                        <p style="margin-top:20px;">
                            Tens uma reserva ativa para este cacifo.
                        </p>

                        <p class="small">
                            Reserva de {{ $activeReservation->starts_at?->format('d/m/Y H:i') }}
                            até {{ $activeReservation->ends_at?->format('d/m/Y H:i') }}
                        </p>

                        @if (now()->between($activeReservation->starts_at, $activeReservation->ends_at))
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">

                                @if (!$locker->door_open)
                                    {{-- ESTADO: FECHADO - Opções: Abrir (Gerar QR) e Terminar --}}
                                    <form method="POST" action="{{ route('locker.generateQr', $locker->id) }}">
                                        @csrf
                                        <button class="main-btn" type="submit" style="width: 100%;">Abrir cacifo (Gerar
                                            QR)</button>
                                    </form>
                                @else
                                    {{-- ESTADO: ABERTO - Opções: Fechar e Terminar --}}
                                    <form method="POST" action="{{ route('locker.close', $locker->id) }}">
                                        @csrf
                                        <button class="main-btn" type="submit"
                                            style="background:#4b5563; width: 100%;">Fechar cacifo</button>
                                    </form>
                                @endif

                                {{-- Opção Terminar Reserva (Sempre visível se estiver no horário da reserva) --}}
                                <form method="POST" action="{{ route('locker.endReservation', $activeReservation->id) }}"
                                    id="form-terminar-reserva">
                                    @csrf
                                    <button class="main-btn" type="button" style="background:#dc2626; width: 100%;"
                                        onclick="confirmarTerminarReserva()">
                                        Terminar reserva
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="small">Os botões ficam disponíveis apenas durante o período da reserva.</p>
                        @endif
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
                <h2>QR Code</h2>

                @auth
                    @if ($activeReservation)
                        @if (now()->lt($activeReservation->starts_at))
                            <p>A reserva ainda não começou.</p>
                            <p class="small">
                                O QR poderá ser gerado a partir de
                                {{ $activeReservation->starts_at->format('d/m/Y H:i') }}.
                            </p>
                        @elseif($activeReservation->qr_token)
                            <div style="text-align:center;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('locker.qr.access', ['token' => $activeReservation->qr_token])) }}"
                                    alt="QR Code do cacifo" />

                                <p class="small">
                                    {{ route('locker.qr.access', ['token' => $activeReservation->qr_token]) }}
                                </p>

                                <p class="small">
                                    QR válido até: {{ $activeReservation->qr_expires_at?->format('d/m/Y H:i:s') }}
                                </p>
                                <p class="small">
                                    Última atualização da página:
                                    {{ now()->format('H:i:s') }}
                                </p>

                                <p class="small">
                                    Depois de fechares o cacifo, este QR desaparece e terás de gerar um novo.
                                </p>
                            </div>
                        @else
                            <p>Ainda não existe QR ativo.</p>
                            <p class="small">Clica no botão "Gerar QR" para mostrar o QR no sistema.</p>
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
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    borderRadius: '18px'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: "{{ session('error') }}",
                    borderRadius: '18px'
                });
            @endif

            // 1. Polling para verificar estado da porta (já tinhas e é bom manter)
            let portaAtualmenteAberta = {{ $locker->door_open ? 'true' : 'false' }};

            setInterval(function() {
                fetch('/api/locker/{{ $locker->id }}/status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.door_open !== portaAtualmenteAberta) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Erro no status:', error));
            }, 2000);

            // 2. QR Code Dinâmico: Gera um novo de 10 em 10 segundos
            // Apenas se existir uma reserva ativa e o QR estiver visível
            @if ($activeReservation && now()->between($activeReservation->starts_at, $activeReservation->ends_at))
                setInterval(function() {
                    fetch("{{ route('locker.generateQr', $locker->id) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(() => {
                        // Recarrega a página para mostrar o novo QR e atualizar a validade
                        window.location.reload();
                    });
                }, 10000);
            @endif

            // 3. Função de confirmação para terminar reserva
            function confirmarTerminarReserva() {
                Swal.fire({
                    title: 'Terminar Reserva?',
                    text: "O cacifo será libertado para outros alunos. Tens a certeza?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Sim, terminar!',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    borderRadius: '18px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Feedback visual de "A Processar"
                        Swal.fire({
                            title: 'A libertar cacifo...',
                            didOpen: () => {
                                Swal.showLoading()
                            },
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });
                        document.getElementById('form-terminar-reserva').submit();
                    }
                });
            }
        </script>
    @endauth


</body>

</html>
