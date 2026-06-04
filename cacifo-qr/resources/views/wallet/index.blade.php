<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title data-pt="Carteira" data-en="Wallet">Carteira</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .content { max-width: 1100px; margin: 0 auto; padding: 32px 20px; }
        .hero { background: linear-gradient(135deg, #1d4ed8, #0f172a); color: white; border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p { margin: 0; color: #dbeafe; }
        .balance-bubble { background: rgba(255,255,255,0.12); border: 2px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 16px 28px; text-align: center; }
        .balance-bubble .label { font-size: 12px; color: #bfdbfe; text-transform: uppercase; letter-spacing: .05em; }
        .balance-bubble .amount { font-size: 32px; font-weight: bold; color: #fff; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
        @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
        .panel { background: white; border-radius: 18px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 16px; }
        .panel h2 { margin: 0; color: #0f172a; font-size: 18px; }
        .card-list { display: flex; flex-direction: column; gap: 12px; }
        .credit-card { border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; border: 2px solid #e5e7eb; transition: border-color .2s; }
        .credit-card.is-default { border-color: #2563eb; background: #eff6ff; }
        .credit-card-info { display: flex; align-items: center; gap: 14px; }
        .card-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .card-icon.visa { background: #dbeafe; }
        .card-icon.mastercard { background: #fef3c7; }
        .card-details .holder { font-weight: bold; color: #0f172a; font-size: 14px; }
        .card-details .number { color: #6b7280; font-size: 13px; margin-top: 2px; }
        .badge-default { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; background: #dcfce7; color: #166534; white-space: nowrap; }
	.btn-link { background: none; border: none; color: #2563eb; cursor: pointer; font-size: 13px; text-decoration: none; padding: 0; }
        label { display: block; font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 4px; }
        input, select { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box; font-size: 14px; background: #f9fafb; }
        input:focus, select:focus { outline: none; border-color: #2563eb; background: #fff; }
        .form-group { display: flex; flex-direction: column; gap: 12px; }
        .main-btn { padding: 12px 18px; border: none; border-radius: 10px; background: #2563eb; color: white; cursor: pointer; font-size: 15px; font-weight: bold; width: 100%; margin-top: 4px; }
        .main-btn:hover { background: #1d4ed8; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .flash-success { background: #dcfce7; color: #166534; border-radius: 10px; padding: 12px 16px; font-weight: bold; font-size: 14px; }
        .flash-error { background: #fee2e2; color: #b91c1c; border-radius: 10px; padding: 12px 16px; font-weight: bold; font-size: 14px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 4px 0; }
        .empty-state { text-align: center; color: #9ca3af; font-size: 14px; padding: 20px 0; }
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="content">

        {{-- Hero com saldo --}}
        <div class="hero">
            <div>
                <h1>💳 <span data-pt="Carteira" data-en="Wallet">Carteira</span></h1>
                <p data-pt="Gere os teus métodos de pagamento e carrega saldo"
                   data-en="Manage your payment methods and top up balance">
                    Gere os teus métodos de pagamento e carrega saldo
                </p>
            </div>
            <div class="balance-bubble">
                <div class="label" data-pt="Saldo disponível" data-en="Available balance">Saldo disponível</div>
                <div class="amount">{{ number_format($user->wallet_balance, 2, ',', '.') }}€</div>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flash-success" style="margin-bottom: 20px;">✅ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error" style="margin-bottom: 20px;">❌ {{ session('error') }}</div>
        @endif

        <div class="grid" style="align-items: stretch">

            {{-- Coluna esquerda --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">

                {{-- Carregar saldo --}}
                <div class="panel">
                    <h2 data-pt="Carregar saldo" data-en="Top up balance">Carregar saldo</h2>
                    <hr class="divider">

                    <form method="POST" action="{{ route('wallet.topup') }}">
                        @csrf
                        <div class="form-group">

                            <div>
                                <label for="amount" data-pt="Montante (€)" data-en="Amount (€)">Montante (€)</label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                    placeholder="Ex: 5.00" required>
                                <p class="hint" data-pt="Valor mínimo: 0.01€" data-en="Minimum value: €0.01">Valor mínimo: 0.01€</p>
                            </div>

                            <div>
                                <label for="method" data-pt="Método de pagamento" data-en="Payment method">Método de pagamento</label>
                                <select id="method" name="method" required onchange="toggleSavedCard(this.value)">
                                    <option value="" data-pt="— Selecionar —" data-en="— Select —">— Selecionar —</option>
                                    <option value="mbway">MB Way</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="visa">VISA</option>
                                    <option value="mastercard">Mastercard</option>
                                    @if ($cards->isNotEmpty())
                                        <option value="saved_card" data-pt="💳 Cartão guardado" data-en="💳 Saved card">💳 Cartão guardado</option>
                                    @endif
                                </select>

                                {{-- MB Way --}}
                                <div id="field-mbway" style="display:none; margin-top:10px;">
                                    <label for="mbway_phone" data-pt="Número de telemóvel" data-en="Phone number">Número de telemóvel</label>
                                    <input type="tel" id="mbway_phone" name="mbway_phone" placeholder="9XXXXXXXX" maxlength="9">
                                </div>

                                {{-- PayPal --}}
                                <div id="field-paypal" style="display:none; margin-top:10px;">
                                    <label for="paypal_email" data-pt="Email do PayPal" data-en="PayPal email">Email do PayPal</label>
                                    <input type="email" id="paypal_email" name="paypal_email" placeholder="exemplo@email.com">
                                </div>

                                {{-- VISA / Mastercard --}}
                                <div id="field-card" style="display:none; margin-top:10px;">
                                    <label for="card_number_topup" data-pt="Número do cartão" data-en="Card number">Número do cartão</label>
                                    <input type="text" id="card_number_topup" name="card_number_topup" placeholder="16 dígitos" maxlength="16">
                                </div>
                            </div>

                            {{-- Cartão guardado --}}
                            <div id="saved-card-field" style="display:none;">
                                <label for="saved_card_id" data-pt="Escolher cartão" data-en="Choose card">Escolher cartão</label>
                                <select id="saved_card_id" name="saved_card_id">
                                    <option value="" data-pt="— Selecionar cartão —" data-en="— Select card —">— Selecionar cartão —</option>
                                    @foreach ($cards as $card)
                                        <option value="{{ $card->id }}" {{ $card->is_default ? 'selected' : '' }}>
                                            {{ strtoupper($card->brand) }} ···· {{ $card->last_four }}
                                            {{ $card->is_default ? '⭐' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="main-btn" type="submit"
                                data-pt="Carregar saldo" data-en="Top up balance">
                                Carregar saldo
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Coluna direita --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">

                {{-- Cartões guardados --}}
                <div class="panel" style="height: 100%; box-sizing: border-box;">
                    <h2 data-pt="Cartões guardados" data-en="Saved cards">Cartões guardados</h2>
                    <hr class="divider">

                    @if ($cards->isEmpty())
                        <div class="empty-state">
                            <p data-pt="Ainda não tens cartões guardados." data-en="You have no saved cards yet.">
                                Ainda não tens cartões guardados.
                            </p>
                        </div>
                    @else
                        <div class="card-list">
                            @foreach ($cards as $card)
                                <div class="credit-card {{ $card->is_default ? 'is-default' : '' }}">
                                    <div class="credit-card-info">
                                        <div class="card-icon {{ $card->brand }}">
                                            @if ($card->brand === 'visa') 💙 @else 🟠 @endif
                                        </div>
                                        <div class="card-details">
                                            <div class="holder">{{ $card->card_holder }}</div>
                                            <div class="number">{{ strtoupper($card->brand) }} ···· {{ $card->last_four }}</div>
                                        </div>
                                    </div>

                                    <div style="display:flex; flex-direction:row; align-items:flex-end; gap:8px;">
                                        @if ($card->is_default)
                                            <span class="badge-default"
                                                data-pt="⭐ Predefinido" data-en="⭐ Default">
                                                ⭐ Predefinido
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('wallet.card.default', $card->id) }}">
                                                @csrf
						<button class="btn-link" type="submit"
                                                style="font-size:18px; padding:4px;"
                                                title="Definir como padrão"
                                                data-pt="Definir como padrão" data-en="Set as default">
                                                ⭐
                                            </button>
                                            </form>
                                        @endif
<form method="POST" action="{{ route('wallet.card.edit', $card->id) }}"
                                        id="edit-form-{{ $card->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" id="edit-holder-{{ $card->id }}" name="card_holder" value="{{ $card->card_holder }}">
                                        <input type="hidden" id="edit-last-four-{{ $card->id }}" name="last_four" value="{{ $card->last_four }}">
                                        <input type="hidden" id="edit-mbway-{{ $card->id }}" name="mbway_phone" value="{{ $card->mbway_phone }}">
                                        <input type="hidden" id="edit-paypal-{{ $card->id }}" name="paypal_email" value="{{ $card->paypal_email }}">
                                        <button type="button"
                                            onclick="editCard({{ $card->id }}, '{{ $card->card_holder }}', '{{ $card->brand }}', '{{ $card->last_four }}', '{{ $card->mbway_phone }}', '{{ $card->paypal_email }}')"
                                            style="background:none; border:none; cursor:pointer; color:#2563eb; font-size:18px; padding:4px;"
                                            title="Editar cartão">
                                            ✏️
                                        </button>
                                    </form>
					<form method="POST" action="{{ route('wallet.card.delete', $card->id) }}"
                                       		id="delete-form-{{ $card->id }}">
                                        	@csrf
                                        	@method('DELETE')
                                        	<button type="button"
                                        		onclick="confirmDelete({{ $card->id }})"
                                            		style="background:none; border:none; cursor:pointer; color:#dc2626; font-size:18px; padding:4px;"
                                            		title="Eliminar cartão">
                                            		🗑️
                                        	</button>
                                    	</form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Adicionar cartão --}}
        <div class="panel" style="margin-top: 24px">
            <h2 data-pt="Adicionar cartão" data-en="Add card">Adicionar cartão</h2>
            <hr class="divider">

            <form method="POST" action="{{ route('wallet.card.add') }}">
                @csrf
                <div class="form-group">

                    <div>
                        <label for="card_holder" data-pt="Nome no cartão" data-en="Cardholder name">Nome no cartão</label>
                        <input type="text" id="card_holder" name="card_holder"
                            placeholder="Ex: João Silva" value="{{ old('card_holder') }}" required>
                        @error('card_holder')
                            <p class="hint" style="color:#b91c1c;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="brand" data-pt="Tipo" data-en="Type">Tipo</label>
                        <select id="brand" name="brand" required onchange="toggleAddCardFields(this.value)">
                            <option value="" data-pt="— Selecionar —" data-en="— Select —">— Selecionar —</option>
                            <option value="visa">VISA</option>
                            <option value="mastercard">Mastercard</option>
                            <option value="mbway">MB Way</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>

                    <div id="add-card-fields" style="display:none;">
                        <label data-pt="Número do cartão" data-en="Card number">Número do cartão</label>
                        <input type="text" name="card_number" placeholder="16 dígitos" maxlength="16">
                    </div>

                    <div id="add-mbway-field" style="display:none;">
                        <label data-pt="Número de telemóvel" data-en="Phone number">Número de telemóvel</label>
                        <input type="tel" name="mbway_phone" placeholder="9XXXXXXXX" maxlength="9">
                    </div>

                    <div id="add-paypal-field" style="display:none;">
                        <label data-pt="Email do PayPal" data-en="PayPal email">Email do PayPal</label>
                        <input type="email" name="paypal_email" placeholder="exemplo@email.com">
                    </div>

                    <button class="main-btn" type="submit"
                        data-pt="Guardar cartão" data-en="Save card">
                        Guardar cartão
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCard(id, holder, brand, lastFour, mbwayPhone, paypalEmail) {
            let extraField = '';
            if (brand === 'visa' || brand === 'mastercard') {
                extraField = `<div style="margin-top:10px;">
                    <label style="font-size:13px;font-weight:bold;">Confirmar últimos 4 dígitos atuais</label>
                    <input id="swal-confirm-four" class="swal2-input" maxlength="4" placeholder="XXXX">
                    <p style="font-size:11px;color:#6b7280;margin-top:4px;">Por segurança, confirma os últimos 4 dígitos do cartão atual.</p>
                    <div id="swal-confirm-error" style="color:#dc2626;font-size:12px;display:none;margin-top:4px;">❌ Dígitos incorretos!</div>
                </div>
                <div style="margin-top:10px;">
                    <label style="font-size:13px;font-weight:bold;">Novo número do cartão (16 dígitos)</label>
                    <input id="swal-new-card" class="swal2-input" maxlength="16" placeholder="XXXXXXXXXXXXXXXX">
                </div>`;
            } else if (brand === 'mbway') {
                extraField = `<div style="margin-top:10px;">
                    <label style="font-size:13px;font-weight:bold;">Número de telemóvel</label>
                    <input id="swal-mbway" class="swal2-input" maxlength="9" placeholder="9XXXXXXXX" value="${mbwayPhone}">
                </div>`;
            } else if (brand === 'paypal') {
                extraField = `<div style="margin-top:10px;">
                    <label style="font-size:13px;font-weight:bold;">Email do PayPal</label>
                    <input id="swal-paypal" class="swal2-input" type="email" placeholder="exemplo@email.com" value="${paypalEmail}">
                </div>`;
            }

            Swal.fire({
                title: 'Editar cartão',
                html: `
                    <div style="text-align:left;">
                        <label style="font-size:13px;font-weight:bold;">Nome no cartão</label>
                        <input id="swal-holder" class="swal2-input" placeholder="Nome" value="${holder}">
                        ${extraField}
                    </div>`,
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    if (brand === 'visa' || brand === 'mastercard') {
                        const confirmFour = document.getElementById('swal-confirm-four').value;
                        const newCard = document.getElementById('swal-new-card').value;
                        if (confirmFour !== lastFour) {
                            document.getElementById('swal-confirm-error').style.display = 'block';
                            return false;
                        }
                        if (newCard.length !== 16) {
                            Swal.showValidationMessage('O novo número deve ter 16 dígitos!');
                            return false;
                        }
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('edit-holder-' + id).value = document.getElementById('swal-holder').value;
                    if (brand === 'visa' || brand === 'mastercard') {
                        const newCard = document.getElementById('swal-new-card').value;
                        document.getElementById('edit-last-four-' + id).value = newCard.slice(-4);
                    } else if (brand === 'mbway') {
                        document.getElementById('edit-mbway-' + id).value = document.getElementById('swal-mbway').value;
                    } else if (brand === 'paypal') {
                        document.getElementById('edit-paypal-' + id).value = document.getElementById('swal-paypal').value;
                    }
                    document.getElementById('edit-form-' + id).submit();
                }
            });
        }
	function confirmDelete(id) {
            Swal.fire({
                title: 'Eliminar cartão?',
                text: 'Tens a certeza que queres eliminar este cartão?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, eliminar!',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function toggleSavedCard(value) {
            document.getElementById('saved-card-field').style.display = 'none';
            document.getElementById('field-mbway').style.display = 'none';
            document.getElementById('field-paypal').style.display = 'none';
            document.getElementById('field-card').style.display = 'none';

            if (value === 'saved_card')   document.getElementById('saved-card-field').style.display = 'block';
            else if (value === 'mbway')   document.getElementById('field-mbway').style.display = 'block';
            else if (value === 'paypal')  document.getElementById('field-paypal').style.display = 'block';
            else if (value === 'visa' || value === 'mastercard') document.getElementById('field-card').style.display = 'block';
        }

        function toggleAddCardFields(value) {
            document.getElementById('add-card-fields').style.display  = (value === 'visa' || value === 'mastercard') ? 'block' : 'none';
            document.getElementById('add-mbway-field').style.display  = value === 'mbway'   ? 'block' : 'none';
            document.getElementById('add-paypal-field').style.display = value === 'paypal'  ? 'block' : 'none';
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: localStorage.getItem('lang') === 'en' ? 'Success!' : 'Sucesso!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: localStorage.getItem('lang') === 'en' ? 'Error!' : 'Erro!',
                text: "{{ session('error') }}",
            });
        @endif
    </script>
</body>

</html>
