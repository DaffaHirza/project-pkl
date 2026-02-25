# Dokumentasi Models

Lokasi: app/Models/

---

## 1. User.php

Tabel: users

### Deskripsi
Model untuk manajemen user/pengguna sistem.

### Kolom Database
- id (primary key)
- name (string) - Nama lengkap user
- email (string, unique) - Email untuk login
- password (string, hashed) - Password terenkripsi
- role (string) - Role user: user, admin, superuser
- is_active (boolean) - Status aktif/nonaktif
- telegram_chat_id (string, nullable) - ID chat Telegram untuk notifikasi
- last_login_at (datetime, nullable) - Waktu login terakhir
- email_verified_at (datetime, nullable)
- remember_token (string, nullable)
- created_at, updated_at (timestamps)

### Konstanta Role
- ROLE_USER = 'user' - User biasa
- ROLE_ADMIN = 'admin' - Admin
- ROLE_SUPERUSER = 'superuser' - Developer/Superuser

### Method Penting
- isUser() - Cek apakah role = user
- isAdmin() - Cek apakah role = admin
- isSuperuser() - Cek apakah role = superuser
- hasAdminAccess() - Cek apakah admin atau superuser
- uploadedDocuments() - Relasi ke dokumen yang diupload
- notes() - Relasi ke catatan yang dibuat

---

## 2. ClientKanban.php

Tabel: clients_kanban

### Deskripsi
Model untuk data klien/customer yang memiliki project penilaian.

### Kolom Database
- id (primary key)
- name (string) - Nama PIC/kontak person
- company_name (string, nullable) - Nama perusahaan
- email (string, nullable, unique) - Email kontak
- phone (string, nullable) - Nomor telepon
- address (text, nullable) - Alamat lengkap
- created_at, updated_at (timestamps)

### Relasi
- projects() - hasMany ke ProjectKanban (satu client punya banyak project)

### Accessor
- display_name - Menampilkan "Nama (Perusahaan)" jika ada company_name
- projects_count - Jumlah total project
- active_projects_count - Jumlah project aktif

---

## 3. ProjectKanban.php

Tabel: projects_kanban

### Deskripsi
Model untuk project penilaian asset. Satu client bisa punya banyak project.

### Kolom Database
- id (primary key)
- client_id (foreign key ke clients_kanban)
- project_code (string, auto-generate) - Format: PRJ-YYYY-XXX
- name (string) - Nama project
- description (text, nullable) - Deskripsi project
- due_date (date, nullable) - Tanggal deadline
- status (string) - Status: active, completed, cancelled
- created_at, updated_at, deleted_at (timestamps + soft delete)

### Konstanta Status
- active = Aktif
- completed = Selesai
- cancelled = Dibatalkan

### Relasi
- client() - belongsTo ke ClientKanban
- assets() - hasMany ke ProjectAssetKanban

### Accessor
- status_label - Label status dalam bahasa Indonesia
- assets_count - Jumlah asset
- progress - Persentase progress keseluruhan (0-100)
- assets_by_stage - Asset dikelompokkan per stage

### Auto-generate
Saat membuat project baru, project_code otomatis di-generate dengan format PRJ-YYYY-XXX.

---

## 4. ProjectAssetKanban.php

Tabel: project_assets_kanban

### Deskripsi
Model utama untuk asset yang dinilai. Setiap asset memiliki 13 stage penilaian.

### Kolom Database
- id (primary key)
- project_id (foreign key ke projects_kanban)
- asset_code (string, auto-generate) - Format: AST-YYYY-XXXX
- name (string) - Nama asset
- description (text, nullable) - Deskripsi asset
- asset_type (string) - Jenis asset
- location (string, nullable) - Lokasi asset
- current_stage (integer, 1-13) - Stage saat ini
- priority (string) - Prioritas: normal, warning, critical
- position (integer) - Posisi untuk sorting di kanban
- created_at, updated_at, deleted_at (timestamps + soft delete)

### Konstanta - 13 STAGES
1. Inisiasi
2. Penawaran
3. Kesepakatan
4. Eksekusi Lapangan
5. Analisis
6. Review 1
7. Draft Resume
8. Approval Klien
9. Draft Laporan
10. Review 2
11. Finalisasi
12. Delivery & Payment
13. Arsip

### Konstanta - Tipe Asset
- tanah = Tanah
- bangunan = Bangunan
- tanah_bangunan = Tanah & Bangunan
- mesin = Mesin & Peralatan
- kendaraan = Kendaraan
- inventaris = Inventaris
- aset_tak_berwujud = Aset Tak Berwujud
- lainnya = Lainnya

### Konstanta - Prioritas
- normal = Normal
- warning = Warning (kuning)
- critical = Critical (merah)

