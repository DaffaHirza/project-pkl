# Dokumentasi Views

Lokasi: resources/views/

---

## Struktur Folder

```
resources/views/
├── layouts/           - Layout utama
├── components/        - Blade components
├── kanban/           - Halaman kanban
│   ├── assets/       - CRUD asset
│   ├── clients/      - CRUD client
│   └── projects/     - CRUD project
├── auth/             - Halaman autentikasi
├── profile/          - Halaman profil
├── notifications/    - Halaman notifikasi
├── partials/         - Partial views
├── admin/            - Halaman admin
├── assistant/        - Halaman assistant
└── tracking/         - Halaman tracking
```

---

## Layout Files

### layouts/app.blade.php
Layout utama untuk halaman yang membutuhkan login.

Komponen:
- Header dengan navigasi
- Sidebar menu
- Content area
- Footer
- Alpine.js dan Tailwind CSS

Yield/Slot:
- @yield('title') - Judul halaman
- @yield('content') - Konten utama
- {{ $slot }} - Untuk component-based layouts

---

### layouts/guest.blade.php
Layout untuk halaman publik (login, register, dll).

Komponen:
- Logo di tengah
- Card untuk form
- Background biru/gradient

---

## Halaman Kanban

### kanban/dashboard.blade.php
Dashboard utama dengan statistik.

Menampilkan:
- Total clients, projects, assets
- Project aktif dan overdue
- Asset completed dan critical
- Grafik asset per stage
- Daftar asset critical
- Aktivitas terbaru

---

### kanban/clients/index.blade.php
Daftar semua client.

Fitur:
- Tabel client dengan pagination
- Search by nama/perusahaan/email
- Kolom: Nama, Perusahaan, Email, Phone, Jumlah Project
- Tombol tambah, view, edit, hapus

---

### kanban/clients/create.blade.php
Form tambah client baru.

Field:
- Nama (required)
- Nama Perusahaan
- Email
- Telepon
- Alamat

---

### kanban/clients/edit.blade.php
Form edit client.

Field: Sama dengan create

---

### kanban/clients/show.blade.php
Detail client.

Menampilkan:
- Info client lengkap
- Daftar project milik client
- Statistik project (aktif/selesai)
- Tombol: Edit, Hapus, Tambah Project

---

### kanban/projects/index.blade.php
Daftar semua project.

Fitur:
- Tabel project dengan pagination
- Filter by status, client
- Search by nama/kode
- Kolom: Kode, Nama, Client, Status, Due Date, Jumlah Asset

---

### kanban/projects/create.blade.php
Form tambah project baru.

Field:
- Client (dropdown, required)
- Nama Project (required)
- Deskripsi
- Due Date

---

### kanban/projects/edit.blade.php
Form edit project.

Field: Sama dengan create

---

### kanban/projects/show.blade.php
Detail project.

Menampilkan:
- Info project lengkap
- Info client
- Daftar asset per stage (mini kanban view)
- Progress keseluruhan
- Tombol: Edit, Hapus, Tambah Asset

---

### kanban/assets/index.blade.php
Daftar semua asset.

Fitur:
- Tabel asset dengan pagination
- Filter by project, stage, priority
- Search by nama/kode
- Kolom: Kode, Nama, Project, Type, Stage, Priority
- Badge warna untuk priority

---

### kanban/assets/board.blade.php
Kanban board dengan 13 stage.

Fitur:
- 13 kolom horizontal (scroll)
- Card asset yang bisa di-drag
- Warna card berdasarkan priority
- Filter by project
- Drag & drop antar stage
- Modal konfirmasi saat pindah stage
- Input catatan perpindahan

JavaScript:
- SortableJS untuk drag & drop
- Alpine.js untuk interaktivitas
- Fetch API untuk update stage

---

### kanban/assets/create.blade.php
Form tambah asset baru.

Field:
- Project (dropdown, required)
- Nama Asset (required)
- Deskripsi
- Tipe Asset (dropdown, required)
- Lokasi
- Priority (dropdown, required)

