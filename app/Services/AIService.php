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
                } elseif (in_array($provider, ['openai', 'xai', 'deepseek', 'openrouter', 'iyh', 'ninerouter'])) {
                    $endpoints = [
                        'openai' => 'https://api.openai.com/v1/chat/completions',
                        'xai' => 'https://api.x.ai/v1/chat/completions',
                        'deepseek' => 'https://api.deepseek.com/chat/completions',
                        'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
                        'iyh' => 'https://api.iyh.app/v1/chat/completions',
                        'ninerouter' => rtrim($providerConfig['url'] ?? 'https://api.9router.com/v1', '/') . '/chat/completions',
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
                        'response_format' => ['type' => 'json_object'],
                        'stream' => false,
                    ]);

                    if ($response->successful()) {
                        $rawBody = $response->body();
                        if (Str::startsWith(trim($rawBody), 'data:')) {
                            $lines = explode("\n", $rawBody);
                            $rawJson = '';
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (Str::startsWith($line, 'data:')) {
                                    $dataText = trim(Str::after($line, 'data:'));
                                    if ($dataText === '[DONE]') {
                                        continue;
                                    }
                                    $chunk = json_decode($dataText, true);
                                    if (isset($chunk['choices'][0]['delta']['content'])) {
                                        $rawJson .= $chunk['choices'][0]['delta']['content'];
                                    }
                                }
                            }
                        } else {
                            $body = $response->json();
                            $rawJson = $body['choices'][0]['message']['content'] ?? '';
                        }
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

                    // 1. Remove <think>...</think> tags (e.g. DeepSeek R1, Qwen3 thinking models)
                    if (preg_match('/<think>.*?<\/think>/is', $rawJson)) {
                        $rawJson = preg_replace('/<think>.*?<\/think>/is', '', $rawJson);
                        $rawJson = trim($rawJson);
                    }

                    // 2. Strip markdown code block wrappers
                    if (Str::startsWith($rawJson, '```json')) {
                        $rawJson = Str::after($rawJson, '```json');
                    } elseif (Str::startsWith($rawJson, '```')) {
                        $rawJson = Str::after($rawJson, '```');
                    }
                    if (Str::endsWith($rawJson, '```')) {
                        $rawJson = Str::beforeLast($rawJson, '```');
                    }
                    $rawJson = trim($rawJson);

                    // 3. Extract first JSON object block (handles conversational prefix/suffix)
                    if (!Str::startsWith($rawJson, '{')) {
                        if (preg_match('/\{.*\}/s', $rawJson, $matches)) {
                            $rawJson = $matches[0];
                        }
                    }

                    // 4. Attempt to repair truncated JSON (model cut off mid-response)
                    //    Count unmatched braces and close them
                    $data = json_decode($rawJson, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $repaired = static::repairTruncatedJson($rawJson);
                        if ($repaired !== null) {
                            $data = json_decode($repaired, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                Log::warning("{$provider} response was truncated and auto-repaired.");
                                $rawJson = $repaired;
                            }
                        }
                    }

                    if (json_last_error() !== JSON_ERROR_NONE || empty($data['title'])) {
                        $jsonErrorMsg = json_last_error_msg();
                        $tailSnippet  = mb_substr($rawJson, -300);
                        Log::error("Failed to parse JSON response from {$provider} (key index {$keyIndexNum}). JSON error: {$jsonErrorMsg}. Tail of raw response: ...{$tailSnippet}");
                        $lastError = 'Gagal melakukan parsing JSON dari model AI. (' . $jsonErrorMsg . ')';
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

    /**
     * Attempt to repair a truncated JSON string by closing any unmatched braces/brackets.
     * This handles cases where a model's response is cut off before completion (e.g. token limit).
     *
     * @param string $json
     * @return string|null Repaired JSON string, or null if it cannot be repaired.
     */
    protected static function repairTruncatedJson(string $json): ?string
    {
        $stack      = [];
        $inString   = false;
        $escape     = false;
        $lastGoodPos = 0;

        for ($i = 0, $len = strlen($json); $i < $len; $i++) {
            $char = $json[$i];

            if ($escape) {
                $escape = false;
                continue;
            }
            if ($char === '\\' && $inString) {
                $escape = true;
                continue;
            }
            if ($char === '"') {
                $inString = !$inString;
                if (!$inString) {
                    $lastGoodPos = $i;
                }
                continue;
            }
            if ($inString) {
                continue;
            }

            if ($char === '{' || $char === '[') {
                $stack[] = $char === '{' ? '}' : ']';
            } elseif ($char === '}' || $char === ']') {
                if (!empty($stack) && end($stack) === $char) {
                    array_pop($stack);
                    $lastGoodPos = $i;
                }
            } elseif (!in_array($char, [',', ':', ' ', "\n", "\r", "\t"])) {
                $lastGoodPos = $i;
            }
        }

        if (empty($stack)) {
            // JSON is already balanced
            return $json;
        }

        // Trim trailing comma or whitespace before closing
        $truncated = rtrim(substr($json, 0, $lastGoodPos + 1), ", \t\n\r");

        // Close all open containers in reverse order
        $repaired = $truncated . implode('', array_reverse($stack));

        // Validate repaired result
        json_decode($repaired);
        return json_last_error() === JSON_ERROR_NONE ? $repaired : null;
    }
}
