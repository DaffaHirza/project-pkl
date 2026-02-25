# Dokumentasi Controllers

Lokasi: app/Http/Controllers/

---

## 1. Controller.php

Base controller. Tidak ada logic khusus.

---

## 2. Kanban/DashboardController.php

### Deskripsi
Controller untuk halaman dashboard utama kanban.

### Method

#### index()
- Route: GET /kanban/dashboard
- Fungsi: Menampilkan dashboard dengan statistik
- Data yang dikembalikan:
  - stats (total clients, projects, assets, dll)
  - criticalAssets (asset dengan priority critical)
  - recentActivities (aktivitas terbaru)
- View: kanban.dashboard

#### data()
- Route: GET /kanban/dashboard/data (API)
- Fungsi: Mengambil data dashboard dalam format JSON
- Response: JSON dengan stats dan data terbaru

---

## 3. Kanban/ClientController.php

### Deskripsi
Controller untuk CRUD data client/klien.

### Method

#### index(Request $request)
- Route: GET /kanban/clients
- Fungsi: Daftar semua client dengan pagination
- Filter: search (nama, perusahaan, email)
- View: kanban.clients.index

#### create()
- Route: GET /kanban/clients/create
- Fungsi: Form tambah client baru
- View: kanban.clients.create

#### store(Request $request)
- Route: POST /kanban/clients
- Fungsi: Simpan client baru ke database
- Validasi:
  - name: required, string, max:255, min:2
  - company_name: nullable, string, max:255
  - email: nullable, email, unique
  - phone: nullable, string, max:50
  - address: nullable, string, max:1000
- Redirect: kanban.clients.show

#### show(ClientKanban $client)
- Route: GET /kanban/clients/{client}
- Fungsi: Detail client dengan daftar project
- View: kanban.clients.show

#### edit(ClientKanban $client)
- Route: GET /kanban/clients/{client}/edit
- Fungsi: Form edit client
- View: kanban.clients.edit

#### update(Request $request, ClientKanban $client)
- Route: PUT /kanban/clients/{client}
- Fungsi: Update data client
- Redirect: kanban.clients.show

#### destroy(ClientKanban $client)
- Route: DELETE /kanban/clients/{client}
- Fungsi: Hapus client (hanya jika tidak punya project)
- Redirect: kanban.clients.index

---

## 4. Kanban/ProjectController.php

### Deskripsi
Controller untuk CRUD data project.

### Method

#### index(Request $request)
- Route: GET /kanban/projects
- Fungsi: Daftar semua project dengan pagination
- Filter: status, client_id, search
- View: kanban.projects.index

#### create(Request $request)
- Route: GET /kanban/projects/create
- Fungsi: Form tambah project baru
- Query: client_id (opsional, pre-select client)
- View: kanban.projects.create

#### store(Request $request)
- Route: POST /kanban/projects
- Fungsi: Simpan project baru
- Validasi:
  - client_id: required, exists
  - name: required, string, max:255, min:3
  - description: nullable, string, max:2000
  - due_date: nullable, date, after_or_equal:today
- Notifikasi: Kirim notifikasi ke semua user
- Redirect: kanban.projects.show

#### show(ProjectKanban $project)
- Route: GET /kanban/projects/{project}
- Fungsi: Detail project dengan daftar asset per stage
- View: kanban.projects.show

#### edit(ProjectKanban $project)
- Route: GET /kanban/projects/{project}/edit
- Fungsi: Form edit project
- View: kanban.projects.edit

#### update(Request $request, ProjectKanban $project)
- Route: PUT /kanban/projects/{project}
- Fungsi: Update data project
- Redirect: kanban.projects.show

#### destroy(ProjectKanban $project)
- Route: DELETE /kanban/projects/{project}
- Fungsi: Soft delete project
- Redirect: kanban.projects.index

---

## 5. Kanban/AssetController.php

### Deskripsi
Controller untuk CRUD asset dan kanban board.

### Method

#### index(Request $request)
- Route: GET /kanban/assets
- Fungsi: Daftar semua asset dengan pagination
- Filter: project_id, stage, priority, search
- View: kanban.assets.index

