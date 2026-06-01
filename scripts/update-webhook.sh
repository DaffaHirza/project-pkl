#!/bin/bash

# Script untuk update Telegram webhook setelah restart ngrok
# Usage: ./scripts/update-webhook.sh https://xxxx.ngrok-free.app
#
# Token dibaca dari file .env (TELEGRAM_BOT_TOKEN)

# Get script directory and project root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Read token from .env
if [ -f "$PROJECT_ROOT/.env" ]; then
    TELEGRAM_BOT_TOKEN=$(grep "^TELEGRAM_BOT_TOKEN=" "$PROJECT_ROOT/.env" | cut -d '=' -f2)
fi

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

NGROK_URL=$1
WEBHOOK_URL="${NGROK_URL}/api/telegram/webhook"

echo "🔄 Mengupdate webhook Telegram..."
echo "   URL: $WEBHOOK_URL"
echo ""

# Set webhook
RESULT=$(curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook?url=${WEBHOOK_URL}")

if echo "$RESULT" | grep -q '"ok":true'; then
    echo "✅ Webhook berhasil diupdate!"
    echo ""
    
    # Update .env file
    if [ -f "$PROJECT_ROOT/.env" ]; then
        sed -i '' "s|^APP_URL=.*|APP_URL=${NGROK_URL}|" "$PROJECT_ROOT/.env"
        echo "✅ .env APP_URL sudah diupdate"
    fi
    
    echo ""
    echo "📋 Info webhook:"
    curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo" | python3 -m json.tool 2>/dev/null || curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"
else
    echo "❌ Gagal update webhook:"
    echo "$RESULT"
    exit 1
fi
