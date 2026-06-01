# Services & Notifications


## KanbanNotificationService

Lokasi: `app/Services/KanbanNotificationService.php`

Service untuk kirim notifikasi. Kirim ke:
1. Database (notifikasi in-app)
2. Telegram (jika user punya telegram_chat_id)

**Method:**

**notifyStageChange(asset, oldStage, newStage, changedBy, note)**
- Dipanggil saat asset pindah stage
- Kirim ke database + Telegram

**notifyDocumentUploaded(asset, fileName, uploadedBy)**
- Dipanggil saat upload dokumen
- Kirim ke database saja

**notifyNoteAdded(asset, note, addedBy)**
- Dipanggil saat tambah catatan
- Kirim ke database + Telegram

**notifyAssetCreated(asset, createdBy)**
- Dipanggil saat buat asset baru
- Kirim ke database saja

**notifyClientCreated(client, createdBy)**
- Dipanggil saat buat client baru
- Kirim ke database saja

**notifyPriorityCritical(asset, changedBy)**
- Dipanggil saat priority jadi critical
- Kirim ke database saja

**Catatan penting:**
- Notifikasi tidak dikirim ke user yang melakukan aksi (exclude self)
- Jika ada telegram_chat_id yang sama, hanya kirim 1 pesan (deduplikasi)


## AssessmentUpdated Notification

Lokasi: `app/Notifications/AssessmentUpdated.php`

Laravel Notification class untuk kirim ke Telegram.

**Tipe notifikasi:**
- stage_change - 🔄 Status Berubah!
- new_note - 📝 Catatan Baru!
- document_uploaded - 📎 Dokumen Baru!
- priority_change - ⚠️ Prioritas Berubah!

**Format pesan Telegram:**

```
🔄 *Status Berubah!*

Halo [Nama User],
Asset [Nama Asset] kini berada di stage: [Stage Baru].

📝 Catatan: [catatan jika ada]

🏢 Project: [Nama Project]

[Tombol: Buka Aplikasi]
```

**Catatan:**
- Tombol "Buka Aplikasi" tidak muncul jika APP_URL adalah localhost
- Notifikasi diproses via queue (async)


## Telegram Bot Commands

Bot: @kjpp_mushofah_bot (atau sesuai .env)

**/start**
- Pesan selamat datang
- Tampilkan Chat ID
- Instruksi cara menghubungkan akun

**/id**
- Tampilkan Chat ID saja

**/help**
- Bantuan penggunaan bot


## Cara Kerja Notifikasi

1. User melakukan aksi (pindah stage, tambah catatan, dll)
2. Controller memanggil KanbanNotificationService
3. Service membuat:
   - Record di tabel notifications (in-app)
   - Job di queue untuk AssessmentUpdated (Telegram)
4. Queue worker memproses job
5. AssessmentUpdated mengirim pesan ke Telegram

**Prerequisite:**
- Queue worker harus jalan: `php artisan queue:work`
- User harus punya telegram_chat_id di profile
- Bot token harus valid di .env
