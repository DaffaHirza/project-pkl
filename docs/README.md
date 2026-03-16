# KJPP Mushofah - Sistem Penilaian Asset

Sistem manajemen penilaian asset berbasis Kanban untuk KJPP Mushofah dan Rekan Cabang Semarang.


## Cara Menjalankan

**Yang dibutuhkan:**
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL

**Langkah instalasi:**

```bash
git clone https://github.com/DaffaHirza/project-pkl.git
cd project-pkl
composer install
npm install
cp .env.example .env
php artisan key:generate
```

**Setting database di .env:**

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=projectpkl
DB_USERNAME=username_kamu
DB_PASSWORD=password_kamu
```

**Jalankan migrasi:**

```bash
php artisan migrate
php artisan db:seed
```

**Jalankan aplikasi (3 terminal):**

```bash
# Terminal 1 - Server
php artisan serve --port=8000

# Terminal 2 - Frontend 
npm run dev

# Terminal 3 - Queue (untuk notifikasi)
php artisan queue:work --tries=3
```

Buka http://localhost:8000


## Akun Default

| Email | Password | Role |
|-------|----------|------|
| developer@kjpp.id | password | Superuser |
| admin@kjpp.id | password | Admin |
| supervisor@kjpp.id | password | Admin |
| andi@kjpp.id | password | User |
| budi@kjpp.id | password | User |
| citra@kjpp.id | password | User |


## Fitur Utama

- **Kanban Board** - 13 stage penilaian, drag & drop dengan SortableJS
- **Manajemen Client** - Bank, PT/CV, Debitur dengan struktur hierarki
- **Manajemen Asset** - CRUD asset dengan dokumen & catatan per stage
- **Rekapitulasi** - Laporan progress mingguan untuk evaluasi meeting
- **Notifikasi** - In-app + Telegram (real-time via queue)
- **Dark Mode** - Toggle tema gelap/terang


## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Database:** PostgreSQL
- **Queue:** Laravel Queue (database driver)
- **External:** Telegram Bot API (notifikasi)
- **Build:** Vite


## Dokumentasi Lainnya

- [DATABASE.md](DATABASE.md) - Struktur tabel database
- [MODELS.md](MODELS.md) - Eloquent models
- [CONTROLLERS.md](CONTROLLERS.md) - Controllers & routes
- [VIEWS.md](VIEWS.md) - Blade views
- [SERVICES.md](SERVICES.md) - Service classes
- [SETUP-TELEGRAM.md](SETUP-TELEGRAM.md) - Setup bot Telegram
- [SETUP-NGROK.md](SETUP-NGROK.md) - Setup ngrok