### Relasi
- project() - belongsTo ke ProjectKanban
- documents() - hasMany ke AssetDocumentKanban
- notes() - hasMany ke AssetNoteKanban

### Method Penting
- moveToStage(stage, userId, note) - Pindahkan asset ke stage tertentu
- approve(userId, stage, note) - Setujui di stage tertentu
- reject(userId, stage, note) - Tolak di stage tertentu
- addNote(userId, stage, type, content) - Tambah catatan

### Accessor
- stage_label - Nama stage dalam bahasa Indonesia
- asset_type_label - Label tipe asset
- priority_label - Label prioritas
- progress - Persentase progress (current_stage/13 * 100)

---

## 5. AssetDocumentKanban.php

Tabel: asset_documents_kanban

### Deskripsi
Model untuk dokumen pendukung asset. Dokumen disimpan per stage.

### Kolom Database
- id (primary key)
- asset_id (foreign key ke project_assets_kanban)
- uploaded_by (foreign key ke users)
- stage (integer, 1-13) - Stage saat upload
- file_name (string) - Nama file asli
- file_path (string) - Path file di storage
- file_type (string) - Ekstensi file
- file_size (integer) - Ukuran file dalam bytes
- description (text, nullable) - Deskripsi dokumen
- created_at, updated_at (timestamps)

### Konstanta
- MAX_FILE_SIZE = 20971520 (20MB)
- ALLOWED_TYPES = pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif, webp, zip, rar, 7z, txt, csv

### Relasi
- asset() - belongsTo ke ProjectAssetKanban
- uploader() - belongsTo ke User

### Accessor
- stage_label - Nama stage upload
- file_size_formatted - Ukuran file dalam KB/MB
- download_url - URL untuk download

### Method
- deleteFile() - Hapus file dari storage

---

## 6. AssetNoteKanban.php

Tabel: asset_notes_kanban

### Deskripsi
Model untuk catatan/komentar pada asset. Mencatat history perubahan stage, approval, rejection, dan catatan bebas.

### Kolom Database
- id (primary key)
- asset_id (foreign key ke project_assets_kanban)
- user_id (foreign key ke users)
- stage (integer, 1-13) - Stage saat catatan dibuat
- type (string) - Tipe catatan
- content (text) - Isi catatan
- created_at, updated_at (timestamps)

### Konstanta - Tipe Catatan
- note = Catatan biasa
- stage_change = Perubahan stage (otomatis)
- approval = Persetujuan
- rejection = Penolakan

### Relasi
- asset() - belongsTo ke ProjectAssetKanban
- user() - belongsTo ke User

### Accessor
- stage_label - Nama stage
- type_label - Label tipe catatan

### Method
- isStageChange() - Cek apakah catatan perubahan stage
- isApproval() - Cek apakah catatan approval
- isRejection() - Cek apakah catatan rejection

---

## 7. Notification.php

Tabel: notifications

### Deskripsi
Model untuk sistem notifikasi in-app. Menggunakan UUID sebagai primary key.

### Kolom Database
- id (uuid, primary key)
- type (string) - Tipe notifikasi
- notifiable_type (string) - Model yang menerima (App\Models\User)
- notifiable_id (integer) - ID user penerima
- data (json) - Data notifikasi
- read_at (datetime, nullable) - Waktu dibaca
- created_at, updated_at (timestamps)

### Konstanta - Tipe Notifikasi
Asset:
- asset_stage_changed = Stage Asset Berubah
- asset_created = Asset Baru Dibuat
- asset_document_uploaded = Dokumen Asset Diupload
- asset_note_added = Catatan Asset Ditambahkan
- asset_priority_critical = Asset Priority Critical

Project:
- project_created = Project Baru Dibuat
- project_stage_changed = Stage Project Berubah
- project_assigned = Ditugaskan ke Project
- project_completed = Project Selesai

Client:
- client_created = Client Baru Ditambahkan

Lainnya:
- approval_required = Perlu Approval
- approval_completed = Approval Selesai
- system = Sistem

### Method Static
- notify(user, type, data) - Buat notifikasi baru untuk user

### Scope
- unread() - Filter notifikasi belum dibaca
- read() - Filter notifikasi sudah dibaca
- ofType(type) - Filter berdasarkan tipe
- recent(limit) - Ambil notifikasi terbaru

---

## Diagram Relasi

```
User (1) ──────────────── (M) AssetDocumentKanban
  │                              │
  │                              │
  └──── (M) AssetNoteKanban ─────┘
                │
                │
ClientKanban (1) ── (M) ProjectKanban (1) ── (M) ProjectAssetKanban (1) ─┬─ (M) AssetDocumentKanban
                                                                         │
                                                                         └─ (M) AssetNoteKanban
```

Keterangan:
- (1) = One
- (M) = Many
