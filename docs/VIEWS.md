# Views

Semua view ada di `resources/views/`


## Struktur Folder

```
views/
├── layouts/        - Layout utama
├── components/     - Blade components
├── kanban/         - Halaman kanban
│   ├── assets/     - CRUD asset + board
│   ├── clients/    - CRUD client
│   └── projects/   - CRUD project
├── auth/           - Login, register, dll
├── profile/        - Halaman profil
├── notifications/  - Daftar notifikasi
├── admin/          - Halaman admin
└── assistant/      - Halaman assistant
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
- Form: Nama, Nama Perusahaan

**kanban/clients/show.blade.php**
- Info client
- Daftar project milik client

**kanban/projects/index.blade.php**
- Tabel project dengan pagination
- Filter by status, client
- Kolom: Nama, Client, Status, Jumlah Asset

**kanban/projects/create.blade.php & edit.blade.php**
- Form: Client (dropdown), Nama Project

**kanban/projects/show.blade.php**
- Info project
- Daftar asset per stage (mini kanban)
- Progress keseluruhan

**kanban/assets/index.blade.php**
- Tabel asset dengan pagination
- Filter by project, stage, priority
- Kolom: Nama, Project, Type, Stage, Priority

**kanban/assets/board.blade.php** ⭐
- Kanban board dengan 13 kolom
- Drag & drop asset antar stage
- Filter by project
- Pakai SortableJS

**kanban/assets/create.blade.php & edit.blade.php**
- Form: Project, Nama, Tipe Asset, Lokasi, Priority

**kanban/assets/show.blade.php**
- Info lengkap asset
- Tab dokumen - upload & daftar file
- Tab catatan - timeline & form tambah


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