---

### kanban/assets/edit.blade.php
Form edit asset.

Field: Sama dengan create

---

### kanban/assets/show.blade.php
Detail asset lengkap.

Menampilkan:
- Info asset (nama, kode, type, location)
- Info project dan client
- Status dan priority (dengan badge)
- Progress bar (current_stage/13)

Tab/Section:
1. Dokumen
   - Daftar dokumen per stage
   - Form upload dokumen (dropzone)
   - Download dan hapus dokumen

2. Catatan/Riwayat
   - Timeline catatan dan perubahan stage
   - Form tambah catatan baru
   - Filter by stage atau type

3. Info Tambahan
   - Riwayat lengkap perubahan
   - Waktu created dan updated

---

### kanban/activity-log.blade.php
Log aktivitas sistem.

Menampilkan:
- Timeline semua aktivitas
- Filter by type, user, tanggal
- Detail setiap aktivitas

---

## Halaman Autentikasi

### auth/login.blade.php
Halaman login.

Field:
- Email
- Password
- Remember me
- Link: Lupa password

Styling: Background biru KJPP theme

---

### auth/register.blade.php
Halaman register (jika enabled).

Field:
- Nama
- Email
- Password
- Konfirmasi Password

---

### auth/forgot-password.blade.php
Form lupa password.

Field:
- Email
- Tombol: Kirim Link Reset

---

### auth/reset-password.blade.php
Form reset password.

Field:
- Email (readonly)
- Password baru
- Konfirmasi password

---

### auth/verify-email.blade.php
Halaman verifikasi email.

---

### auth/confirm-password.blade.php
Form konfirmasi password untuk aksi sensitif.

---

## Halaman Profil

### profile/edit.blade.php
Halaman edit profil user.

Section:
1. Update Profile Information
   - Nama
   - Email
   - Telegram Chat ID

2. Update Password
   - Password lama
   - Password baru
   - Konfirmasi password baru

3. Delete Account
   - Konfirmasi password
   - Tombol hapus akun

---

### profile/partials/update-profile-information-form.blade.php
Form update info profil.

Field:
- Name
- Email
- Telegram Chat ID (dengan instruksi cara mendapatkan)

Fitur Telegram:
- Link ke bot @kjpp_mushofah_bot
- Instruksi mendapatkan Chat ID
- Status koneksi (terhubung/belum)

---

### profile/partials/update-password-form.blade.php
Form ganti password.

Field:
- Current Password
- New Password
- Confirm Password

---

### profile/partials/delete-user-form.blade.php
Form hapus akun.

Field:
- Password konfirmasi
- Modal konfirmasi

---

## Halaman Notifikasi

### notifications/index.blade.php
Daftar semua notifikasi.

Fitur:
- Tabel notifikasi dengan pagination
- Filter by status (read/unread), type
- Tandai dibaca
- Tandai semua dibaca
- Hapus notifikasi

Kolom:
- Icon/Type
- Pesan
- Waktu
- Status (read/unread)
- Aksi

---

### notifications/settings.blade.php
Pengaturan notifikasi (jika ada).

---

## Halaman Lainnya

### welcome.blade.php
Landing page publik.

Konten:
- Info KJPP Mushofah dan Rekan
- Layanan penilaian asset
- Kontak dan alamat
- Tombol login

Styling: Full-page dengan background image

---

### dashboard.blade.php
Redirect ke kanban/dashboard.

---

### admin/reports.blade.php
Halaman laporan untuk admin.

---

### assistant/index.blade.php
Halaman assistant (jika ada fitur AI assistant).

---

### tracking/index.blade.php
Halaman tracking untuk klien eksternal.

---

## Blade Components

Lokasi: resources/views/components/

### UI Components

#### card.blade.php
Card container dengan shadow dan rounded corners.

Props:
- class (opsional) - Custom class

#### badge.blade.php
Badge/label kecil.

