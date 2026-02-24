# Setup Ngrok untuk Development

Dokumentasi ini menjelaskan cara menggunakan ngrok untuk mengekspos aplikasi Laravel ke internet, yang diperlukan untuk:
- Testing webhook Telegram
- Demo aplikasi ke klien
- Testing dari device lain

## Prerequisites

- [Ngrok](https://ngrok.com/) terinstall
- Akun ngrok (gratis)
- PHP & Composer terinstall
- Node.js & NPM terinstall

## Instalasi Ngrok

### macOS (via Homebrew)
```bash
brew install ngrok
```

### Manual Download
1. Download dari https://ngrok.com/download
2. Extract dan pindahkan ke `/usr/local/bin/`

### Setup Auth Token
```bash
ngrok config add-authtoken YOUR_AUTH_TOKEN
```
> Auth token bisa didapat dari https://dashboard.ngrok.com/get-started/your-authtoken

---

## Langkah-langkah Menjalankan

### 1. Jalankan Laravel Server

```bash
cd /path/to/project-pkl
php artisan serve --port=8000
```

Server akan berjalan di `http://localhost:8000`

### 2. Jalankan Vite (Frontend Assets)

Buka terminal baru:
```bash
npm run dev
```

Vite akan berjalan di `http://localhost:5173`

### 3. Jalankan Queue Worker

Buka terminal baru:
```bash
php artisan queue:work --tries=3
```

Queue worker diperlukan untuk memproses notifikasi Telegram.

### 4. Jalankan Ngrok

Buka terminal baru:
```bash
ngrok http 8000
```

Output akan seperti ini:
```
Session Status                online
Account                       your-email@example.com
Version                       3.x.x
Region                        United States (us)
Forwarding                    https://abc123.ngrok-free.app -> http://localhost:8000
```

**Catat URL `https://abc123.ngrok-free.app`** - ini adalah URL publik aplikasi Anda.

---

## Konfigurasi Aplikasi untuk Ngrok

### Update .env

Tambahkan/update variabel berikut di file `.env`:

```env
APP_URL=https://abc123.ngrok-free.app
```

> Ganti `abc123.ngrok-free.app` dengan URL ngrok Anda yang sebenarnya.

### Clear Cache

Setelah update `.env`:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## Mengakses Aplikasi

### Via Browser
Buka URL ngrok di browser:
```
https://abc123.ngrok-free.app
```

> **Note:** Ngrok free tier akan menampilkan halaman warning. Klik "Visit Site" untuk melanjutkan.

### Testing dari Device Lain
Gunakan URL ngrok yang sama untuk mengakses dari HP atau komputer lain.

---

## Troubleshooting

### Error: "Vite manifest not found"
Pastikan `npm run dev` berjalan di terminal terpisah.

### Error: "Connection refused"
- Pastikan `php artisan serve --port=8000` berjalan
- Pastikan ngrok pointing ke port yang benar: `ngrok http 8000`

### Ngrok URL berubah setiap restart
Ini normal untuk ngrok free tier. Setiap restart ngrok, URL akan berubah. 
Untuk URL tetap, gunakan ngrok paid plan atau custom domain.

### Session/Login tidak bekerja
Clear session dan cache:
```bash
php artisan session:clear
php artisan cache:clear
```

---

## Tips

1. **Jangan share URL ngrok** ke publik karena memberikan akses ke localhost Anda
2. **Matikan ngrok** setelah selesai development
3. **Gunakan ngrok inspect** di `http://localhost:4040` untuk debug request

---

## Ringkasan Command

```bash
# Terminal 1 - Laravel Server
php artisan serve --port=8000

# Terminal 2 - Vite
npm run dev

# Terminal 3 - Queue Worker
php artisan queue:work --tries=3

# Terminal 4 - Ngrok
ngrok http 8000
```
