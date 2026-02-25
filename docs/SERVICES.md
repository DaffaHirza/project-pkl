# Dokumentasi Services & Notifications

---

## 1. KanbanNotificationService.php

Lokasi: app/Services/KanbanNotificationService.php

### Deskripsi
Service untuk mengirim notifikasi terkait aktivitas kanban. Mengirim ke:
1. Database (notifikasi in-app)
2. Telegram (jika user punya telegram_chat_id)

### Method Static

#### notifyStageChange(asset, oldStage, newStage, changedBy, note)
Dipanggil saat: Asset dipindahkan ke stage lain

Parameter:
- asset (ProjectAssetKanban) - Asset yang dipindahkan
- oldStage (int) - Stage asal (1-13)
- newStage (int) - Stage tujuan (1-13)
- changedBy (User) - User yang memindahkan
- note (string, nullable) - Catatan perpindahan

Aksi:
- Buat notifikasi database untuk semua user kecuali changedBy
- Kirim Telegram ke user dengan telegram_chat_id (unique)

---

#### notifyDocumentUploaded(asset, fileName, uploadedBy)
Dipanggil saat: Dokumen diupload ke asset

Parameter:
- asset (ProjectAssetKanban) - Asset target
- fileName (string) - Nama file yang diupload
- uploadedBy (User) - User yang upload

Aksi:
- Buat notifikasi database untuk semua user kecuali uploadedBy
- TIDAK kirim Telegram

---

#### notifyNoteAdded(asset, note, addedBy)
Dipanggil saat: Catatan ditambahkan ke asset

Parameter:
- asset (ProjectAssetKanban) - Asset target
- note (AssetNoteKanban) - Catatan yang dibuat
- addedBy (User) - User yang menambahkan

Aksi:
- Buat notifikasi database untuk semua user kecuali addedBy
- Kirim Telegram ke user dengan telegram_chat_id (unique)

---

#### notifyAssetCreated(asset, createdBy)
Dipanggil saat: Asset baru dibuat

Parameter:
- asset (ProjectAssetKanban) - Asset baru
- createdBy (User) - User pembuat

Aksi:
- Buat notifikasi database untuk semua user kecuali createdBy
- TIDAK kirim Telegram

---

#### notifyProjectCreated(project, createdBy)
Dipanggil saat: Project baru dibuat

Parameter:
- project (ProjectKanban) - Project baru
- createdBy (User) - User pembuat

Aksi:
- Buat notifikasi database untuk semua user kecuali createdBy
- TIDAK kirim Telegram

---

#### notifyPriorityCritical(asset, changedBy)
Dipanggil saat: Priority asset diubah ke critical

Parameter:
- asset (ProjectAssetKanban) - Asset yang diubah
- changedBy (User) - User yang mengubah

Aksi:
- Buat notifikasi database untuk semua user kecuali changedBy
- TIDAK kirim Telegram

---

### Fitur Deduplikasi Telegram
Jika beberapa user memiliki telegram_chat_id yang sama, sistem hanya mengirim 1 notifikasi per Chat ID untuk menghindari duplikasi.

Cara kerja:
```php
$users = User::whereNotNull('telegram_chat_id')
    ->get()
    ->unique('telegram_chat_id');
```

---

## 2. AssessmentUpdated.php (Notification)

Lokasi: app/Notifications/AssessmentUpdated.php

### Deskripsi
Class notification Laravel untuk mengirim notifikasi Telegram dan database.

### Constructor
```php
__construct(
    ProjectAssetKanban $asset,
    string $type,
    User $actor,
    ?string $additionalInfo = null
)
```

Parameter:
- asset - Asset yang bersangkutan
- type - Tipe notifikasi: stage_change, new_note, document_uploaded, priority_change
- actor - User yang melakukan aksi
- additionalInfo - Info tambahan (catatan, nama file, dll)

### Channel
- database - Selalu
- TelegramChannel - Hanya jika user punya telegram_chat_id

### Tipe Notifikasi

#### stage_change
Emoji: 🔄
Judul: Status Berubah!
Konten: Asset [nama] ([kode]) kini berada di stage: [stage baru]

#### new_note
Emoji: 📝
Judul: Catatan Baru!
Konten: [nama user] menambahkan catatan pada [nama asset]

#### document_uploaded
Emoji: 📎
Judul: Dokumen Baru!
Konten: [nama user] mengupload dokumen pada [nama asset]

#### priority_change
Emoji: ⚠️
Judul: Prioritas Berubah!
Konten: Asset [nama] kini memiliki prioritas: [prioritas]

