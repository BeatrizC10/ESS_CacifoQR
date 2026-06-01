<nav
    style="
    background: #111827;
    padding: 14px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    flex-wrap: wrap;
    gap: 12px;
">
    <div style="display: flex; gap: 18px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('lockers.index') }}"
            style="color: white; text-decoration: none; font-weight: bold; font-size: 18px;">
            ESS Cacifo QR
        </a>

        <div style="position: relative; display: inline-block;"
            onmouseenter="this.querySelector('.dropdown').style.display='block'"
            onmouseleave="this.querySelector('.dropdown').style.display='none'">
            <a href="#" style="color: #d1d5db; text-decoration: none;">
                <span data-pt="Cacifos ▾" data-en="Lockers ▾">Cacifos ▾</span>
            </a>
            <div class="dropdown"
                style="
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                background: #1f2937;
                border-radius: 10px;
                padding: 8px;
                padding-top: 16px;
                min-width: 150px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.3);
                z-index: 999;
            ">
                @foreach ($lockers as $locker)
                    <a href="{{ route('locker.show', $locker->id) }}"
                        style="
                        display: block;
                        padding: 8px 12px;
                        color: #d1d5db;
                        text-decoration: none;
                        border-radius: 7px;
                    "
                        onmouseenter="this.style.background='#374151'; this.style.color='white'"
                        onmouseleave="this.style.background='transparent'; this.style.color='#d1d5db'">
                        {{-- Span com o nome original guardado em data-locker-name para tradução --}}
                        <span class="locker-name" data-locker-name="{{ $locker->name }}">{{ $locker->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ route('wallet.index') }}" style="color: #d1d5db; text-decoration: none;">
            <span data-pt="Carteira" data-en="Wallet">Carteira</span>
        </a>

        @auth
            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('admin.lockers.index') }}" style="color: #d1d5db; text-decoration: none;">
                    <span data-pt="Painel Admin" data-en="Admin Panel">Painel Admin</span>
                </a>

            @endif
        @endauth
    </div>

    <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
        @auth
            <span
                style="
                background: {{ auth()->user()?->role === 'admin' ? '#16a34a' : '#2563eb' }};
                color: white;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: bold;
                letter-spacing: 0.5px;
            ">
                {{ auth()->user()?->role === 'admin' ? 'ADMIN' : 'USER' }}
            </span>

            <a href="{{ route('profile.edit') }}" style="color: #e5e7eb; font-size: 14px; text-decoration: none;">
                {{ auth()->user()->name }}
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit"
                    style="
                    background: #dc2626;
                    color: white;
                    border: none;
                    padding: 8px 14px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: bold;
                ">
                    <span data-pt="Logout" data-en="Logout">Logout</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" style="color: #d1d5db; text-decoration: none;">
                <span data-pt="Login" data-en="Login">Login</span>
            </a>
            <a href="{{ route('register') }}"
                style="
                background: #2563eb;
                color: white;
                text-decoration: none;
                padding: 8px 14px;
                border-radius: 8px;
                font-weight: bold;
            ">
                <span data-pt="Registar" data-en="Register">Registar</span>
            </a>
        @endauth

        {{-- Botão PT / EN --}}
        <button id="lang-toggle-btn" onclick="toggleLang()"
            style="
                font-family: monospace;
                font-size: 12px;
                font-weight: bold;
                letter-spacing: 0.05em;
                color: #111827;
                background: #d1fae5;
                border: 2px solid #16a34a;
                border-radius: 6px;
                padding: 5px 14px;
                cursor: pointer;
            "
            onmouseenter="this.style.background='#16a34a'; this.style.color='white';"
            onmouseleave="this.style.background='#d1fae5'; this.style.color='#111827';">EN</button>
    </div>
</nav>

