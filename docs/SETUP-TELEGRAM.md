# Setup Telegram

Cara mengatur notifikasi Telegram.


## 1. Dapat Chat ID

1. Buka Telegram
2. Cari bot: **@appraisal_notif_bot** (atau sesuai .env)
3. Klik **Start** atau ketik `/start`
4. Bot akan kasih **Chat ID** kamu (angka seperti `827770943`)


## 2. Simpan Chat ID di Aplikasi

1. Login ke aplikasi
2. Buka **Profile** (klik nama di pojok kanan atas)
3. Masukkan Chat ID ke field **Telegram Chat ID**
4. Klik **Save**


## 3. Test Notifikasi

1. Buka Kanban Board
2. Drag asset ke stage lain
3. Cek Telegram - harusnya ada notifikasi masuk

**Catatan:** Notifikasi tidak dikirim ke diri sendiri. Jadi kalau kamu yang pindah asset, kamu tidak akan terima notifikasi. User lain yang punya telegram_chat_id yang akan terima.


## Troubleshooting

**Tidak ada notifikasi masuk?**
- Pastikan queue worker jalan: `php artisan queue:work`
- Pastikan telegram_chat_id sudah disimpan di profile
- Cek webhook sudah diset (lihat SETUP-NGROK.md)

**Cek status webhook:**
```bash
source .env
curl "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"
```


## Bot Commands

- `/start` - Mulai & dapatkan Chat ID
- `/id` - Tampilkan Chat ID saja
- `/help` - Bantuan
