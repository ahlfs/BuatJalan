<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIService
{
    /**
     * Call the configured AI provider with a prompt.
     * Supports multiple API keys separated by commas.
     * Automatically falls back to the next key if one fails/exhausts.
     *
     * @param string $prompt
     * @return array
     */
    public static function call(string $prompt): array
    {
        $provider = config('services.ai.provider', 'iyh');
        $providerConfig = config("services.ai.providers.{$provider}");

        if (empty($providerConfig) || empty($providerConfig['key'])) {
            Log::error("API key is not configured for provider: {$provider} in .env file.");
            return [
                'error' => "API Key untuk provider '" . strtoupper($provider) . "' belum diisi di berkas .env Anda."
            ];
        }

        $rawKeys = $providerConfig['key'];
        $model = $providerConfig['model'];

        // Split comma-separated keys and trim spaces
        $apiKeys = array_filter(array_map('trim', explode(',', $rawKeys)));

        if (empty($apiKeys)) {
            Log::error("No valid API keys found after parsing for provider: {$provider}");
            return [
                'error' => "API Key untuk provider '" . strtoupper($provider) . "' kosong atau tidak valid."
            ];
        }

        $lastError = 'Unknown error';
        $totalKeys = count($apiKeys);

        foreach ($apiKeys as $index => $apiKey) {
            $keyIndexNum = $index + 1;
            Log::info("Attempting AI generation with {$provider} using API Key {$keyIndexNum} of {$totalKeys}...");

            try {
                $rawJson = null;

                if ($provider === 'gemini') {
                    $response = Http::timeout(120)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey,
                        [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $prompt]
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'responseMimeType' => 'application/json'
                            ]
                        ]
                    );

                    if ($response->successful()) {
                        $body = $response->json();
                        $rawJson = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    } else {
                        $lastError = $response->body() ?: $response->reason();
                        Log::warning("Gemini API key index {$keyIndexNum} failed. Response: {$lastError}");
                        continue;
                    }
                } elseif (in_array($provider, ['openai', 'xai', 'deepseek', 'openrouter', 'iyh'])) {
                    $endpoints = [
                        'openai' => 'https://api.openai.com/v1/chat/completions',
                        'xai' => 'https://api.x.ai/v1/chat/completions',
                        'deepseek' => 'https://api.deepseek.com/chat/completions',
                        'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
                        'iyh' => 'https://api.iyh.app/v1/chat/completions',
                    ];

                    $request = Http::timeout(120)->withToken($apiKey);

                    if ($provider === 'openrouter') {
                        $request->withHeaders([
                            'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
                            'X-Title' => config('app.name', 'BuatJalan'),
                        ]);
                    }

                    $response = $request->post($endpoints[$provider], [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'response_format' => ['type' => 'json_object']
                    ]);

                    if ($response->successful()) {
                        $body = $response->json();
                        $rawJson = $body['choices'][0]['message']['content'] ?? '';
                    } else {
                        $lastError = $response->body() ?: $response->reason();
                        Log::warning(strtoupper($provider) . " API key index {$keyIndexNum} failed. Response: {$lastError}");
                        continue;
                    }
                } elseif ($provider === 'anthropic') {
                    $response = Http::timeout(120)
                        ->withHeaders([
                            'x-api-key' => $apiKey,
                            'anthropic-version' => '2023-06-01',
                            'content-type' => 'application/json',
                        ])
                        ->post('https://api.anthropic.com/v1/messages', [
                            'model' => $model,
                            'max_tokens' => 4000,
                            'messages' => [
                                ['role' => 'user', 'content' => $prompt]
                            ]
                        ]);

                    if ($response->successful()) {
                        $body = $response->json();
                        $rawJson = $body['content'][0]['text'] ?? '';
                    } else {
                        $lastError = $response->body() ?: $response->reason();
                        Log::warning("Anthropic API key index {$keyIndexNum} failed. Response: {$lastError}");
                        continue;
                    }
                } else {
                    return ['error' => "Provider AI '" . strtoupper($provider) . "' tidak didukung."];
                }

                if ($rawJson !== null) {
                    // Clean up code block ticks if AI accidentally wraps it
                    $rawJson = trim($rawJson);
                    if (Str::startsWith($rawJson, '```json')) {
                        $rawJson = Str::after($rawJson, '```json');
                    }
                    if (Str::endsWith($rawJson, '```')) {
                        $rawJson = Str::beforeLast($rawJson, '```');
                    }
                    $rawJson = trim($rawJson);

                    $data = json_decode($rawJson, true);

                    if (json_last_error() !== JSON_ERROR_NONE || empty($data['title'])) {
                        Log::error("Failed to parse JSON response from {$provider} (key index {$keyIndexNum}). Raw response: " . $rawJson);
                        $lastError = 'Gagal melakukan parsing JSON dari model AI.';
                        continue;
                    }

                    Log::info("AI generation with {$provider} succeeded using API Key {$keyIndexNum} of {$totalKeys}.");
                    return ['success' => true, 'data' => $data];
                }

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::error("Exception occurred using {$provider} API key index {$keyIndexNum}: {$lastError}");
                continue;
            }
        }

        // If we get here, all API keys failed
        Log::error("All {$totalKeys} API keys for provider {$provider} failed.");
        return [
            'error' => "Semua API Key (" . $totalKeys . ") untuk " . strtoupper($provider) . " gagal digunakan atau habis kuotanya. Error terakhir: {$lastError}"
        ];
    }
}
