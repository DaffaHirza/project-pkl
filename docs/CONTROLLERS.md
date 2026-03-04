# Controllers

Semua controller ada di `app/Http/Controllers/`


## DashboardController

Route: `/kanban/dashboard`

**index()** - Tampilkan dashboard dengan statistik:
- Total clients, projects aktif, assets
- Asset critical
- Aktivitas terbaru


## ClientController

Route: `/kanban/clients`

**index()** - Daftar client dengan search & pagination
**create()** - Form tambah client
**store()** - Simpan client baru
**show()** - Detail client + daftar project
**edit()** - Form edit client
**update()** - Update client
**destroy()** - Hapus client (jika tidak punya project)

Validasi store/update:
- name: required, min:2, max:255
- company_name: nullable, max:255


## ProjectController

Route: `/kanban/projects`

**index()** - Daftar project dengan filter (status, client) & search
**create()** - Form tambah project
**store()** - Simpan project + kirim notifikasi
**show()** - Detail project + daftar asset per stage
**edit()** - Form edit project
**update()** - Update project
**destroy()** - Soft delete project

Validasi store/update:
- client_id: required, exists
- name: required, min:3, max:255


## AssetController

Route: `/kanban/assets`

**index()** - Daftar asset dengan filter (project, stage, priority) & search
**board()** - Kanban board 13 stage
**create()** - Form tambah asset
**store()** - Simpan asset + kirim notifikasi
**show()** - Detail asset dengan dokumen & catatan
**edit()** - Form edit asset
**update()** - Update asset
**destroy()** - Soft delete asset
**moveStage()** - Pindah stage (untuk drag & drop) + kirim notifikasi Telegram

Validasi store/update:
- project_id: required, exists
- name: required, min:3, max:255
- asset_type: required, in:[types]
- location: nullable, max:500
- priority: required, in:normal,warning,critical


## DocumentController

Route: `/kanban/assets/{asset}/documents`

**store()** - Upload dokumen ke asset
**download()** - Download dokumen
**destroy()** - Hapus dokumen

Validasi upload:
- files: required, array
- files.*: file, max:20480 (20MB)


## NoteController

Route: `/kanban/assets/{asset}/notes`

**store()** - Tambah catatan + kirim notifikasi Telegram
**destroy()** - Hapus catatan (hanya milik sendiri)
**byStage()** - Ambil catatan per stage

Validasi:
- content: required, min:3, max:2000
- type: nullable, in:note,approval,rejection


## NotificationController

Route: `/notifications`

**index()** - Daftar semua notifikasi
**recent()** - 10 notifikasi terbaru (JSON)
**markAsRead()** - Tandai sudah dibaca
**markAllAsRead()** - Tandai semua sudah dibaca
**destroy()** - Hapus notifikasi
**unreadCount()** - Jumlah belum dibaca (JSON)


## TelegramWebhookController

Route: `/api/telegram/webhook`

**handle()** - Handle pesan dari Telegram
**setWebhook()** - Set webhook URL (admin only)
**getWebhookInfo()** - Cek status webhook (admin only)
**deleteWebhook()** - Hapus webhook (admin only)

Bot commands:
- /start - Selamat datang + Chat ID
- /id - Tampilkan Chat ID
- /help - Bantuan


## ProfileController

Route: `/profile`

**edit()** - Form edit profil
**update()** - Update profil (name, email, telegram_chat_id)
**destroy()** - Hapus akun


## Auth Controllers (Laravel Breeze)

Standard authentication:
- AuthenticatedSessionController - Login/logout
- RegisteredUserController - Register
- PasswordResetLinkController - Lupa password
- NewPasswordController - Reset password
- PasswordController - Ubah password
