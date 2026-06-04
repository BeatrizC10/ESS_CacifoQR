<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; margin: 0; }
        .content { max-width: 800px; margin: 0 auto; padding: 32px 20px; }

        .hero {
            background: linear-gradient(135deg, #1d4ed8, #0f172a);
            color: white; border-radius: 20px; padding: 28px;
            margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .hero h1 { margin: 0 0 8px 0; font-size: 30px; }
        .hero p  { margin: 0; color: #dbeafe; }

        .panel {
            background: white; border-radius: 18px; padding: 28px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06); margin-bottom: 24px;
        }
        .panel-title {
            font-size: 18px; font-weight: bold; color: #0f172a;
            margin: 0 0 4px 0;
        }
        .panel-desc {
            font-size: 13px; color: #6b7280; margin: 0 0 20px 0;
            padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;
        }

        /* Override Tailwind inputs */
        .panel input[type=text],
        .panel input[type=email],
        .panel input[type=password] {
            width: 100% !important;
            padding: 10px 14px !important;
            border-radius: 10px !important;
            border: 1px solid #d1d5db !important;
            box-sizing: border-box !important;
            font-size: 15px !important;
            background: #f9fafb !important;
            margin-top: 4px !important;
        }
        .panel input:focus {
            outline: none !important;
            border-color: #2563eb !important;
            background: white !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important;
        }
        .panel label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #374151 !important;
        }

        /* Override buttons */
        .panel button[type=submit],
        .panel .btn-primary {
            padding: 10px 22px !important;
            border-radius: 10px !important;
            background: #2563eb !important;
            color: white !important;
            font-weight: bold !important;
            font-size: 14px !important;
            border: none !important;
            cursor: pointer !important;
        }
        .panel button[type=submit]:hover { background: #1d4ed8 !important; }

        /* Danger zone */
        .danger-panel {
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 18px; padding: 28px;
            margin-bottom: 24px;
        }
        .danger-panel button {
            padding: 10px 22px !important;
            border-radius: 10px !important;
            background: #dc2626 !important;
            color: white !important;
            font-weight: bold !important;
            border: none !important;
            cursor: pointer !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

            <h1>👤 <span data-pt="O meu perfil" data-en="My profile">O meu perfil</span></h1>
            <p data-pt="Gere as tuas informações pessoais e segurança da conta"
               data-en="Manage your personal information and account security">
                Gere as tuas informações pessoais e segurança da conta
            </p>
        </div>

	{{-- Informações pessoais --}}
        <div class="panel">
            <p class="panel-title" data-pt="Informações pessoais" data-en="Personal information">Informações pessoais</p>
            <p class="panel-desc" data-pt="Atualiza o teu nome e endereço de email." data-en="Update your name and email address.">Atualiza o teu nome e endereço de email.</p>
            @include('profile.partials.update-profile-information-form')
        </div>

	{{-- Password --}}
        <div class="panel">
            <p class="panel-title" data-pt="🔒 Alterar password" data-en="🔒 Change password">🔒 Alterar password</p>
            <p class="panel-desc" data-pt="Usa uma password longa e aleatória para manteres a conta segura." data-en="Use a long, random password to keep your account secure.">Usa uma password longa e aleatória para manteres a conta segura.</p>
            @include('profile.partials.update-password-form')
        </div>

	{{-- Eliminar conta --}}
        <div class="danger-panel">
            <p class="panel-title" style="color:#dc2626;" data-pt="⚠️ Zona de perigo" data-en="⚠️ Danger zone">⚠️ Zona de perigo</p>
            <p class="panel-desc" style="border-bottom:1px solid #fecaca;" data-pt="Ao eliminar a conta, todos os dados serão permanentemente apagados." data-en="Deleting your account will permanently remove all your data.">Ao eliminar a conta, todos os dados serão permanentemente apagados.</p>
            @include('profile.partials.delete-user-form')
        </div>

    </div>

	<script>
		const lang = '{{ session("locale", config("app.locale")) }}';
		document.querySelectorAll('[data-pt]').forEach(el => {
		        if (el.children.length === 0) {
            			el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-pt');
        		}
    		});
	</script>

</body>
</html>
