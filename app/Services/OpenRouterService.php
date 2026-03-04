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

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
            'model' => 'openai/gpt-4o-mini', // free / cheap model
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            Log::error('OpenRouter API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception($response->body());
        }

        $body = $response->json();

        $text = $body['choices'][0]['message']['content'] ?? null;

        if (!$text) {
            throw new Exception('Empty AI response');
        }

        return $this->parseJsonResponse($text);
    }

    protected function buildPrompt(string $topic, string $mood, int $duration): string
    {
        return "
Respond ONLY in valid JSON. No explanation.

{
  \"script\": [],
  \"scenes\": [],
  \"captions\": [],
  \"music\": \"\"
}

Topic: {$topic}
Mood: {$mood}
Duration: {$duration} seconds
Generate 5 lines each for script, scenes, captions.
";
    }

    protected function parseJsonResponse(string $text): array
    {
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON parse error', ['text' => $text]);
            throw new Exception('JSON parse failed: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
