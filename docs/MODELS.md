# Models

Semua model ada di `app/Models/`


## User.php

Model untuk akun pengguna.

**Fillable:** name, email, password, role, is_active, telegram_chat_id

**Role constants:**
- ROLE_USER = 'user'
- ROLE_ADMIN = 'admin'
- ROLE_SUPERUSER = 'superuser'

**Method penting:**
- isUser(), isAdmin(), isSuperuser() - Cek role
- hasAdminAccess() - Cek apakah admin atau superuser

**Relasi:**
- uploadedDocuments() - Dokumen yang diupload user ini
- notes() - Catatan yang dibuat user ini


## ClientKanban.php

Model untuk data klien.

**Fillable:** name, company_name

**Relasi:**
- projects() - Project milik client ini

**Accessor:**
- display_name - Format "Nama (Perusahaan)"
- projects_count - Jumlah project
- active_projects_count - Jumlah project aktif


## ProjectKanban.php

Model untuk project penilaian.

**Fillable:** client_id, name, status

**Status constants:**
- active, completed, cancelled

**Relasi:**
- client() - Client pemilik project
- assets() - Asset dalam project ini

**Accessor:**
- status_label - Label status Indonesia
- assets_count - Jumlah asset
- progress - Persentase progress (0-100)
- assets_by_stage - Asset dikelompokkan per stage

**Scope:**
- active() - Filter project aktif
- withMinimalClient() - Eager load client


## ProjectAssetKanban.php

Model utama untuk asset yang dinilai.

**Fillable:** project_id, name, asset_type, location, current_stage, priority, position

**Constants:**
- STAGES - 13 stage (lihat DATABASE.md)
- ASSET_TYPES - 8 tipe asset
- PRIORITIES - normal, warning, critical

**Relasi:**
- project() - Project induk
- documents() - Dokumen asset
- notes() - Catatan asset

**Method penting:**
- moveToStage(stage, userId, note) - Pindah ke stage tertentu
- moveToNextStage() - Pindah ke stage berikutnya
- moveToPreviousStage() - Pindah ke stage sebelumnya
- isCompleted() - Cek apakah sudah di stage 13

**Accessor:**
- stage_label - Nama stage
- asset_type_label - Label tipe asset
- priority_label - Label prioritas
- progress - Persentase (current_stage/13 * 100)

**Scope:**
- atStage(stage) - Filter by stage
- completed() - Filter asset selesai
- active() - Filter asset belum selesai
- priority(priority) - Filter by priority


## AssetDocumentKanban.php

Model untuk dokumen asset.

**Fillable:** asset_id, uploaded_by, stage, file_name, file_path, file_type, file_size, description

**Constants:**
- MAX_FILE_SIZE = 20MB
- ALLOWED_TYPES - pdf, doc, docx, xls, xlsx, jpg, png, dll

**Relasi:**
- asset() - Asset pemilik dokumen
- uploader() - User yang upload

**Accessor:**
- stage_label - Nama stage
- file_size_formatted - Format KB/MB
- download_url - URL download


## AssetNoteKanban.php

Model untuk catatan asset.

**Fillable:** asset_id, user_id, stage, type, content

**Type constants:**
- note, stage_change, approval, rejection

**Relasi:**
- asset() - Asset terkait
- user() - User pembuat

**Accessor:**
- stage_label - Nama stage
- type_label - Label tipe catatan


## Notification.php

Model untuk notifikasi in-app.

**Tipe notifikasi:**
- asset_stage_changed
- asset_created
- asset_document_uploaded
- asset_note_added
- asset_priority_critical
- project_created
- project_completed
- client_created

**Method:**
- notify(user, type, data) - Buat notifikasi baru

**Scope:**
- unread() - Filter belum dibaca
- read() - Filter sudah dibaca
- recent(limit) - Ambil terbaru
