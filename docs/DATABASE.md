# Database

Menggunakan PostgreSQL dengan 8 tabel utama.


## Relasi Antar Tabel

```
users
  └── upload dokumen & buat catatan & buat rekapitulasi

clients_kanban (type: bank / pt_cv / debitur)
  ├── [Bank] punya banyak Debitur (child dengan type='debitur')
  │     └── Debitur punya banyak assets_kanban
  │           ├── punya banyak asset_documents_kanban
  │           └── punya banyak asset_notes_kanban
  │
  ├── [PT/CV] punya banyak assets_kanban (langsung)
  │     ├── punya banyak asset_documents_kanban
  │     └── punya banyak asset_notes_kanban
  │
  └── [PT/CV] bisa punya banyak PT Anak (child dengan type='pt_cv')
        └── PT Anak punya banyak assets_kanban
              └── (struktur sama dengan di atas)

recapitulations_kanban (rekapitulasi mingguan)
  └── punya banyak recapitulation_items_kanban
        └── bereferensi ke assets_kanban

notifications (standalone)
```


## Tabel users

Menyimpan akun pengguna.

- **id** - Primary key
- **name** - Nama lengkap
- **email** - Email untuk login (unique)
- **password** - Password (hashed)
- **role** - user / admin / superuser
- **is_active** - Status aktif
- **telegram_chat_id** - ID Telegram untuk notifikasi
- **last_login_at** - Waktu login terakhir
- **timestamps** - created_at, updated_at


## Tabel clients_kanban

Menyimpan data klien (Bank, PT/CV, atau Debitur).

- **id** - Primary key
- **name** - Nama kontak person / debitur
- **company_name** - Nama perusahaan (nullable)
- **spk_number** - Nomor SPK untuk bank/perusahaan (nullable)
- **type** - bank / pt_cv / debitur (default: bank)
- **parent_id** - FK ke clients_kanban (self-referential, nullable)
- **timestamps**

**Penjelasan Type:**
- **bank**: Client perbankan yang memiliki debitur sebagai children.
- **pt_cv**: Client PT/CV yang langsung memiliki aset. Bisa memiliki PT anak sebagai children.
- **debitur**: Debitur dari bank. parent_id mengarah ke bank.

**Parent ID digunakan untuk:**
- Debitur: parent_id = ID bank
- PT Anak: parent_id = ID PT induk

**Indexes:**
- type
- parent_id


## Tabel assets_kanban

Menyimpan asset yang dinilai (objek penilaian).

- **id** - Primary key
- **client_id** - FK ke clients_kanban (bisa mengarah ke debitur, pt_cv, atau pt_anak)
- **name** - Nama objek
- **asset_type** - tanah / bangunan / tanah_bangunan / mesin / kendaraan / inventaris / aset_tak_berwujud / lainnya
- **location** - Alamat lokasi
- **current_stage** - Stage 1-13
- **position** - Urutan di kanban
- **timestamps** + soft delete

**13 Stage Workflow:**
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


## Tabel asset_documents_kanban

Menyimpan dokumen/file asset.

- **id** - Primary key
- **asset_id** - FK ke assets_kanban
- **uploaded_by** - FK ke users
- **stage** - Stage saat upload (1-13)
- **file_name** - Nama file asli
- **file_path** - Path di storage
- **file_type** - Ekstensi file
- **file_size** - Ukuran dalam bytes
- **description** - Keterangan
- **timestamps**

Max file size: 100MB


## Tabel asset_notes_kanban

Menyimpan catatan/komentar asset.

- **id** - Primary key
- **asset_id** - FK ke assets_kanban
- **user_id** - FK ke users
- **stage** - Stage saat catatan dibuat
- **type** - note / stage_change / approval / rejection
- **content** - Isi catatan
- **timestamps**


## Tabel notifications

Notifikasi in-app untuk user.

- **id** - UUID
- **type** - Class notifikasi
- **notifiable_type** - App\Models\User
- **notifiable_id** - ID user
- **data** - JSON data notifikasi
- **read_at** - Waktu dibaca
- **timestamps**


## Tabel recapitulations_kanban

Menyimpan rekapitulasi progress mingguan untuk evaluasi meeting.

- **id** - Primary key
- **title** - Judul rekapitulasi (contoh: "Rekapitulasi Minggu 1 Maret 2026")
- **period_start** - Tanggal mulai periode
- **period_end** - Tanggal akhir periode
- **summary** - Ringkasan/catatan umum (nullable)
- **status** - draft / published (default: draft)
- **created_by** - FK ke users (pembuat)
- **published_at** - Tanggal dipublikasikan (nullable)
- **timestamps**


## Tabel recapitulation_items_kanban

Menyimpan item-item pekerjaan dalam rekapitulasi.

- **id** - Primary key
- **recapitulation_id** - FK ke recapitulations_kanban
- **asset_id** - FK ke assets_kanban
- **stage_start** - Stage di awal periode
- **stage_end** - Stage di akhir periode
- **work_status** - Status pekerjaan (not_started / in_progress / completed / blocked / pending_review)
- **activities** - JSON array aktivitas yang dilakukan
- **notes** - Catatan tambahan (nullable)
- **next_actions** - Langkah selanjutnya (nullable)
- **timestamps**

**Work Status:**
- **not_started**: Belum dimulai
- **in_progress**: Sedang dikerjakan
- **completed**: Selesai
- **blocked**: Terhambat (otomatis jika ada catatan tipe `rejection` di asset)
- **pending_review**: Menunggu review (otomatis jika asset di stage 6 atau 10)


## Migration Files

Urutan:
1. create_users_table
2. create_cache_table
3. create_notifications_table
4. create_clients_table (dengan type & parent_id)
5. create_assets_table
6. create_asset_documents_table
7. create_asset_notes_table
8. create_jobs_table
9. create_failed_jobs_table
10. create_recapitulations_table
11. create_recapitulation_items_table
