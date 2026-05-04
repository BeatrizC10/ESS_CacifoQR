<nav style="
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
        <a href="{{ route('lockers.index') }}" style="color: white; text-decoration: none; font-weight: bold; font-size: 18px;">
            ESS Cacifo QR
        </a>

        <a href="{{ route('lockers.index') }}" style="color: #d1d5db; text-decoration: none;">
            Home
        </a>

        @auth
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('admin.lockers.index') }}" style="color: #d1d5db; text-decoration: none;">
                    Painel Admin
                </a>
            @endif
        @endauth
    </div>

    <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
        @auth
            <span style="
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

            <span style="color: #e5e7eb; font-size: 14px;">
                {{ auth()->user()->name }}
            </span>

            <a href="{{ route('profile.edit') }}" style="color: #d1d5db; text-decoration: none;">
                Perfil
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="
                    background: #dc2626;
                    color: white;
                    border: none;
                    padding: 8px 14px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: bold;
                ">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" style="color: #d1d5db; text-decoration: none;">
                Login
            </a>

            <a href="{{ route('register') }}" style="
                background: #2563eb;
                color: white;
                text-decoration: none;
                padding: 8px 14px;
                border-radius: 8px;
                font-weight: bold;
            ">
                Registar
            </a>
        @endauth
    </div>
</nav>
