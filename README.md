# 🔐 ESS Cacifo QR — Smart Locker with QR Code

> Engenharia de Sistemas e Serviços — ESTG / IPL | 2025/26

Sistema IoT de controlo de acesso a cacifos com QR Code dinâmico, reservas com pagamento, alertas por email e monitorização em tempo real.

---

## ✨ Funcionalidades

- 🔐 QR Code dinâmico com expiração de 20 segundos
- 📅 Reservas com verificação de conflitos e pagamento (0,15€/min)
- 💳 Carteira digital — MB Way, PayPal, VISA, Mastercard
- 📡 Atualizações em tempo real via WebSockets (Laravel Reverb)
- 📧 Alertas por email — confirmação, abertura e aviso de expiração
- 🛡️ Painel Admin com gráficos, logs e controlo manual
- 🌐 Interface bilingue PT / EN

## 🛠️ Stack

**Backend:** Laravel 11, PHP 8.x, SQLite  
**Frontend:** Blade, Tailwind CSS, Alpine.js, Chart.js  
**Real-Time:** Laravel Reverb + Echo  
**Hardware:** ESP32 / Arduino (simulado)

## 🚀 Instalação

```bash
git clone https://github.com/BeatrizC10/ESS_CacifoQR.git
cd ESS_CacifoQR/cacifo-qr
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

## ▶️ Arranque

```bash
php artisan serve          # Servidor Laravel
npm run dev                # Vite
php artisan reverb:start   # WebSockets
php artisan schedule:work  # Scheduler (emails)
./mailpit.exe              # Email client → localhost:8025
```

## 👥 Autoras

| Nome | Nº Estudante |
|------|-------------|
| Beatriz Henriques Capucho | 2232118 |
| Filipa Henriques Capucho | 2230971 |
