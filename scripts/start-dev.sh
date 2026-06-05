#!/bin/bash

# Script untuk memulai development environment otomatis
# Usage:
#   ./scripts/start-dev.sh
#
# Yang dilakukan:
# - Start PostgreSQL
# - Start Laravel server di port 8000
# - Start ngrok http 8000
# - Ambil URL ngrok otomatis
# - Update APP_URL dan Telegram webhook

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

APP_PORT=8000
LARAVEL_HOST="127.0.0.1"
LARAVEL_LOG="$PROJECT_ROOT/storage/logs/serve.log"
NGROK_LOG="$PROJECT_ROOT/storage/logs/ngrok.log"
PID_FILE="$PROJECT_ROOT/storage/logs/dev.pids"

cd "$PROJECT_ROOT"

echo "🚀 KJPP Mushofah - Development Startup"
echo "======================================="
echo ""

mkdir -p "$PROJECT_ROOT/storage/logs"
: > "$PID_FILE"

# ==================================================
# 1. PostgreSQL
# ==================================================
echo "1️⃣  Checking PostgreSQL..."

if command -v brew >/dev/null 2>&1; then
    if brew services list | grep -q "postgresql.*started"; then
        echo "   ✅ PostgreSQL sudah running"
    else
        echo "   🔄 Starting PostgreSQL..."
        brew services start postgresql
    fi
else
    echo "   ⚠️  Homebrew tidak ditemukan. Lewati auto-start PostgreSQL."
    echo "   Pastikan PostgreSQL sudah running manual."
fi

echo ""

# ==================================================
# 2. Laravel Server
# ==================================================
echo "2️⃣  Checking Laravel server on port $APP_PORT..."

if lsof -iTCP:$APP_PORT -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "   ✅ Port $APP_PORT sudah aktif. Laravel server kemungkinan sudah running."
else
    echo "   🔄 Starting Laravel server..."
    nohup php artisan serve --host="$LARAVEL_HOST" --port="$APP_PORT" > "$LARAVEL_LOG" 2>&1 &
    LARAVEL_PID=$!
    echo "laravel:$LARAVEL_PID" >> "$PID_FILE"
    echo "   ✅ Laravel server started. PID: $LARAVEL_PID"
    echo "   📄 Log: $LARAVEL_LOG"
fi

echo ""

# ==================================================
# 3. ngrok
# ==================================================
echo "3️⃣  Checking ngrok..."

if ! command -v ngrok >/dev/null 2>&1; then
    echo "   ❌ ngrok belum terinstall atau belum ada di PATH."
    echo "   Install dulu ngrok, lalu jalankan ulang script ini."
    exit 1
fi

if curl -s http://127.0.0.1:4040/api/tunnels >/dev/null 2>&1; then
    echo "   ✅ ngrok sudah running."
else
    echo "   🔄 Starting ngrok http $APP_PORT..."
    nohup ngrok http "$APP_PORT" > "$NGROK_LOG" 2>&1 &
    NGROK_PID=$!
    echo "ngrok:$NGROK_PID" >> "$PID_FILE"
    echo "   ✅ ngrok started. PID: $NGROK_PID"
    echo "   📄 Log: $NGROK_LOG"
fi

echo ""

# ==================================================
# 4. Ambil URL ngrok
# ==================================================
echo "4️⃣  Mengambil URL ngrok..."

NGROK_URL=""

for i in {1..30}; do
    NGROK_URL=$(python3 - <<'PY'
import json
import urllib.request

try:
    with urllib.request.urlopen("http://127.0.0.1:4040/api/tunnels", timeout=2) as response:
        data = json.loads(response.read().decode("utf-8"))

    tunnels = data.get("tunnels", [])

    https_urls = [
        tunnel.get("public_url")
        for tunnel in tunnels
        if tunnel.get("public_url", "").startswith("https://")
    ]

    print(https_urls[0] if https_urls else "")
except Exception:
    print("")
PY
)

    if [ -n "$NGROK_URL" ]; then
        break
    fi

    sleep 1
done

if [ -z "$NGROK_URL" ]; then
    echo "   ❌ Gagal mengambil URL ngrok."
    echo "   Cek log:"
    echo "   $NGROK_LOG"
    exit 1
fi

echo "   ✅ URL ngrok ditemukan:"
echo "   $NGROK_URL"
echo ""

# ==================================================
# 5. Update webhook dan APP_URL
# ==================================================
echo "5️⃣  Updating APP_URL dan Telegram webhook..."

"$SCRIPT_DIR/update-webhook.sh" "$NGROK_URL"

echo ""

# ==================================================
# 6. Clear Laravel config
# ==================================================
echo "6️⃣  Clearing Laravel config/cache..."

php artisan config:clear >/dev/null 2>&1 || true
php artisan route:clear >/dev/null 2>&1 || true
php artisan view:clear >/dev/null 2>&1 || true

echo "   ✅ Laravel cache cleared"
echo ""

echo "======================================="
echo "✅ Development environment ready!"
echo ""
echo "🌐 App URL:"
echo "   $NGROK_URL"
echo ""
echo "🧪 Local URL:"
echo "   http://127.0.0.1:$APP_PORT"
echo ""
echo "📄 Logs:"
echo "   Laravel: $LARAVEL_LOG"
echo "   ngrok:   $NGROK_LOG"
echo ""
echo "📝 Jika ingin stop manual:"
echo "   lsof -iTCP:$APP_PORT -sTCP:LISTEN -t | xargs kill"
echo "   pkill ngrok"
echo ""
echo "⚠️  Google Drive upload tidak perlu setup ulang setiap ngrok berubah,"
echo "   selama GOOGLE_DRIVE_REFRESH_TOKEN sudah valid."
echo "======================================="