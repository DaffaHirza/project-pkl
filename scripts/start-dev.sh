#!/bin/bash

# Script untuk memulai development environment
# Usage: ./start-dev.sh

echo "🚀 KJPP Mushofah - Development Startup"
echo "======================================="
echo ""

# Check PostgreSQL
echo "1️⃣  Checking PostgreSQL..."
if brew services list | grep -q "postgresql.*started"; then
    echo "   ✅ PostgreSQL sudah running"
else
    echo "   🔄 Starting PostgreSQL..."
    brew services start postgresql
fi
echo ""

# Instructions
echo "2️⃣  Buka terminal baru dan jalankan:"
echo "   php artisan serve --port=8000"
echo ""

echo "3️⃣  Buka terminal baru lagi dan jalankan:"
echo "   php artisan queue:work"
echo ""

echo "4️⃣  Buka terminal baru lagi dan jalankan:"
echo "   ngrok http 8000"
echo ""

echo "5️⃣  Setelah ngrok jalan, copy URL-nya lalu jalankan:"
echo "   ./update-webhook.sh https://xxxx.ngrok-free.app"
echo ""

echo "======================================="
echo "📝 Quick Commands:"
echo "   - Update webhook:  ./update-webhook.sh <ngrok-url>"
echo "   - Check webhook:   curl -s 'https://api.telegram.org/bot8797700772:AAFSAVH54S0TrJg9ha6DyihnsawXqO08Qp8/getWebhookInfo' | python3 -m json.tool"
echo ""