### Format Pesan Telegram
```
🔄 *Status Berubah!*

Halo [Nama User],
Asset [Nama Asset] ([Kode]) kini berada di stage: [Stage Baru].

📝 Catatan: [catatan jika ada]

🏢 Project: [Nama Project]
📋 Kode Asset: [Kode Asset]

[Tombol: Buka Aplikasi] - hanya jika bukan localhost
```

### Catatan Penting

#### URL Localhost
Telegram menolak URL localhost untuk inline button. Sistem mendeteksi ini:
```php
$appUrl = config('app.url');
if (!str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
    $message->button('Buka Aplikasi', $assetUrl);
}
```

#### Queue
Notifikasi diproses via queue (async). Pastikan menjalankan:
```bash
php artisan queue:work --tries=3
```

---

## 3. TelegramWebhookController.php

Lokasi: app/Http/Controllers/TelegramWebhookController.php

### Deskripsi
Controller untuk menangani webhook dari Telegram bot.

### Bot Commands

#### /start
Response:
```
👋 *Selamat datang, [Nama]!*

Ini adalah bot notifikasi untuk sistem *KJPP Mushofah dan Rekan - Cabang Semarang*.

🆔 *Chat ID Anda:*
`[Chat ID]`

📋 *Cara menghubungkan akun:*
1. Login ke aplikasi web
2. Buka menu *Profil/Profile*
3. Masukkan Chat ID di atas ke kolom *Telegram Chat ID*
4. Klik *Simpan*

✅ Setelah terhubung, Anda akan menerima notifikasi:
• Perubahan status/stage asset
• Catatan baru pada asset
• Upload dokumen baru

Gunakan /help untuk bantuan lebih lanjut.
```

#### /id
Response:
```
🆔 *Chat ID Anda:*

`[Chat ID]`

Copy ID di atas dan paste ke pengaturan profil di aplikasi web.
```

#### /help
Response:
```
📚 *Bantuan Bot KJPP*

*Perintah yang tersedia:*
/start - Memulai dan mendapatkan Chat ID
/id - Mendapatkan Chat ID Anda
/help - Menampilkan bantuan ini

*Tentang Bot ini:*
Bot ini akan mengirimkan notifikasi otomatis dari sistem manajemen asset KJPP Mushofah dan Rekan.

Jika ada pertanyaan, hubungi admin sistem.
```

### Webhook Management

#### setWebhook(url)
- Set webhook URL ke Telegram API
- Perlu akses admin
- URL harus HTTPS (ngrok untuk development)

#### getWebhookInfo()
- Cek status webhook saat ini
- Perlu akses admin

#### deleteWebhook()
- Hapus webhook
- Bot tidak akan menerima update lagi

---

## Alur Notifikasi

### Skenario: User memindahkan asset di kanban board

1. User drag asset dari stage A ke stage B
2. Frontend memanggil POST /kanban/assets/{id}/move
3. AssetController::moveStage() dipanggil
4. Asset di-update ke stage baru
5. KanbanNotificationService::notifyStageChange() dipanggil
6. Service membuat notifikasi database untuk semua user
7. Service membuat AssessmentUpdated notification untuk Telegram
8. Notification di-queue ke database
9. Queue worker mengambil job dan mengirim ke Telegram API
10. User dengan telegram_chat_id menerima notifikasi di Telegram

### Diagram Alur
```
User Action
    │
    ▼
Controller
    │
    ▼
KanbanNotificationService
    │
    ├──► Database Notification (langsung)
    │
    └──► AssessmentUpdated (queue)
              │
              ▼
         Queue Worker
              │
              ▼
         Telegram API
              │
              ▼
         User Telegram
```

---

## Environment Variables

File .env yang diperlukan:

```env
# Queue driver (harus database untuk notifikasi async)
QUEUE_CONNECTION=database

# Telegram Bot Token dari @BotFather
TELEGRAM_BOT_TOKEN=your_bot_token_here

# APP_URL harus benar untuk link di notifikasi
APP_URL=http://your-ngrok-url.ngrok-free.app
```

---

## Troubleshooting

### Notifikasi Telegram tidak sampai

1. Cek queue worker berjalan:
   ```bash
   php artisan queue:work --tries=3
   ```

2. Cek pending jobs:
   ```bash
   php artisan tinker --execute="echo DB::table('jobs')->count();"
   ```

3. Cek failed jobs:
   ```bash
   php artisan queue:failed
   ```

4. Cek telegram_chat_id tersimpan:
   ```bash
   php artisan tinker --execute="App\Models\User::pluck('telegram_chat_id','name');"
   ```

5. Cek log error:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Notifikasi duplikat

Sistem sudah menangani deduplikasi dengan `->unique('telegram_chat_id')`. Jika masih duplikat, cek apakah webhook dipanggil berkali-kali.

### Error "localhost URL rejected"

Telegram menolak URL localhost. Gunakan ngrok dan update APP_URL di .env.