#### board(Request $request)
- Route: GET /kanban/assets/board
- Fungsi: Tampilan kanban board dengan 13 stage
- Filter: project_id
- View: kanban.assets.board

#### create(Request $request)
- Route: GET /kanban/assets/create
- Fungsi: Form tambah asset baru
- Query: project_id (opsional)
- View: kanban.assets.create

#### store(Request $request)
- Route: POST /kanban/assets
- Fungsi: Simpan asset baru
- Validasi:
  - project_id: required, exists
  - name: required, string, max:255, min:3
  - description: nullable, string, max:2000
  - asset_type: required, in:tanah,bangunan,...
  - location: nullable, string, max:500
  - priority: required, in:normal,warning,critical
- Notifikasi: Kirim notifikasi asset baru
- Redirect: kanban.assets.show

#### show(ProjectAssetKanban $asset)
- Route: GET /kanban/assets/{asset}
- Fungsi: Detail asset dengan dokumen dan catatan
- View: kanban.assets.show

#### edit(ProjectAssetKanban $asset)
- Route: GET /kanban/assets/{asset}/edit
- Fungsi: Form edit asset
- View: kanban.assets.edit

#### update(Request $request, ProjectAssetKanban $asset)
- Route: PUT /kanban/assets/{asset}
- Fungsi: Update data asset
- Redirect: kanban.assets.show

#### destroy(ProjectAssetKanban $asset)
- Route: DELETE /kanban/assets/{asset}
- Fungsi: Soft delete asset
- Redirect: kanban.assets.index

#### moveStage(Request $request, ProjectAssetKanban $asset)
- Route: POST /kanban/assets/{asset}/move
- Fungsi: Pindahkan asset ke stage lain (drag & drop)
- Parameter:
  - stage: integer (1-13)
  - position: integer (opsional)
  - note: string (opsional)
- Notifikasi: Kirim notifikasi perubahan stage + Telegram
- Response: JSON

---

## 6. Kanban/DocumentController.php

### Deskripsi
Controller untuk upload dan manajemen dokumen asset.

### Method

#### store(Request $request, ProjectAssetKanban $asset)
- Route: POST /kanban/assets/{asset}/documents
- Fungsi: Upload dokumen ke asset
- Validasi:
  - files: required, array
  - files.*: file, max:20480 (20MB)
  - stage: nullable, integer, 1-13
  - description: nullable, string, max:500
- Path: storage/assets/{asset_id}/stage-{stage}/
- Notifikasi: Kirim notifikasi dokumen diupload
- Response: JSON atau redirect

#### download(AssetDocumentKanban $document)
- Route: GET /kanban/documents/{document}/download
- Fungsi: Download dokumen
- Response: File download

#### destroy(AssetDocumentKanban $document)
- Route: DELETE /kanban/documents/{document}
- Fungsi: Hapus dokumen (file + record)
- Response: JSON atau redirect

---

## 7. Kanban/NoteController.php

### Deskripsi
Controller untuk catatan/komentar asset.

### Method

#### store(Request $request, ProjectAssetKanban $asset)
- Route: POST /kanban/assets/{asset}/notes
- Fungsi: Tambah catatan baru
- Validasi:
  - content: required, string, min:3, max:2000
  - stage: nullable, integer, 1-13
  - type: nullable, in:note,approval,rejection
- Notifikasi: Kirim notifikasi catatan baru + Telegram
- Response: JSON atau redirect

#### destroy(AssetNoteKanban $note)
- Route: DELETE /kanban/notes/{note}
- Fungsi: Hapus catatan (hanya catatan sendiri)
- Response: JSON atau redirect

#### byStage(ProjectAssetKanban $asset, int $stage)
- Route: GET /kanban/assets/{asset}/notes/stage/{stage}
- Fungsi: Ambil catatan per stage
- Response: JSON

---

## 8. ProfileController.php

### Deskripsi
Controller untuk manajemen profil user.

### Method

#### edit(Request $request)
- Route: GET /profile
- Fungsi: Form edit profil
- View: profile.edit

#### update(ProfileUpdateRequest $request)
- Route: PATCH /profile
- Fungsi: Update data profil
- Data yang diupdate:
  - name
  - email
  - telegram_chat_id
- Redirect: profile.edit