Props:
- color - Warna: blue, green, red, yellow, gray
- text - Teks badge

#### alert.blade.php
Alert/notifikasi.

Props:
- type - success, error, warning, info
- message - Pesan alert

#### modal.blade.php
Modal dialog.

Props:
- name - Identifier modal
- maxWidth - sm, md, lg, xl

#### confirm-modal.blade.php
Modal konfirmasi dengan tombol OK/Cancel.

#### stats-card.blade.php
Card untuk statistik dashboard.

Props:
- title - Judul
- value - Nilai
- icon - Icon (opsional)
- color - Warna

#### empty-state.blade.php
Tampilan saat data kosong.

Props:
- title - Judul
- description - Deskripsi
- icon - Icon

---

### Form Components

#### text-input.blade.php
Input text.

Props: type, name, value, class, autofocus, dll

#### input-label.blade.php
Label untuk input.

Props: for, value

#### input-error.blade.php
Pesan error input.

Props: messages

#### text-area-inputs.blade.php
Textarea.

Props: name, value, rows

#### checkbox-component.blade.php
Checkbox.

Props: name, checked

#### file-uploader.blade.php
File upload dengan dropzone.

Props: name, accept, multiple

#### dropzone.blade.php
Dropzone area untuk drag & drop file.

---

### Navigation Components

#### nav-link.blade.php
Link navigasi.

Props: href, active

#### dropdown.blade.php
Dropdown menu.

#### dropdown-link.blade.php
Link dalam dropdown.

#### responsive-nav-link.blade.php
Link navigasi responsif (mobile).

---

### Button Components

#### primary-button.blade.php
Tombol utama (biru).

#### secondary-button.blade.php
Tombol sekunder (abu-abu).

#### danger-button.blade.php
Tombol bahaya (merah).

---

### Other Components

#### application-logo.blade.php
Logo aplikasi.

#### auth-session-status.blade.php
Status session (untuk pesan sukses login, dll).

---

## Partials

Lokasi: resources/views/partials/

### navigation.blade.php
Navigasi header utama.

Konten:
- Logo
- Menu utama
- Dropdown user
- Tombol notifikasi

### sidebar.blade.php
Sidebar navigasi.

Menu:
- Dashboard
- Clients
- Projects
- Assets
- Kanban Board
- Notifications

### notification-icon.blade.php
Icon notifikasi dengan badge count.

---

## JavaScript di Views

### Kanban Board (board.blade.php)

```javascript
// Inisialisasi SortableJS untuk setiap stage
Sortable.create(stageColumn, {
    group: 'kanban',
    animation: 150,
    onEnd: function(evt) {
        // Panggil API untuk update stage
        fetch(`/kanban/assets/${assetId}/move`, {
            method: 'POST',
            body: JSON.stringify({ stage: newStage })
        });
    }
});
```

### File Upload (show.blade.php)

```javascript
// Dropzone untuk upload file
function handleFileDrop(files) {
    const formData = new FormData();
    files.forEach(f => formData.append('files[]', f));
    
    fetch(`/kanban/assets/${assetId}/documents`, {
        method: 'POST',
        body: formData
    });
}
```

### Notifikasi Real-time

```javascript
// Polling notifikasi setiap 30 detik
setInterval(() => {
    fetch('/notifications/unread-count')
        .then(r => r.json())
        .then(data => updateBadge(data.count));
}, 30000);
```

---

## Tips Styling

### Warna Theme KJPP
- Primary: Blue (#2563eb, #3b82f6)
- Success: Green (#22c55e)
- Warning: Yellow (#eab308)
- Danger: Red (#ef4444)
- Background: Gray (#f3f4f6)

### Priority Colors
- Normal: Gray
- Warning: Yellow
- Critical: Red

### Stage Colors
- Stage 1-4: Light blue (awal)
- Stage 5-8: Blue (proses)
- Stage 9-12: Dark blue (akhir)
- Stage 13: Green (selesai/arsip)