<script>
    (function() {

        // ── Mapa de nomes de cacifos ──────────────────────────────────────
        const lockerNames = {
            pt: {
                'Cacifo 1': 'Cacifo 1',
                'Cacifo 2': 'Cacifo 2',
                'Cacifo 3': 'Cacifo 3',
                'Cacifo 4': 'Cacifo 4'
            },
            en: {
                'Cacifo 1': 'Locker 1',
                'Cacifo 2': 'Locker 2',
                'Cacifo 3': 'Locker 3',
                'Cacifo 4': 'Locker 4'
            }
        };

        // ── Mapa de descrições de logs ────────────────────────────────────
        const logDescriptions = {
            pt: {
                'Cacifo aberto manualmente pelo administrador.': 'Cacifo aberto manualmente pelo administrador.',
                'Cacifo fechado manualmente pelo administrador.': 'Cacifo fechado manualmente pelo administrador.',
                'Cacifo reposto manualmente para disponível.': 'Cacifo reposto manualmente para disponível.',
                'QR validado com sucesso. Cacifo aberto.': 'QR validado com sucesso. Cacifo aberto.',
                'Tentativa de uso de QR expirado.': 'Tentativa de uso de QR expirado.',
                'Reserva terminada por fim do período.': 'Reserva terminada por fim do período.',
                'Reserva terminada antecipadamente pelo utilizador.': 'Reserva terminada antecipadamente pelo utilizador.',
                'Cacifo fechado pelo utilizador e QR removido.': 'Cacifo fechado pelo utilizador e QR removido.',
                'Tentativa de acesso antes do início da reserva.': 'Tentativa de acesso antes do início da reserva.',
                'Reserva terminada automaticamente por expiração do período.': 'Reserva terminada automaticamente por expiração do período.',
                'Abertura confirmada pela simulação/API.': 'Abertura confirmada pela simulação/API.',
            },
            en: {
                'Cacifo aberto manualmente pelo administrador.': 'Locker opened manually by the administrator.',
                'Cacifo fechado manualmente pelo administrador.': 'Locker closed manually by the administrator.',
                'Cacifo reposto manualmente para disponível.': 'Locker manually reset to available.',
                'QR validado com sucesso. Cacifo aberto.': 'QR validated successfully. Locker opened.',
                'Tentativa de uso de QR expirado.': 'Attempt to use an expired QR code.',
                'Reserva terminada por fim do período.': 'Reservation ended at period expiry.',
                'Reserva terminada antecipadamente pelo utilizador.': 'Reservation ended early by the user.',
                'Cacifo fechado pelo utilizador e QR removido.': 'Locker closed by user and QR removed.',
                'Tentativa de acesso antes do início da reserva.': 'Access attempt before reservation start.',
                'Reserva terminada automaticamente por expiração do período.': 'Reservation automatically ended due to period expiry.',
                'Abertura confirmada pela simulação/API.': 'Opening confirmed by simulation/API.',
            }
        };

        // ── Mapa de flash messages do Laravel ────────────────────────────
        const flashMessages = {
            pt: {
                'Cacifo aberto com sucesso.': 'Cacifo aberto com sucesso.',
                'Cacifo fechado com sucesso.': 'Cacifo fechado com sucesso.',
                'Cacifo reposto para disponível.': 'Cacifo reposto para disponível.',
                'Reserva criada com sucesso.': 'Reserva criada com sucesso.',
                'Reserva terminada e cacifo libertado com sucesso.': 'Reserva terminada e cacifo libertado com sucesso.',
                'Cacifo fechado. O QR foi removido.': 'Cacifo fechado. O QR foi removido.',
                'QR atualizado.': 'QR atualizado.',
                'Cartão adicionado com sucesso!': 'Cartão adicionado com sucesso!',
                'Cartão predefinido alterado com sucesso.': 'Cartão predefinido alterado com sucesso.',
            },
            en: {
                'Cacifo aberto com sucesso.': 'Locker opened successfully.',
                'Cacifo fechado com sucesso.': 'Locker closed successfully.',
                'Cacifo reposto para disponível.': 'Locker reset to available.',
                'Reserva criada com sucesso.': 'Reservation created successfully.',
                'Reserva terminada e cacifo libertado com sucesso.': 'Reservation ended and locker freed successfully.',
                'Cacifo fechado. O QR foi removido.': 'Locker closed. QR has been removed.',
                'QR atualizado.': 'QR updated.',
                'Cartão adicionado com sucesso!': 'Card added successfully!',
                'Cartão predefinido alterado com sucesso.': 'Default card changed successfully.',
            }
        };

        // ── Função principal de tradução ──────────────────────────────────
        window._applyFullLang = function(lang) {

            // 1. Elementos data-pt / data-en (interface)
            document.querySelectorAll('[data-pt][data-en]').forEach(el => {
                const val = el.getAttribute('data-' + lang);
                if (val !== null) el.textContent = val;
            });

            // 2. Nomes dos cacifos
            document.querySelectorAll('.locker-name').forEach(el => {
                const original = el.getAttribute('data-locker-name');
                if (!original) return;
                const t = lockerNames[lang]?.[original];
                if (t) el.textContent = t;
            });

            // 3. Descrições dos logs
            document.querySelectorAll('.log-description').forEach(el => {
                const original = el.getAttribute('data-description');
                if (!original) return;
                if (logDescriptions[lang]?.[original]) {
                    el.textContent = logDescriptions[lang][original];
                    return;
                }
                // Textos com datas dinâmicas
                if (lang === 'en') {
                    el.textContent = original
                        .replace(/^Reserva criada de (.+) até (.+)\.$/,
                            'Reservation created from $1 until $2.')
                        .replace(/^Reserva rejeitada por conflito (.+)$/,
                            'Reservation rejected due to conflict: $1')
                        .replace(/^Falha no pagamento de (.+) via (.+)$/,
                            'Payment failure of $1 via $2')
                        .replace(
                            /^Reserva rejeitada porque o utilizador já tem outra reserva nesse período\.$/,
                            'Reservation rejected: user already has a reservation in that period.');
                } else {
                    el.textContent = original;
                }
            });

            // 4. Flash messages (sucesso/erro visíveis na página)
            document.querySelectorAll('.flash-msg').forEach(el => {
                const original = el.getAttribute('data-original');
                if (!original) return;
                const t = flashMessages[lang]?.[original];
                if (t) el.textContent = (el.getAttribute('data-prefix') || '') + t;
            });

            // 5. Atualiza botão
            const btn = document.getElementById('lang-toggle-btn');
            if (btn) btn.textContent = lang === 'pt' ? 'EN' : 'PT';

            localStorage.setItem('lang', lang);
            document.documentElement.lang = lang;
        };

        window.toggleLang = function() {
            const current = localStorage.getItem('lang') || 'pt';
            window._applyFullLang(current === 'pt' ? 'en' : 'pt');
        };

        document.addEventListener('DOMContentLoaded', function() {
            const saved = localStorage.getItem('lang') || 'pt';
            if (saved === 'en') window._applyFullLang('en');
            else {
                const btn = document.getElementById('lang-toggle-btn');
                if (btn) btn.textContent = 'EN';
            }
        });

    })();
</script>
