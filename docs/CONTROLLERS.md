# Controllers

Semua controller ada di `app/Http/Controllers/`


## DashboardController

Route: `/kanban/dashboard`

**index()** - Tampilkan dashboard dengan statistik:
- Total clients, assets
- Asset critical
- Aktivitas terbaru


## ClientController

Route: `/kanban/clients`

**index()** - Daftar client dengan search & pagination
**create()** - Form tambah client
**store()** - Simpan client baru
**show()** - Detail client + daftar asset
**edit()** - Form edit client
**update()** - Update client
**destroy()** - Hapus client (jika tidak punya asset)

Validasi store/update:
- name: required, min:2, max:255
- company_name: nullable, max:255
- type: required, in:bank,pt_cv,debitur
- parent_id: nullable, exists (untuk debitur → bank, pt_anak → pt_induk)


## AssetController

Route: `/kanban/assets`

**index()** - Daftar asset dengan filter (client, stage, priority) & search
  - Mengirim `stageCounts` ke view (jumlah asset per stage dari seluruh database)
**board()** - Kanban board 13 stage
**create()** - Form tambah asset
**store()** - Simpan asset + kirim notifikasi
**show()** - Detail asset dengan dokumen & catatan
**edit()** - Form edit asset
**update()** - Update asset
**destroy()** - Soft delete asset
**moveStage()** - Pindah stage (untuk drag & drop) + kirim notifikasi Telegram

Validasi store/update:
- client_id: required, exists
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
- files.*: file, max:102400 (100MB)


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


## RecapitulationController

Route: `/kanban/recapitulations`

**index()** - Daftar rekapitulasi dengan filter status & pagination
**create()** - Form buat rekapitulasi dengan saran periode
**store()** - Simpan rekapitulasi baru + auto-generate items
**show()** - Detail rekapitulasi dengan statistik & items
**edit()** - Form edit rekapitulasi
**update()** - Update info rekapitulasi
**destroy()** - Hapus rekapitulasi + items
**print()** - Tampilan cetak untuk rapat

**publish()** - Publikasikan rekapitulasi
**unpublish()** - Kembalikan ke draft
**regenerate()** - Generate ulang items dari aktivitas

**Item Management (AJAX):**
- addItem(POST) - Tambah asset ke rekapitulasi
- updateItem(PUT) - Update status/notes item
- removeItem(DELETE) - Hapus item dari rekapitulasi
- availableAssets(GET) - Daftar asset yang belum ada di rekapitulasi

Validasi store/update:
- title: required, max:255
- period_start: required, date
- period_end: required, date, after:period_start
- summary: nullable, max:2000


## Auth Controllers (Laravel Breeze)

Standard authentication:
- AuthenticatedSessionController - Login/logout
- RegisteredUserController - Register
- PasswordResetLinkController - Lupa password
- NewPasswordController - Reset password
- PasswordController - Ubah password
