# Setup Ngrok

Ngrok dipakai untuk:
- Webhook Telegram (wajib untuk development)
- Demo ke orang lain
- Testing dari HP


## Install Ngrok

**macOS:**
```bash
brew install ngrok
```

**Manual:**
Download dari https://ngrok.com/download

**Setup auth token:**
```bash
ngrok config add-authtoken YOUR_TOKEN
```
Token dapat dari https://dashboard.ngrok.com/get-started/your-authtoken


## Cara Pakai

**1. Jalankan aplikasi dulu:**
```bash
# Terminal 1
php artisan serve --port=8000

# Terminal 2
npm run dev

# Terminal 3
php artisan queue:work
```

**2. Jalankan ngrok:**
```bash
ngrok http 8000
```

**3. Copy URL yang muncul:**
```
Forwarding    https://abc123.ngrok-free.app -> http://localhost:8000
```

**4. Update APP_URL di .env:**
```
APP_URL=https://abc123.ngrok-free.app
```

**5. Set webhook Telegram:**
```bash
source .env
curl -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook?url=https://abc123.ngrok-free.app/api/telegram/webhook"
```

Atau pakai script:
```bash
./scripts/update-webhook.sh https://abc123.ngrok-free.app
```


## Tips

- URL ngrok berubah setiap restart (kecuali pakai domain berbayar)
- Setelah restart ngrok, update webhook lagi
- Bisa akses dashboard ngrok di http://localhost:4040


## Script Helper

Ada script di `scripts/` folder:

```bash
# Checklist startup
./scripts/start-dev.sh

# Update webhook
./scripts/update-webhook.sh https://xxx.ngrok-free.app
```
