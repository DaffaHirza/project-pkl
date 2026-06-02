# Controllers

Semua controller ada di `app/Http/Controllers/`


## DashboardController

Lokasi: `app/Http/Controllers/Kanban/DashboardController.php`

Route: `/kanban`

**index()** - Tampilkan dashboard dengan statistik:
- Total clients, assets
- Asset critical
- Aktivitas terbaru

**activityLog()** - GET `/kanban/activity-log` - Log aktivitas semua asset


## ClientController

Lokasi: `app/Http/Controllers/Kanban/ClientController.php`

Route: `/kanban/clients`

**Daftar Klien (Split by Type):**
- **index()** - GET `/kanban/clients` - Type selector dengan statistik
- **indexPerusahaan()** - GET `/kanban/clients/perusahaan` - Daftar Bank & PT/CV Induk
- **indexDebitur()** - GET `/kanban/clients/debitur` - Daftar Debitur & PT/CV Anak
- **search()** - GET `/kanban/clients/search` - Search API (JSON)

**Form Create (Split by Type):**
- **create()** - GET `/kanban/clients/create` - Type selector
- **createBank()** - GET `/kanban/clients/create/bank` - Form bank + multi debitur
- **createPerusahaanInduk()** - GET `/kanban/clients/create/perusahaan-induk` - Form PT/CV + multi anak
- **createKlien()** - GET `/kanban/clients/create/klien` - Form debitur/PT anak tunggal

**Store (Split by Type):**
- **storeBank()** - POST `/kanban/clients/bank` - Simpan bank + debitur sekaligus
- **storePerusahaanInduk()** - POST `/kanban/clients/perusahaan-induk` - Simpan PT/CV + anak
- **storeKlien()** - POST `/kanban/clients/klien` - Simpan debitur/PT anak tunggal

**CRUD Standard:**
- **show()** - GET `/kanban/clients/{id}` - Detail client + daftar asset
- **edit()** - GET `/kanban/clients/{id}/edit` - Form edit
- **update()** - PUT `/kanban/clients/{id}` - Update client
- **destroy()** - DELETE `/kanban/clients/{id}` - Hapus (jika tidak punya asset)

Validasi storeBank:
- company_name: required, min:2, max:255
- spk_number: nullable, max:100
- debiturs: required, array, min:1
- debiturs.*.name: required, min:2, max:255

Validasi storePerusahaanInduk:
- company_name: required, min:2, max:255
- spk_number: nullable, max:100
- children: nullable, array
- children.*.company_name: required_with, min:2, max:255

Validasi storeKlien:
- name: required, min:2, max:255
- company_name: nullable, max:255
- client_type: required, in:debitur,pt_cv_anak
- parent_id: required, exists


## AssetController

Lokasi: `app/Http/Controllers/Kanban/AssetController.php`

Route: `/kanban/assets`

**index()** - Daftar asset dengan filter (client, stage) & search
  - Mengirim `stageCounts` ke view (jumlah asset per stage dari seluruh database)
**board()** - Kanban board 13 stage dengan drag & drop
**create()** - Form tambah asset
**store()** - Simpan asset + kirim notifikasi
**show()** - Detail asset dengan dokumen & catatan
**edit()** - Form edit asset
**update()** - Update asset
**destroy()** - Soft delete asset (admin only)
**moveStage()** - Pindah stage (untuk drag & drop) + kirim notifikasi Telegram
**updatePosition()** - Update posisi dalam stage (untuk sorting)

Validasi store/update:
- client_id: required, exists
- name: required, min:3, max:255
- asset_type: required, in:[types]
- location: nullable, max:500


## DocumentController

Lokasi: `app/Http/Controllers/Kanban/DocumentController.php`

Route: `/kanban/assets/{asset}/documents`

**store()** - POST - Upload dokumen ke asset
**download()** - GET `/kanban/documents/{id}/download` - Download file
**destroy()** - DELETE `/kanban/documents/{id}` - Hapus dokumen

Validasi upload:
- files: required, array
- files.*: file, max:102400 (100MB)


## NoteController

Lokasi: `app/Http/Controllers/Kanban/NoteController.php`

Route: `/kanban/assets/{asset}/notes`

**store()** - POST - Tambah catatan + kirim notifikasi Telegram
**destroy()** - DELETE `/kanban/notes/{id}` - Hapus catatan (hanya milik sendiri)

