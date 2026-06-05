# KJPP Mushofah dan Rekan - Sistem Penilaian Aset

Sistem internal berbasis Kanban untuk membantu pengelolaan pekerjaan penilaian aset di KJPP Mushofah dan Rekan Cabang Semarang.

Aplikasi ini digunakan untuk mencatat data klien, debitur, objek penilaian, progres tahapan pekerjaan, dokumen pendukung, catatan internal, serta rekapitulasi pekerjaan.

## Fitur Utama

- Manajemen data Bank, PT/CV Induk, Debitur, dan PT/CV Anak
- Manajemen objek penilaian atau aset
- Kanban board untuk monitoring progres pekerjaan
- Filter berdasarkan kategori klien dan tipe aset
- Checklist stage yang fleksibel dan tidak harus berurutan
- Catatan internal, termasuk penanda hambatan atau penolakan
- Upload, preview, dan download dokumen pendukung
- Rekapitulasi progress pekerjaan untuk evaluasi internal
- Notifikasi internal melalui integrasi layanan pendukung

## Tech Stack

- Laravel
- Blade Template
- Tailwind CSS
- Alpine.js
- PostgreSQL
- Queue Worker
- Integrasi layanan eksternal untuk notifikasi dan penyimpanan dokumen

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed

# Run application
php artisan serve --port=8000
npm run dev
php artisan queue:work
```
