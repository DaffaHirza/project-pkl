<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClientService
{
    public function analyze(string $promptFinal, string $imageData = '', bool $isImage = false, string $mimeType = ''): string
    {
        return $this->openrouter($promptFinal, $imageData, $isImage, $mimeType);
        // return $this->geminiAI($promptFinal, $imageData, $isImage, $mimeType);
        //return $this->analisisAILMStudio($promptFinal, $imageData);
    }

    private function openrouter(string $promptFinal, string $imageData = '', bool $isImage = false, string $mimeType = ''): string
    {
        $apiKey = (string) config('services.openrouter.key', '');
        if (trim($apiKey) === '') {
            throw new \Exception('OpenRouter API key not configured');
        }

        $apiUrl = (string) config('services.openrouter.base_url', 'https://openrouter.ai/api/v1/chat/completions');
        $timeout = (int) config('services.openrouter.timeout', 120);
        if ($timeout <= 0) {
            $timeout = 120;
        }

        $configuredModel = trim((string) config('services.openrouter.model', 'nvidia/nemotron-3-super-120b-a12b:free'));
        $freeModels = config('services.openrouter.free_models', []);
        if (is_string($freeModels)) {
            $freeModels = array_map('trim', explode(',', $freeModels));
        }

        if (!is_array($freeModels)) {
            $freeModels = [];
        }

        $fallbackModels = array_values(array_filter(array_map(
            fn($model) => trim((string) $model),
            $freeModels
        )));

        $models = array_values(array_unique(array_filter([
            $configuredModel,
            ...$fallbackModels,
        ])));

        if (empty($models)) {
            $models = ['nvidia/nemotron-3-super-120b-a12b:free'];
        }

        if ($isImage) {
            $mime = $mimeType !== '' ? $mimeType : 'image/jpeg';
            $messages = [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $promptFinal,
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => "data:{$mime};base64,{$imageData}",
                        ],
                    ],
                ],
            ]];
        } else {
            $messages = [[
                'role' => 'user',
                'content' => $promptFinal,
            ]];
        }

        $lastError = 'Tidak ada respon dari OpenRouter.';
        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::connectTimeout(30)
                        ->timeout($timeout)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $apiKey,
                            'HTTP-Referer' => (string) config('app.url', 'http://localhost'),
                            'X-Title' => (string) config('app.name', 'Laravel App'),
                        ])
                        ->post($apiUrl, [
                            'model' => $model,
                            'messages' => $messages,
                            'temperature' => 0.2,
                            'max_tokens' => 2000,
                        ]);

                    if (!$response->failed()) {
                        return $response['choices'][0]['message']['content'] ?? 'Tidak ada respon.';
                    }

                    $status = $response->status();
                    $errorBody = $response->json('error.message') ?: $response->body();
                    $lastError = "OpenRouter error {$status} ({$model}): {$errorBody}";

                    if (in_array($status, [408, 409, 425, 429, 500, 502, 503, 504], true)) {
                        if ($attempt < 2) {
                            usleep(400000 * $attempt);
                            continue;
                        }

                        break;
                    }

                    return $lastError;
                } catch (\Exception $e) {
                    $lastError = 'OpenRouter exception: ' . $e->getMessage();
                    if ($attempt < 2) {
                        usleep(400000 * $attempt);
                        continue;
                    }

                    break;
                }
            }
        }

        return $lastError;
    }

    private function geminiAI(string $promptText, string $imageData = '', bool $isImage = false, string $mimeType = ''): string
    {
        $apiKey = (string) config('gemini.api_key', env('GEMINI_API_KEY', ''));
        if (trim($apiKey) === '') {
            throw new \Exception('Gemini API key not configured');
        }


        $timeout = (int) config('gemini.request_timeout', 300);
        if ($timeout <= 0) {
            $timeout = 300;
        }

        $parts = [];
        if ($isImage) {
            $parts = [
                ["text" => $promptText],
                ["inline_data" => ["mime_type" => $mimeType, "data" => $imageData]]
            ];
        } else {
            $parts = [["text" => $promptText]];
        }

        $models = ['gemini-2.0-flash'];
        $lastError = 'Tidak ada respon.';

        foreach ($models as $model) {
            $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {

                    $response = Http::timeout($timeout)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($apiUrl, [
                            "contents" => [["parts" => $parts]],
                            "generationConfig" => [
                                "temperature" => 0.3,
                                "maxOutputTokens" => 2000
                            ]
                        ]);

                    if (!$response->failed()) {
                        return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respon.';
                    }

                    $status = $response->status();
                    $lastError = 'Error API (' . $status . '): ' . $response->body();

                    if (in_array($status, [429, 503], true)) {
                        if ($attempt < 2) {
                            usleep(400000 * $attempt);
                            continue;
                        }

                        break;
                    }

                    return $lastError;
                } catch (\Exception $e) {
                    $lastError = 'Exception: ' . $e->getMessage();
                    if ($attempt < 2) {
                        usleep(400000 * $attempt);
                        continue;
                    }

                    break;
                }
            }
        }

        return $lastError;
    }

    private function analisisAILMStudio($promptFinal, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('LM_STUDIO_API_KEY', 'lm-studio');
        $baseUrl = rtrim(env('LM_STUDIO_BASE_URL', 'http://127.0.0.1:1234'), '/');
        $apiUrl = $baseUrl . '/v1/chat/completions';
        $timeoutSeconds = (int) env('LM_STUDIO_TIMEOUT', 300);
        if ($timeoutSeconds <= 0) {
            $timeoutSeconds = 300;
        }

        $messages = [];
        if ($isImage) {
            $mime = $mimeType ?: 'image/jpeg';
            $messages = [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $promptFinal,
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => "data:{$mime};base64,{$imageData}",
                        ],
                    ],
                ],
            ]];
        } else {
            $messages = [
                ['role' => 'user', 'content' => $promptFinal],
            ];
        }

        try {
            $response = Http::connectTimeout(300)
                ->timeout($timeoutSeconds)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post($apiUrl, [
                    'model' => env('LM_STUDIO_MODEL', 'oreal-deepseek-r1-distill-qwen-7b'),
                    'messages' => $messages,
                    'temperature' => 0.1,
                    'max_tokens' => 2000,
                ]);

            if ($response->failed()) {
                $error = 'Error LM Studio: ' . $response->status() . ' - ' . $response->body();
                Log::error('LM Studio API Error', ['error' => $error]);
                return $error;
            }

            return $response['choices'][0]['message']['content'] ?? 'Tidak ada respon.';
        } catch (\Exception $e) {
            $error = 'Exception LM Studio: ' . $e->getMessage();
            Log::error('LM Studio Exception', ['error' => $error, 'trace' => $e->getTraceAsString()]);
            return $error;
        }
    }
}
