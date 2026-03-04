# Database

Menggunakan PostgreSQL dengan 7 tabel utama.


## Relasi Antar Tabel

```
users
  └── upload dokumen & buat catatan

clients_kanban
  └── punya banyak projects_kanban
        └── punya banyak project_assets_kanban
              ├── punya banyak asset_documents_kanban
              └── punya banyak asset_notes_kanban

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

Menyimpan data klien.

- **id** - Primary key
- **name** - Nama kontak person
- **company_name** - Nama perusahaan
- **timestamps**


## Tabel projects_kanban

Menyimpan data project penilaian.

- **id** - Primary key
- **client_id** - FK ke clients_kanban
- **name** - Nama project
- **status** - active / completed / cancelled
- **timestamps** + soft delete


## Tabel project_assets_kanban

Menyimpan asset yang dinilai (objek penilaian).

- **id** - Primary key
- **project_id** - FK ke projects_kanban
- **name** - Nama objek
- **asset_type** - tanah / bangunan / tanah_bangunan / mesin / kendaraan / inventaris / aset_tak_berwujud / lainnya
- **location** - Alamat lokasi
- **current_stage** - Stage 1-13
- **priority** - normal / warning / critical
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
- **asset_id** - FK ke project_assets_kanban
- **uploaded_by** - FK ke users
- **stage** - Stage saat upload (1-13)
- **file_name** - Nama file asli
- **file_path** - Path di storage
- **file_type** - Ekstensi file
- **file_size** - Ukuran dalam bytes
- **description** - Keterangan
- **timestamps**

Max file size: 20MB


## Tabel asset_notes_kanban

Menyimpan catatan/komentar asset.

- **id** - Primary key
- **asset_id** - FK ke project_assets_kanban
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


## Migration Files

Urutan:
1. create_users_table
2. create_cache_table
3. create_notifications_table
4. create_clients_table
5. create_projects_table
6. create_project_assets_table
7. create_asset_documents_table
8. create_asset_notes_table
9. create_jobs_table
10. create_failed_jobs_table
