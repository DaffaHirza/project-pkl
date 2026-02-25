<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Telegram Webhook - no auth required (Telegram will call this)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);

// Telegram management routes (protected - admin only)
Route::middleware(['auth', 'admin'])->prefix('telegram')->group(function () {
    Route::post('/set-webhook', [TelegramWebhookController::class, 'setWebhook']);
    Route::get('/webhook-info', [TelegramWebhookController::class, 'getWebhookInfo']);
    Route::post('/delete-webhook', [TelegramWebhookController::class, 'deleteWebhook']);
});
