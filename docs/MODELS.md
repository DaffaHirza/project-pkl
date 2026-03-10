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

Model untuk data klien dengan struktur hierarki.

**Fillable:** name, company_name, type, parent_id

**Type constants:**
- TYPE_BANK = 'bank' - Bank yang memiliki debitur
- TYPE_PT_CV = 'pt_cv' - PT/CV (bisa induk atau anak)
- TYPE_DEBITUR = 'debitur' - Debitur dari bank

**Relasi:**
- parent() - Client induk (untuk debitur → bank, atau pt_anak → pt_induk)
- children() - Child clients (semua tipe)
- debiturs() - Khusus debitur (jika ini bank)
- childCompanies() - Khusus PT/CV anak (jika ini PT induk)
- assets() - Asset milik client ini

**Accessor:**
- display_name - Format "Nama (Perusahaan)"
- assets_count - Jumlah asset

**Scope:**
- banks() - Filter hanya bank
- companies() - Filter hanya PT/CV
- rootClients() - Client tanpa parent


## AssetKanban.php

Model utama untuk asset yang dinilai.

**Fillable:** client_id, name, asset_type, location, current_stage, priority, position

**Constants:**
- STAGES - 13 stage (lihat DATABASE.md)
- ASSET_TYPES - 8 tipe asset
- PRIORITIES - normal, warning, critical

**Relasi:**
- client() - Client pemilik asset
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
- MAX_FILE_SIZE = 100MB
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
- client_created

**Method:**
- notify(user, type, data) - Buat notifikasi baru

**Scope:**
- unread() - Filter belum dibaca
- read() - Filter sudah dibaca
- recent(limit) - Ambil terbaru


## RecapitulationKanban.php

Model untuk rekapitulasi progress mingguan.

**Fillable:** title, period_start, period_end, summary, status, created_by, published_at

**Status constants:**
- STATUS_DRAFT = 'draft'
- STATUS_PUBLISHED = 'published'

**Relasi:**
- creator() - User pembuat rekapitulasi
- items() - Item-item pekerjaan

**Method:**
- publish() - Publikasikan rekapitulasi
- unpublish() - Kembalikan ke draft
- generateTitle() - Generate judul otomatis berdasarkan periode
- getSuggestedPeriod() - Dapatkan saran periode (7-14 hari dari hari ini)

**Accessor:**
- status_label - Label status (Draft/Dipublikasikan)
- period_label - Format "DD MMM - DD MMM YYYY"
- duration_days - Jumlah hari dalam periode
- progress_summary - Ringkasan progress (X dari Y selesai)
- completion_rate - Persentase penyelesaian

**Scope:**
- published() - Filter dipublikasikan
- draft() - Filter draft
- inPeriod(start, end) - Filter by periode


## RecapitulationItemKanban.php

Model untuk item pekerjaan dalam rekapitulasi.

**Fillable:** recapitulation_id, asset_id, stage_start, stage_end, work_status, activities, notes, next_actions

**Work Status constants:**
- not_started - Belum Dimulai
- in_progress - Dalam Proses
- completed - Selesai
- blocked - Terhambat
- pending_review - Menunggu Review

**WORK_STATUS_COLORS:**
- not_started - gray
- in_progress - blue
- completed - green
- blocked - red
- pending_review - yellow

**Relasi:**
- recapitulation() - Rekapitulasi parent
- asset() - Asset terkait

**Method:**
- generateActivitiesFromNotes(periodStart, periodEnd) - Generate aktivitas dari catatan dalam periode
- determineWorkStatus() - Tentukan status berdasarkan stage progress

**Accessor:**
- work_status_label - Label status in Indonesian
- work_status_color - Warna untuk badge
- stage_start_label - Label stage awal
- stage_end_label - Label stage akhir
- stage_progress - Jumlah stage yang dilalui

**Cast:**
- activities => array (JSON)
