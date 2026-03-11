# Views

Semua view ada di `resources/views/`


## Struktur Folder

```
views/
├── layouts/           - Layout utama
├── components/        - Blade components
├── kanban/            - Halaman kanban
│   ├── assets/        - CRUD asset + board
│   ├── clients/       - CRUD client
│   ├── projects/      - (legacy, deprecated)
│   └── recapitulations/ - Rekapitulasi mingguan
├── auth/              - Login, register, dll
├── profile/           - Halaman profil
├── notifications/     - Daftar notifikasi
├── admin/             - Halaman admin
└── assistant/         - Halaman assistant
```


## Layouts

**layouts/app.blade.php**
- Layout utama untuk halaman yang butuh login
- Ada header, sidebar, content area, footer
- Pakai Tailwind CSS + Alpine.js

**layouts/guest.blade.php**
- Layout untuk halaman publik (login, register)
- Simple dengan logo di tengah


## Kanban Pages

**kanban/dashboard.blade.php**
- Statistik: total clients, projects aktif, assets
- Daftar asset critical
- Aktivitas terbaru

**kanban/clients/index.blade.php**
- Tabel client dengan pagination
- Search by nama/perusahaan
- Kolom: Nama, Perusahaan, Jumlah Project

**kanban/clients/create.blade.php & edit.blade.php**
- Form: Nama, Nama Perusahaan, Tipe (Bank/PT-CV/Debitur), Parent

**kanban/clients/show.blade.php**
- Info client (nama, tipe, perusahaan, parent)
- Daftar debitur/PT anak (jika ada)
- Daftar asset milik client

**kanban/assets/index.blade.php**
- Tabel asset dengan pagination
- Filter by client, stage, priority
- Kolom: Nama, Client, Type, Stage, Priority

**kanban/assets/board.blade.php** ⭐
- Kanban board dengan 13 kolom
- Drag & drop asset antar stage
- Filter by client
- Pakai SortableJS

**kanban/assets/create.blade.php & edit.blade.php**
- Form: Client (dropdown), Nama, Tipe Asset, Lokasi, Priority

**kanban/assets/show.blade.php**
- Info lengkap asset
- Tab dokumen - upload & daftar file
- Tab catatan - timeline & form tambah


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


## Profile

**profile/edit.blade.php**
- Edit nama, email
- Edit Telegram Chat ID
- Ubah password
- Hapus akun


## Notifications

**notifications/index.blade.php**
- Daftar semua notifikasi
- Filter read/unread
- Tombol mark as read


## Components

Blade components di `views/components/`:
- Layout components
- Form inputs
- Buttons
- Modals
- Cards
- dll


## Dark Mode

Semua halaman support dark mode:
- Toggle di header/navbar
- Pakai Tailwind `dark:` classes
- Disimpan di localStorage
