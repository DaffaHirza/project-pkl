#!/bin/bash

# Script untuk update Telegram webhook setelah restart ngrok
# Usage:
#   ./scripts/update-webhook.sh https://xxxx.ngrok-free.app
#
# Yang dilakukan:
# - Baca TELEGRAM_BOT_TOKEN dari .env
# - Update APP_URL di .env
# - Update TELEGRAM_WEBHOOK_URL di .env
# - Update webhook Telegram ke /api/telegram/webhook

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$PROJECT_ROOT/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: file .env tidak ditemukan di $PROJECT_ROOT"
    exit 1
fi

read_env() {
    local key="$1"
    grep -E "^${key}=" "$ENV_FILE" | tail -n 1 | cut -d '=' -f2- | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

set_env() {
    local key="$1"
    local value="$2"

    if grep -qE "^${key}=" "$ENV_FILE"; then
        if [[ "$OSTYPE" == "darwin"* ]]; then
            sed -i '' "s|^${key}=.*|${key}=${value}|" "$ENV_FILE"
        else
            sed -i "s|^${key}=.*|${key}=${value}|" "$ENV_FILE"
        fi
    else
        echo "${key}=${value}" >> "$ENV_FILE"
    fi
}

TELEGRAM_BOT_TOKEN="$(read_env TELEGRAM_BOT_TOKEN)"

if [ -z "$TELEGRAM_BOT_TOKEN" ]; then
    echo "❌ Error: TELEGRAM_BOT_TOKEN tidak ditemukan di .env"
    exit 1
fi

if [ -z "$1" ]; then
    echo "❌ Error: URL ngrok tidak diberikan"
    echo ""
    echo "Cara pakai:"
    echo "  ./scripts/update-webhook.sh https://xxxx.ngrok-free.app"
    exit 1
fi

NGROK_URL="$1"
NGROK_URL="${NGROK_URL%/}"

if [[ ! "$NGROK_URL" =~ ^https:// ]]; then
    echo "❌ Error: URL ngrok harus diawali https://"
    echo "Contoh:"
    echo "  ./scripts/update-webhook.sh https://xxxx.ngrok-free.app"
    exit 1
fi

WEBHOOK_URL="${NGROK_URL}/api/telegram/webhook"

echo "🔄 Updating .env..."
set_env "APP_URL" "$NGROK_URL"
set_env "TELEGRAM_WEBHOOK_URL" "$WEBHOOK_URL"

# Optional: update redirect URI untuk OAuth kalau nanti kamu generate ulang token.
# Catatan: Google Cloud Console tetap harus ditambahkan manual jika redirect URI berubah.
set_env "GOOGLE_DRIVE_REDIRECT_URI" "${NGROK_URL}/google-drive/callback"

echo "   ✅ APP_URL=${NGROK_URL}"
echo "   ✅ TELEGRAM_WEBHOOK_URL=${WEBHOOK_URL}"
echo "   ✅ GOOGLE_DRIVE_REDIRECT_URI=${NGROK_URL}/google-drive/callback"
echo ""

echo "🔄 Mengupdate webhook Telegram..."
echo "   URL: $WEBHOOK_URL"
echo ""

RESULT=$(curl -sS -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook" \
    --data-urlencode "url=${WEBHOOK_URL}")

if echo "$RESULT" | grep -q '"ok":true'; then
    echo "✅ Webhook berhasil diupdate!"
    echo ""

    echo "🧹 Clearing Laravel config..."
    cd "$PROJECT_ROOT"
    php artisan config:clear >/dev/null 2>&1 || true
    php artisan route:clear >/dev/null 2>&1 || true
    php artisan view:clear >/dev/null 2>&1 || true
    echo "✅ Laravel config/cache cleared"
    echo ""

    echo "📋 Info webhook:"
    curl -sS "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo" \
        | python3 -m json.tool 2>/dev/null \
        || curl -sS "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"

    echo ""
else
    echo "❌ Gagal update webhook:"
    echo "$RESULT"
    exit 1
fi