Validasi:
- content: required, min:3, max:2000
- type: nullable, in:note,approval,rejection


## NotificationController

Lokasi: `app/Http/Controllers/NotificationController.php`

Route: `/notifications`

**Daftar & View:**
- **index()** - GET `/notifications` - Daftar semua notifikasi dengan filter & pagination
- **recent()** - GET `/notifications/recent` - 10 notifikasi terbaru (JSON untuk dropdown)
- **unreadCount()** - GET `/notifications/unread-count` - Jumlah belum dibaca (JSON)
- **view()** - GET `/notifications/{id}/view` - Buka notifikasi & mark as read + redirect

**Mark Read/Unread:**
- **markAsRead()** - POST `/notifications/{id}/mark-read` - Tandai sudah dibaca
- **markAsUnread()** - POST `/notifications/{id}/mark-unread` - Tandai belum dibaca
- **markAllAsRead()** - POST `/notifications/mark-all-read` - Tandai semua sudah dibaca

**Delete:**
- **destroy()** - DELETE `/notifications/{id}` - Hapus satu notifikasi
- **destroyAllRead()** - DELETE `/notifications/bulk/read` - Hapus semua yang sudah dibaca
- **destroyAll()** - DELETE `/notifications/bulk/all` - Hapus semua notifikasi

**Settings:**
- **settings()** - GET `/notifications/settings` - Halaman pengaturan notifikasi
- **updateSettings()** - POST `/notifications/settings` - Simpan pengaturan


## TelegramWebhookController

Lokasi: `app/Http/Controllers/TelegramWebhookController.php`

Route: `/api/telegram/webhook`

**handle()** - POST - Handle pesan dari Telegram
**setWebhook()** - Set webhook URL (admin only)
**getWebhookInfo()** - Cek status webhook (admin only)
**deleteWebhook()** - Hapus webhook (admin only)

Bot commands:
- /start - Selamat datang + Chat ID
- /id - Tampilkan Chat ID
- /help - Bantuan


## ProfileController

Lokasi: `app/Http/Controllers/ProfileController.php`

Route: `/profile`

**edit()** - GET - Form edit profil
**update()** - PATCH - Update profil (name, email, telegram_chat_id)
**destroy()** - DELETE - Hapus akun


## RecapitulationController

Lokasi: `app/Http/Controllers/Kanban/RecapitulationController.php`

Route: `/kanban/recapitulations`

**CRUD:**
- **index()** - GET - Daftar rekapitulasi dengan filter status & pagination
- **create()** - GET - Form buat rekapitulasi dengan saran periode
- **store()** - POST - Simpan rekapitulasi baru + auto-generate items
- **show()** - GET `/{id}` - Detail rekapitulasi dengan statistik & items
- **edit()** - GET `/{id}/edit` - Form edit rekapitulasi
- **update()** - PUT `/{id}` - Update info rekapitulasi
- **destroy()** - DELETE `/{id}` - Hapus rekapitulasi + items

**Actions:**
- **publish()** - POST `/{id}/publish` - Publikasikan rekapitulasi
- **unpublish()** - POST `/{id}/unpublish` - Kembalikan ke draft
- **regenerate()** - POST `/{id}/regenerate` - Generate ulang items dari aktivitas
- **print()** - GET `/{id}/print` - Tampilan cetak untuk rapat

**Item Management (AJAX):**
- **addItem()** - POST `/{id}/items` - Tambah asset ke rekapitulasi
- **updateItem()** - PUT `/items/{id}` - Update status/notes item
- **removeItem()** - DELETE `/items/{id}` - Hapus item dari rekapitulasi
- **availableAssets()** - GET `/{id}/available-assets` - Daftar asset yang belum ada

Validasi store/update:
- title: required, max:255
- period_start: required, date
- period_end: required, date, after:period_start
- summary: nullable, max:2000


## AssistantController

Lokasi: `app/Http/Controllers/AssistantController.php`

Route: `/assistant`

**index()** - GET - Halaman AI Assistant


## Auth Controllers (Laravel Breeze)

Lokasi: `app/Http/Controllers/Auth/`

Standard authentication:
- AuthenticatedSessionController - Login/logout
- RegisteredUserController - Register
- PasswordResetLinkController - Lupa password
- NewPasswordController - Reset password
- PasswordController - Ubah password
