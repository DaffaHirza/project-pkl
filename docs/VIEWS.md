# Views

Semua view ada di `resources/views/`


## Struktur Folder

```
views/
├── layouts/             - Layout utama
├── components/          - Blade components
├── partials/            - Header, sidebar, navigation
├── kanban/              - Halaman kanban
│   ├── assets/          - CRUD asset + board
│   ├── clients/         - CRUD client (split by type)
│   ├── recapitulations/ - Rekapitulasi mingguan
│   ├── partials/        - Partial views untuk kanban
│   ├── dashboard.blade.php
│   └── activity-log.blade.php
├── auth/                - Login, register, dll
├── profile/             - Halaman profil
├── notifications/       - Daftar & settings notifikasi
├── admin/               - Halaman admin (reports)
├── assistant/           - Halaman AI assistant
├── dashboard.blade.php  - Dashboard utama
└── welcome.blade.php    - Landing page
```


## Layouts

**layouts/app.blade.php**
- Layout utama untuk halaman yang butuh login
- Include: sidebar, navigation (header)
- State Alpine: darkMode, sidebarToggle
- Pakai Tailwind CSS + Alpine.js

**layouts/guest.blade.php**
- Layout untuk halaman publik (login, register)
- Simple dengan logo di tengah


## Partials

**partials/sidebar.blade.php**
- Sidebar navigasi dengan menu collapse
- Active state indicator (biru)
- Menu: Dashboard, Tracker (submenu), Notifikasi, Assistant

**partials/navigation.blade.php**
- Header/navbar
- Hamburger toggle (mobile)
- Notifikasi dropdown
- Dark mode toggle
- User dropdown (profile, logout)


## Kanban Dashboard

**kanban/dashboard.blade.php**
- Statistik: total clients, assets, asset critical
- Daftar asset critical
- Aktivitas terbaru

**kanban/activity-log.blade.php**
- Log aktivitas semua asset
- Filter by date, type
- Timeline view


## Kanban Clients

**kanban/clients/index.blade.php**
- Type selector dengan statistik:
  - Bank, PT/CV Induk, Debitur, PT/CV Anak
- Cards dengan jumlah masing-masing tipe

**kanban/clients/perusahaan.blade.php** ⭐
- Daftar Bank & PT/CV Induk
- Search, filter by type
- Kolom: Nama, No SPK, Tipe, Jumlah Anak, Jumlah Asset

**kanban/clients/debitur.blade.php** ⭐
- Daftar Debitur & PT/CV Anak
- Search, filter by parent
- Kolom: Nama, Induk (Bank/PT), Jumlah Asset

**kanban/clients/create.blade.php**
- Type selector untuk form create
- Pilihan: Bank, Perusahaan Induk, Klien Tunggal

**kanban/clients/create-bank.blade.php** ⭐
- Form bank dengan multiple debitur sekaligus
- Dynamic form: tambah/hapus debitur
- Fields: Nama Bank, No SPK, Daftar Debitur

**kanban/clients/create-perusahaan-induk.blade.php** ⭐
- Form PT/CV Induk dengan optional PT anak
- Dynamic form: tambah/hapus PT anak
- Fields: Nama Perusahaan, No SPK, Daftar PT Anak

**kanban/clients/create-klien.blade.php**
- Form tunggal untuk Debitur atau PT/CV Anak
- Dropdown: pilih induk (Bank/PT)
- Fields: Nama, Tipe, Parent

**kanban/clients/show.blade.php**
- Info client (nama, tipe, perusahaan, parent, SPK)
- Daftar children (debitur/PT anak) jika ada
- Daftar asset milik client

**kanban/clients/edit.blade.php**
- Form edit client


## Kanban Assets

**kanban/assets/index.blade.php** ⭐
- Kanban board dengan 13 kolom stage
- Drag & drop asset antar stage (SortableJS)
- Filter by client
- Real-time position update

**kanban/assets/board.blade.php**
- Sudah dihapus, board sekarang memakai `kanban/assets/index.blade.php`

**kanban/assets/create.blade.php**
- Form: Client (searchable dropdown), Nama, Tipe Asset, Lokasi

**kanban/assets/edit.blade.php**
- Form edit asset

**kanban/assets/show.blade.php** ⭐
- Info lengkap asset
- Breadcrumb: Bank/PT → Debitur/Anak → Asset
- Tab dokumen - upload & daftar file dengan stage filter
- Tab catatan - timeline & form tambah dengan pilihan tipe:
  - Catatan (default)
  - Approval
  - Penolakan/Terhambat


## Rekapitulasi Pages

**kanban/recapitulations/index.blade.php**
- Tabel rekapitulasi dengan pagination
- Filter by status (draft/published)
- Kolom: Judul, Periode, Status, Ringkasan Progress

**kanban/recapitulations/create.blade.php**
- Form: Judul, Tanggal Mulai, Tanggal Akhir, Ringkasan
- Saran periode otomatis (7-14 hari)
- Opsi auto-generate items dari aktivitas

**kanban/recapitulations/show.blade.php** ⭐
- Info rekapitulasi + statistik
- Cards: Total Aset, Selesai, Dalam Proses, Pending Review, Terhambat
- Tabel items: Asset, Status, Stage Progress, Aktivitas, Catatan
- Modal: Tambah asset, Edit item
- Tombol: Cetak, Regenerate, Publish/Unpublish

**kanban/recapitulations/edit.blade.php**
- Edit info dasar rekapitulasi (judul, periode, ringkasan)
- Form hapus terpisah dari form edit

**kanban/recapitulations/print.blade.php** ⭐
- Tampilan cetak untuk rapat evaluasi
- Layout bersih & print-friendly
- Header, statistik, tabel items lengkap
- Standalone HTML (tanpa layout)


## Auth Pages

- auth/login.blade.php - Form login
- auth/register.blade.php - Form register
- auth/forgot-password.blade.php - Form lupa password
- auth/reset-password.blade.php - Form reset password
- auth/verify-email.blade.php - Verifikasi email
- auth/confirm-password.blade.php - Konfirmasi password


## Profile

**profile/edit.blade.php**
- Edit nama, email
- Edit Telegram Chat ID
- Ubah password
- Hapus akun


## Notifications

**notifications/index.blade.php**
- Daftar semua notifikasi dengan pagination
- Filter: read/unread, type
- Aksi: mark read/unread, delete
- Bulk actions: mark all read, delete all read, delete all

**notifications/settings.blade.php**
- Pengaturan preferensi notifikasi
- Toggle per tipe notifikasi


## Admin

**admin/reports.blade.php**
- Halaman laporan (untuk admin)


## Assistant

**assistant/index.blade.php**
- Halaman AI Assistant


## Components

Blade components di `views/components/`:
- confirm-modal.blade.php - Modal konfirmasi
- dropdown.blade.php - Dropdown menu
- file-uploader.blade.php - Upload file dengan progress
- dropzone.blade.php - Drag & drop upload area
- Form inputs, buttons, cards, dll


## Dark Mode

Semua halaman support dark mode:
- Toggle di navigation (header)
- Pakai Tailwind `dark:` classes
- Persisted via Alpine.js `$persist`
- Sync dengan `<html>` class
