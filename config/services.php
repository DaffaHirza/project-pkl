<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY', ''),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1/chat/completions'),
        'model' => env('OPENROUTER_MODEL', 'nvidia/nemotron-3-super-120b-a12b:free'),
        'timeout' => env('OPENROUTER_TIMEOUT', 120),
    ],

    'google_drive' => [
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'redirect_uri' => env('GOOGLE_DRIVE_REDIRECT_URI'),
    ],

];