#### destroy(Request $request)
- Route: DELETE /profile
- Fungsi: Hapus akun sendiri
- Validasi: password required
- Redirect: / (home)

---

## 9. NotificationController.php

### Deskripsi
Controller untuk manajemen notifikasi in-app.

### Method

#### index(Request $request)
- Route: GET /notifications
- Fungsi: Daftar semua notifikasi user
- Filter: status (read/unread), type
- View: notifications.index

#### recent(Request $request)
- Route: GET /notifications/recent (API)
- Fungsi: Ambil 10 notifikasi terbaru
- Response: JSON

#### markAsRead(Notification $notification)
- Route: POST /notifications/{notification}/read
- Fungsi: Tandai notifikasi sudah dibaca
- Response: JSON

#### markAllAsRead(Request $request)
- Route: POST /notifications/read-all
- Fungsi: Tandai semua notifikasi sudah dibaca
- Response: JSON

#### destroy(Notification $notification)
- Route: DELETE /notifications/{notification}
- Fungsi: Hapus notifikasi
- Response: JSON atau redirect

#### unreadCount(Request $request)
- Route: GET /notifications/unread-count (API)
- Fungsi: Hitung jumlah notifikasi belum dibaca
- Response: JSON dengan count

---

## 10. TelegramWebhookController.php

### Deskripsi
Controller untuk menangani webhook Telegram bot.

### Method

#### handle(Request $request)
- Route: POST /api/telegram/webhook
- Fungsi: Handle pesan masuk dari Telegram
- Command yang didukung:
  - /start - Selamat datang + Chat ID
  - /help - Bantuan penggunaan
  - /id - Tampilkan Chat ID
- Response: JSON {ok: true}

#### setWebhook(Request $request)
- Route: POST /api/telegram/set-webhook (Admin only)
- Fungsi: Set webhook URL ke Telegram
- Parameter: url (string)
- Response: JSON

#### getWebhookInfo()
- Route: GET /api/telegram/webhook-info (Admin only)
- Fungsi: Cek status webhook
- Response: JSON

#### deleteWebhook()
- Route: POST /api/telegram/delete-webhook (Admin only)
- Fungsi: Hapus webhook
- Response: JSON

---

## 11. Auth Controllers (Laravel Breeze)

Lokasi: app/Http/Controllers/Auth/

### AuthenticatedSessionController.php
- create() - Form login
- store() - Proses login
- destroy() - Logout

### RegisteredUserController.php
- create() - Form register
- store() - Proses register

### PasswordResetLinkController.php
- create() - Form lupa password
- store() - Kirim email reset

### NewPasswordController.php
- create() - Form reset password
- store() - Simpan password baru

### PasswordController.php
- update() - Update password di profil

### ConfirmablePasswordController.php
- show() - Form konfirmasi password
- store() - Proses konfirmasi

### EmailVerificationPromptController.php
- __invoke() - Halaman verifikasi email

### EmailVerificationNotificationController.php
- store() - Kirim ulang email verifikasi

### VerifyEmailController.php
- __invoke() - Proses verifikasi email

---

## Ringkasan Route Utama

### Publik
- GET / - Landing page
- GET /login - Form login
- POST /login - Proses login
- GET /forgot-password - Form lupa password
- POST /forgot-password - Kirim email reset

### Authenticated (Perlu Login)
- GET /dashboard - Redirect ke kanban dashboard
- GET /profile - Edit profil
- PATCH /profile - Update profil
- GET /notifications - Daftar notifikasi

### Kanban Routes
- GET /kanban/dashboard - Dashboard
- Resource: /kanban/clients - CRUD Client
- Resource: /kanban/projects - CRUD Project
- Resource: /kanban/assets - CRUD Asset
- GET /kanban/assets/board - Kanban Board
- POST /kanban/assets/{asset}/move - Pindah stage
- POST /kanban/assets/{asset}/documents - Upload dokumen
- POST /kanban/assets/{asset}/notes - Tambah catatan

### API Routes
- POST /api/telegram/webhook - Telegram webhook
- GET /api/telegram/webhook-info - Info webhook (admin)
- POST /api/telegram/set-webhook - Set webhook (admin)
- POST /api/telegram/delete-webhook - Hapus webhook (admin)
