# KJPP Mushofah dan Rekan - Sistem Penilaian Asset

Sistem manajemen penilaian asset berbasis Kanban untuk KJPP Mushofah dan Rekan Cabang Semarang.

## Quick Start

```bash
# Install dependencies
composer install && npm install

# Setup database
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Run application
php artisan serve --port=8000  # Terminal 1
npm run dev                     # Terminal 2
php artisan queue:work          # Terminal 3
```

Buka http://localhost:8000

## Dokumentasi

Dokumentasi lengkap tersedia di folder `docs/`:

- [📖 README](docs/README.md) - Overview & quick start
- [🌐 Setup Ngrok](docs/SETUP-NGROK.md) - Ekspos aplikasi ke internet
- [📱 Setup Telegram](docs/SETUP-TELEGRAM.md) - Konfigurasi notifikasi Telegram

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kjpp.id | admin123 |
| User | andi@kjpp.id | password123 |

## Tech Stack

- Laravel 11 + Blade + Tailwind CSS + Alpine.js
- PostgreSQL
- Telegram Bot API

## License

Proprietary - KJPP Mushofah dan Rekan
