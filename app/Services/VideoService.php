<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VideoService
{
    protected $replicateApiToken;
    protected $replicateApiUrl = 'https://api.replicate.com/v1/models/minimax/video-01/predictions';

    public function __construct()
    {
        $this->replicateApiToken = trim(config('services.replicate.api_token')
            ?? env('REPLICATE_API_TOKEN'));

        if (!$this->replicateApiToken) {
            Log::error('VideoService: REPLICATE_API_TOKEN not configured');
        }
    }

    /**
     * Generate vertical reel video using Replicate AI
     * Returns immediately with prediction_id for async polling
     *
     * @param array $script - The AI generated script
     * @param array $scenes - The AI generated scenes
     * @param int $duration - Video duration in seconds (15 or 30)
     * @return array - ['success' => bool, 'prediction_id' => string, 'error' => string]
     */
    public function generateReelVideo(array $script, array $scenes, int $duration = 15): array
    {
        try {
            // Validate inputs
            if (empty($script) || empty($scenes)) {
                Log::warning('VideoService.generateReelVideo: no text content provided');
                return [
                    'success' => false,
                    'error' => 'No script or scenes provided for video generation.'
                ];
            }

            if (!in_array($duration, [15, 30, 60, 90])) {
                Log::warning('VideoService.generateReelVideo: invalid duration', ['duration' => $duration]);
                return [
                    'success' => false,
                    'error' => 'Duration must be 15 or 30 seconds'
                ];
            }

            if (!$this->replicateApiToken) {
                Log::error('VideoService.generateReelVideo: Replicate API token not configured');
                return [
                    'success' => false,
                    'error' => 'Video service configuration error'
                ];
            }

            // Build the textual prompt for Replicate API
            $prompt = $this->buildReelPrompt($script, $scenes, $duration);

            Log::info('VideoService.generateReelVideo: submitting to Replicate', [
                'duration' => $duration
            ]);

            // Call Replicate API to generate video using the model endpoint
            $response = Http::withoutVerifying()
                ->withHeaders([
                'Authorization' => "Token {$this->replicateApiToken}",
            ])
                ->post($this->replicateApiUrl, [
                'input' => [
                    'prompt' => $prompt,
                    'prompt_optimizer' => true,
                ]
            ]);

            if (!$response->successful()) {
                $status = $response->status();
                $errorMsg = 'Failed to start video generation: ' . $status;
                
                if ($status === 401) {
                    $errorMsg = 'Replicate API Authentication Failed: Your API key is invalid or expired. Please check your .env file.';
                }

                if ($status === 402) {
                    $errorMsg = 'Replicate API Billing Error: Your account has run out of funds or free credits. Please check your billing at replicate.com/account/billing';
                }

                Log::error('VideoService.generateReelVideo: Replicate API failed', [
                    'status' => $status,
                    'response' => $response->json(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $errorMsg
                ];
            }

            $data = $response->json();
            $predictionId = $data['id'] ?? null;

            if (!$predictionId) {
                Log::error('VideoService.generateReelVideo: no prediction_id in response', ['response' => $data]);
                return [
                    'success' => false,
                    'error' => 'Invalid response from video service'
                ];
            }

            Log::info('VideoService.generateReelVideo: prediction created', ['prediction_id' => $predictionId]);

            return [
                'success' => true,
                'prediction_id' => $predictionId,
                'status' => $data['status'] ?? 'starting'
            ];

        }
        catch (\Exception $e) {
            Log::error('VideoService.generateReelVideo: exception', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Video generation error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check the status of a video generation prediction
     *
     * @param string $predictionId - The Replicate prediction ID
     * @return array - ['status' => string, 'output' => mixed, 'error' => string|null]
     */
    public function checkVideoStatus(string $predictionId): array
    {
        try {
            if (!$predictionId) {
                Log::warning('VideoService.checkVideoStatus: empty prediction_id');
                return [
                    'status' => 'error',
                    'error' => 'Invalid prediction ID'
                ];
            }

            if (!$this->replicateApiToken) {
                Log::error('VideoService.checkVideoStatus: Replicate API token not configured');
                return [
                    'status' => 'error',
                    'error' => 'Service configuration error'
                ];
            }

            $response = Http::withoutVerifying()
                ->withHeaders([
                'Authorization' => "Token {$this->replicateApiToken}",
            ])
                ->get("{$this->replicateApiUrl}/{$predictionId}");

            if (!$response->successful()) {
                Log::error('VideoService.checkVideoStatus: Replicate API failed', [
                    'status' => $response->status(),
                    'prediction_id' => $predictionId,
                    'response' => $response->json()
                ]);
                return [
                    'status' => 'error',
                    'error' => 'Failed to check status'
                ];
            }

            $data = $response->json();
            $status = $data['status'] ?? 'unknown';
            $output = $data['output'] ?? null;
            $error = $data['error'] ?? null;

            // Log status updates
            Log::info('VideoService.checkVideoStatus: status check', [
                'prediction_id' => $predictionId,
                'status' => $status
            ]);

            // Handle completed status
            if ($status === 'succeeded' && $output) {
                // Replicate typically returns output as array or single URL string
                $videoUrl = is_array($output) ? ($output[0] ?? $output) : $output;

                Log::info('VideoService.checkVideoStatus: prediction succeeded', [
                    'prediction_id' => $predictionId,
                    'video_url' => $videoUrl
                ]);

                return [
                    'status' => 'completed',
                    'output' => $videoUrl,
                    'error' => null
                ];
            }

            // Handle failed status
            if ($status === 'failed') {
                Log::error('VideoService.checkVideoStatus: prediction failed', [
                    'prediction_id' => $predictionId,
                    'error' => $error
                ]);
                return [
                    'status' => 'failed',
                    'output' => null,
                    'error' => $error ?? 'Video generation failed'
                ];
            }

            // Processing or starting
            return [
                'status' => $status,
                'output' => null,
                'error' => null
            ];

        }
        catch (\Exception $e) {
            Log::error('VideoService.checkVideoStatus: exception', [
                'prediction_id' => $predictionId,
                'exception' => $e->getMessage()
            ]);
            return [
                'status' => 'error',
                'error' => 'Status check error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Build optimized text prompt for Replicate video generation
     *
     * @param array $script - The AI generated script
     * @param array $scenes - The AI generated scenes
     * @param int $duration - Duration in seconds
     * @return string - Formatted text prompt
     */
    private function buildReelPrompt(array $script, array $scenes, int $duration): string
    {
        $basePrompt = "Synthesize a cinematic {$duration}-second vertical video (9:16 aspect ratio). Style: Highly realistic narrative sequence. Ensure smooth camera transitions between these scenes:\n\n";

        // Inject the scene descriptions directly as the prompt
        $sceneText = implode("\n", $scenes);

        $basePrompt .= $sceneText;

        $basePrompt .= "\n\nAccompanying Voiceover/Script timeline:\n";
        $basePrompt .= implode("\n", $script);

        return $basePrompt;
    }
}
