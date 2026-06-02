#!/bin/bash

echo "🛑 Stopping KJPP development environment..."
echo "=========================================="

APP_PORT=8000

echo "1️⃣  Stopping Laravel server on port $APP_PORT..."

PIDS=$(lsof -iTCP:$APP_PORT -sTCP:LISTEN -t 2>/dev/null)

if [ -n "$PIDS" ]; then
    echo "$PIDS" | xargs kill
    echo "   ✅ Laravel server stopped"
else
    echo "   ℹ️  Tidak ada Laravel server di port $APP_PORT"
fi

echo ""

echo "2️⃣  Stopping ngrok..."

if pgrep ngrok >/dev/null 2>&1; then
    pkill ngrok
    echo "   ✅ ngrok stopped"
else
    echo "   ℹ️  ngrok tidak sedang running"
fi

echo ""

echo "3️⃣  Checking remaining processes..."

if lsof -iTCP:$APP_PORT -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "   ⚠️  Masih ada process di port $APP_PORT:"
    lsof -iTCP:$APP_PORT -sTCP:LISTEN
else
    echo "   ✅ Port $APP_PORT sudah kosong"
fi

if pgrep ngrok >/dev/null 2>&1; then
    echo "   ⚠️  ngrok masih running:"
    pgrep -fl ngrok
else
    echo "   ✅ ngrok sudah mati"
fi

echo ""
echo "✅ Development environment stopped"
