<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming Telegram webhook
     */
    public function handle(Request $request)
    {
        $update = $request->all();
        
        Log::info('Telegram Webhook received', $update);

        // Handle message updates
        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $firstName = $message['from']['first_name'] ?? 'User';

            // Handle /start command
            if (str_starts_with($text, '/start')) {
                $this->sendMessage($chatId, $this->getStartMessage($chatId, $firstName));
            }
            // Handle /help command
            elseif (str_starts_with($text, '/help')) {
                $this->sendMessage($chatId, $this->getHelpMessage());
            }
            // Handle /id command (alternative way to get ID)
            elseif (str_starts_with($text, '/id')) {
                $this->sendMessage($chatId, "🆔 *Chat ID Anda:*\n\n`{$chatId}`\n\nCopy ID di atas dan paste ke pengaturan profil di aplikasi web.");
            }
            // Handle unknown commands
            elseif (str_starts_with($text, '/')) {
                $this->sendMessage($chatId, "❓ Perintah tidak dikenali.\n\nGunakan /help untuk melihat daftar perintah yang tersedia.");
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Get welcome message for /start command
     */
    private function getStartMessage(string $chatId, string $firstName): string
    {
        return "👋 *Selamat datang, {$firstName}!*\n\n".
               "Ini adalah bot notifikasi untuk sistem *KJPP Mushofah dan Rekan - Cabang Semarang*.\n\n".
               "🆔 *Chat ID Anda:*\n`{$chatId}`\n\n".
               "📋 *Cara menghubungkan akun:*\n".
               "1. Login ke aplikasi web\n".
               "2. Buka menu *Profil/Profile*\n".
               "3. Masukkan Chat ID di atas ke kolom *Telegram Chat ID*\n".
               "4. Klik *Simpan*\n\n".
               "✅ Setelah terhubung, Anda akan menerima notifikasi:\n".
               "• Perubahan status/stage asset\n".
               "• Catatan baru pada asset\n".
               "• Upload dokumen baru\n\n".
               "Gunakan /help untuk bantuan lebih lanjut.";
    }

    /**
     * Get help message
     */
    private function getHelpMessage(): string
    {
        return "📚 *Bantuan Bot KJPP*\n\n".
               "*Perintah yang tersedia:*\n".
               "/start - Memulai dan mendapatkan Chat ID\n".
               "/id - Mendapatkan Chat ID Anda\n".
               "/help - Menampilkan bantuan ini\n\n".
               "*Tentang Bot ini:*\n".
               "Bot ini akan mengirimkan notifikasi otomatis dari sistem manajemen asset KJPP Mushofah dan Rekan.\n\n".
               "Jika ada pertanyaan, hubungi admin sistem.";
    }

    /**
     * Send message to Telegram
     */
    private function sendMessage(string $chatId, string $text, array $options = []): void
    {
        $token = config('services.telegram-bot-api.token');
        
        if (empty($token)) {
            Log::error('Telegram bot token not configured');
            return;
        }

        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ], $options);

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
            
            if (!$response->successful()) {
                Log::error('Failed to send Telegram message', [
                    'response' => $response->json(),
                    'chat_id' => $chatId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram API error', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);
        }
    }

    /**
     * Set webhook URL (call this once to register webhook with Telegram)
     */
    public function setWebhook()
    {
        $token = config('services.telegram-bot-api.token');
        $webhookUrl = url('/api/telegram/webhook');
        
        if (empty($token)) {
            return response()->json(['error' => 'Telegram bot token not configured'], 500);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
                'url' => $webhookUrl,
                'allowed_updates' => ['message'],
            ]);

            return response()->json([
                'success' => $response->successful(),
                'response' => $response->json(),
                'webhook_url' => $webhookUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        $token = config('services.telegram-bot-api.token');
        
        if (empty($token)) {
            return response()->json(['error' => 'Telegram bot token not configured'], 500);
        }

        try {
            $response = Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete webhook (for testing with getUpdates)
     */
    public function deleteWebhook()
    {
        $token = config('services.telegram-bot-api.token');
        
        if (empty($token)) {
            return response()->json(['error' => 'Telegram bot token not configured'], 500);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
