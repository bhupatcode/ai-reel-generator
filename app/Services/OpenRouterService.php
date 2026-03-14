<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenRouterService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->endpoint = config('services.openrouter.endpoint');

        if (empty($this->apiKey)) {
            throw new Exception('OpenRouter API key missing.');
        }
    }

    public function generateReel(string $topic, string $mood, int $duration): array
    {
        $prompt = $this->buildPrompt($topic, $mood, $duration);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://ai-reel-generator.app',
            ])
                ->timeout(60)
                ->retry(2, 100)
                ->post($this->endpoint, [
                'model' => 'openai/gpt-3.5-turbo', // Cheaper model, lower token usage
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500, // Limit max tokens to reduce cost
            ]);
        }
        catch (Exception $e) {
            Log::error('OpenRouter HTTP Error', [
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to connect to OpenRouter API: ' . $e->getMessage());
        }

        if ($response->failed()) {
            Log::error('OpenRouter API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // Try to parse error message from response
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();

            throw new Exception('OpenRouter API failed: ' . $errorMessage);
        }

        $body = $response->json();

        $text = $body['choices'][0]['message']['content'] ?? null;

        if (!$text) {
            Log::error('Empty OpenRouter response', ['body' => $body]);
            throw new Exception('Empty response from OpenRouter API');
        }

        return $this->parseJsonResponse($text);
    }

    protected function buildPrompt(string $topic, string $mood, int $duration): string
    {
        return "Generate JSON for a {$duration}s video about: {$topic}
Mood: {$mood}

{
  \"script\": [5 lines],
  \"scenes\": [5 descriptions],
  \"captions\": [5 captions],
  \"music\": \"genre\"
}

Return ONLY valid JSON. No markdown.";
    }

    protected function parseJsonResponse(string $text): array
    {
        $text = trim($text);

        // Remove markdown code blocks if present
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = preg_replace('/^```\s*/i', '', $text);
        $text = trim($text);

        // Try to extract JSON if it's wrapped in extra text
        if (strpos($text, '{') !== 0) {
            $start = strpos($text, '{');
            if ($start !== false) {
                $text = substr($text, $start);
            }
        }

        // Find the last closing brace
        $lastBrace = strrpos($text, '}');
        if ($lastBrace !== false) {
            $text = substr($text, 0, $lastBrace + 1);
        }

        $text = trim($text);

        Log::info('Parsing OpenRouter response', ['text' => substr($text, 0, 200)]);

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON parse error', [
                'error' => json_last_error_msg(),
                'text' => $text,
                'text_length' => strlen($text)
            ]);
            throw new Exception('JSON parse failed: ' . json_last_error_msg() . '. Response: ' . substr($text, 0, 500));
        }

        // Validate structure
        if (!isset($decoded['script']) || !isset($decoded['scenes']) || !isset($decoded['captions']) || !isset($decoded['music'])) {
            Log::error('Invalid response structure', ['decoded' => $decoded]);
            throw new Exception('Response missing required fields: script, scenes, captions, or music');
        }

        // Ensure arrays have correct format
        $decoded['script'] = is_array($decoded['script']) ? $decoded['script'] : [$decoded['script']];
        $decoded['scenes'] = is_array($decoded['scenes']) ? $decoded['scenes'] : [$decoded['scenes']];
        $decoded['captions'] = is_array($decoded['captions']) ? $decoded['captions'] : [$decoded['captions']];

        return $decoded;
    }
}
