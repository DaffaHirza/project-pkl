#!/bin/bash

# Script untuk update Telegram webhook setelah restart ngrok
# Usage: ./update-webhook.sh https://xxxx.ngrok-free.app

TELEGRAM_BOT_TOKEN="8797700772:AAFSAVH54S0TrJg9ha6DyihnsawXqO08Qp8"

if [ -z "$1" ]; then
    echo "❌ Error: URL ngrok tidak diberikan"
    echo ""
    echo "Cara pakai:"
    echo "  ./update-webhook.sh https://xxxx.ngrok-free.app"
    echo ""
    echo "Contoh:"
    echo "  ./update-webhook.sh https://274f-2407-0-3006-5836-cd21-13af-a3d6-8848.ngrok-free.app"
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
    if [ -f ".env" ]; then
        # Replace APP_URL line
        sed -i '' "s|^APP_URL=.*|APP_URL=${NGROK_URL}|" .env
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
