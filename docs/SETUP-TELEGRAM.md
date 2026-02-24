# Setup Notifikasi Telegram

Dokumentasi ini menjelaskan cara mengatur notifikasi Telegram untuk menerima update penilaian asset secara real-time.

## Fitur Notifikasi Telegram

Sistem akan mengirim notifikasi ke Telegram saat:
- ✅ **Stage asset berubah** (dipindahkan antar stage di kanban)
- ✅ **Catatan baru ditambahkan** pada asset

---

## Bagian 1: Mendapatkan Telegram Chat ID

### Langkah 1: Buka Telegram
Buka aplikasi Telegram di HP atau desktop.

### Langkah 2: Cari Bot KJPP
Cari bot dengan username:
```
@kjpp_mushofah_bot
```

Atau klik link langsung: https://t.me/kjpp_mushofah_bot

### Langkah 3: Start Bot
1. Klik tombol **Start** atau ketik `/start`
2. Bot akan membalas dengan pesan selamat datang dan **Chat ID** Anda

Contoh balasan:
```
🎉 Selamat datang di Bot KJPP Mushofah dan Rekan!

📋 Chat ID Anda: 827770943

Gunakan Chat ID ini di halaman Profile aplikasi untuk menerima notifikasi.

Perintah yang tersedia:
/start - Mulai bot dan dapatkan Chat ID
/help - Tampilkan bantuan
/id - Tampilkan Chat ID Anda
```

### Langkah 4: Catat Chat ID
**Catat nomor Chat ID** yang diberikan (contoh: `827770943`)

---

## Bagian 2: Menyimpan Chat ID di Aplikasi

### Langkah 1: Login ke Aplikasi
Login ke aplikasi KJPP dengan akun Anda:
```
URL: http://localhost:8000/login
```

### Langkah 2: Buka Halaman Profile
1. Klik nama/avatar di pojok kanan atas
2. Pilih **Profile** atau langsung akses `/profile`

### Langkah 3: Masukkan Chat ID
1. Scroll ke bagian **Telegram Chat ID**
2. Masukkan Chat ID yang didapat dari bot
3. Klik tombol **Save**

### Langkah 4: Verifikasi
Setelah disimpan, akan muncul pesan:
```
✅ Telegram sudah terhubung! Anda akan menerima notifikasi.
```

---

## Bagian 3: Testing Notifikasi

### Test 1: Pindah Stage Asset
1. Buka halaman Kanban
2. Drag salah satu asset dari satu stage ke stage lain
3. Cek Telegram - notifikasi akan masuk

Contoh notifikasi:
```
🔄 Status Berubah!

Halo [Nama Anda],
Asset [Nama Asset] ([Kode]) kini berada di stage: [Stage Baru].

🏢 Project: [Nama Project]
📋 Kode Asset: [Kode Asset]
```

### Test 2: Tambah Catatan
1. Buka detail asset (klik pada asset)
2. Scroll ke bagian **Catatan Internal**
3. Ketik catatan dan klik tambah
4. Cek Telegram - notifikasi akan masuk

Contoh notifikasi:
```
📝 Catatan Baru!

Halo [Nama Anda],
[Nama User] menambahkan catatan pada [Nama Asset].

💬 "[Isi catatan...]"

🏢 Project: [Nama Project]
📋 Kode Asset: [Kode Asset]
```

---

## Troubleshooting

### Notifikasi tidak masuk

**1. Pastikan Queue Worker berjalan**
```bash
php artisan queue:work --tries=3
```

**2. Cek Chat ID tersimpan**
```bash
php artisan tinker --execute="echo App\Models\User::where('email', 'email@anda.com')->first()->telegram_chat_id;"
```

**3. Cek pending jobs**
```bash
php artisan tinker --execute="echo DB::table('jobs')->count();"
```

**4. Cek failed jobs**
```bash
php artisan queue:failed
```

**5. Retry failed jobs**
```bash
php artisan queue:retry all
```

### Bot tidak merespon

1. Pastikan bot sudah di-start dengan `/start`
2. Coba ketik `/id` untuk mendapatkan Chat ID
3. Pastikan bot username benar: `@kjpp_mushofah_bot`

### Notifikasi duplikat

Jika beberapa akun menggunakan Chat ID yang sama, sistem sudah otomatis mengirim hanya 1 notifikasi per Chat ID unik.

---

## Command Bot yang Tersedia

| Command | Fungsi |
|---------|--------|
| `/start` | Mulai bot dan dapatkan Chat ID |
| `/help` | Tampilkan bantuan |
| `/id` | Tampilkan Chat ID Anda |

---

## Konfigurasi Bot (Untuk Developer)

### Environment Variables

File `.env` harus memiliki:
```env
TELEGRAM_BOT_TOKEN=8797700772:AAFSAVH54S0TrJg9ha6DyihnsawXqO08Qp8
```

### Setup Webhook (Opsional)

Jika menggunakan ngrok dan ingin bot merespon:

```bash
# Set webhook
curl -X POST "https://api.telegram.org/bot{TOKEN}/setWebhook" \
  -d "url=https://YOUR-NGROK-URL.ngrok-free.app/api/telegram/webhook"

# Cek status webhook
curl "https://api.telegram.org/bot{TOKEN}/getWebhookInfo"

# Hapus webhook
curl -X POST "https://api.telegram.org/bot{TOKEN}/deleteWebhook"
```

### File Terkait

- `app/Notifications/AssessmentUpdated.php` - Class notifikasi
- `app/Services/KanbanNotificationService.php` - Service pengirim notifikasi
- `app/Http/Controllers/TelegramWebhookController.php` - Handler webhook bot
- `config/services.php` - Konfigurasi Telegram

---

## Ringkasan

1. **Dapatkan Chat ID** → Buka @kjpp_mushofah_bot, ketik `/start`
2. **Simpan Chat ID** → Buka Profile di aplikasi, masukkan Chat ID
3. **Jalankan Queue** → `php artisan queue:work --tries=3`
4. **Test** → Pindahkan asset atau tambah catatan, cek Telegram
