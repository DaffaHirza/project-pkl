# KJPP Mushofah dan Rekan - Sistem Penilaian Asset

Sistem manajemen penilaian asset berbasis Kanban untuk KJPP Mushofah dan Rekan Cabang Semarang.

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL
- Ngrok (untuk development)

### 1. Clone & Install

```bash
# Clone repository
git clone https://github.com/DaffaHirza/project-pkl.git
cd project-pkl

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 2. Konfigurasi Database

Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=projectpkl
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Migrasi & Seed

```bash
# Jalankan migrasi
php artisan migrate

# Seed data awal
php artisan db:seed
```

### 4. Jalankan Aplikasi

```bash
# Terminal 1 - Laravel Server
php artisan serve --port=8000

# Terminal 2 - Vite (frontend)
npm run dev

# Terminal 3 - Queue Worker
php artisan queue:work --tries=3
```

Buka http://localhost:8000

---

## Fitur Utama

### Kanban Board
- 13 stage penilaian asset
- Drag & drop untuk pindah stage
- Filter berdasarkan project, status, prioritas

### Notifikasi
- Notifikasi in-app real-time
- Notifikasi Telegram (stage change & notes)

### Manajemen Data
- Client management
- Project management
- Asset management
- Document upload
- Notes/catatan internal

---

## Dokumentasi Lanjutan

- [Setup Ngrok](./SETUP-NGROK.md) - Ekspos aplikasi ke internet
- [Setup Telegram](./SETUP-TELEGRAM.md) - Konfigurasi notifikasi Telegram

---

## Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Database:** PostgreSQL
- **Queue:** Database driver
- **Notification:** Telegram Bot API

---

## Struktur Database

```
users                  - Akun pengguna
clients_kanban         - Data klien
projects_kanban        - Data project/proyek
project_assets_kanban  - Asset yang dinilai
asset_documents_kanban - Dokumen pendukung
asset_notes_kanban     - Catatan internal
notifications          - Notifikasi sistem
```

---

## License

Proprietary - KJPP Mushofah dan Rekan
