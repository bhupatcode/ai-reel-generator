<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VideoService
{
    protected $replicateApiKey;
    protected $replicateApiUrl = 'https://api.replicate.com/v1/predictions';
    protected $replicateModel = 'minimax/video-01';
    protected $replicateVersion = '6c95a8f10eade6f6f4ecb6d0c21ca63b8d69b55b38b1d02488a63cebc4b5efaf';

    public function __construct()
    {
        $this->replicateApiKey = config('services.replicate.api_key')
            ?? env('REPLICATE_API_KEY');

        if (!$this->replicateApiKey) {
            Log::error('VideoService: REPLICATE_API_KEY not configured');
        }
    }

    /**
     * Generate vertical reel video using Replicate AI
     * Returns immediately with prediction_id for async polling
     *
     * @param array $imageUrls - Array of image URLs (must be publicly accessible)
     * @param int $duration - Video duration in seconds (15 or 30)
     * @return array - ['success' => bool, 'prediction_id' => string, 'error' => string]
     */
    public function generateReelVideo(array $imageUrls, int $duration = 15): array
    {
        try {
            // Validate inputs
            if (empty($imageUrls)) {
                Log::warning('VideoService.generateReelVideo: no images provided');
                return [
                    'success' => false,
                    'error' => 'No images provided'
                ];
            }

            if ($duration !== 15 && $duration !== 30) {
                Log::warning('VideoService.generateReelVideo: invalid duration', ['duration' => $duration]);
                return [
                    'success' => false,
                    'error' => 'Duration must be 15 or 30 seconds'
                ];
            }

            if (!$this->replicateApiKey) {
                Log::error('VideoService.generateReelVideo: Replicate API key not configured');
                return [
                    'success' => false,
                    'error' => 'Video service configuration error'
                ];
            }

            // Commenting out the reel creation code for now
            /*
            // Build the prompt for Replicate API
            $prompt = $this->buildReelPrompt($imageUrls, $duration);

            Log::info('VideoService.generateReelVideo: submitting to Replicate', [
                'image_count' => count($imageUrls),
                'duration' => $duration
            ]);

            // Call Replicate API to generate video
            $response = Http::withToken($this->replicateApiKey)
                ->post($this->replicateApiUrl, [
                    'version' => $this->replicateVersion,
                    'input' => [
                        'prompt' => $prompt,
                        'aspect_ratio' => '9:16',
                        'duration' => $duration,
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('VideoService.generateReelVideo: Replicate API failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'body' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to start video generation: ' . $response->status()
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
            */

            return [
                'success' => true,
                'message' => 'Reel creation code is commented out for now.'
            ];

        } catch (\Exception $e) {
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

            if (!$this->replicateApiKey) {
                Log::error('VideoService.checkVideoStatus: Replicate API key not configured');
                return [
                    'status' => 'error',
                    'error' => 'Service configuration error'
                ];
            }

            $response = Http::withToken($this->replicateApiKey)
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

        } catch (\Exception $e) {
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
     * Build optimized prompt for Replicate video generation
     *
     * @param array $imageUrls - Array of image URLs
     * @param int $duration - Duration in seconds
     * @return string - Formatted prompt with image URLs
     */
    private function buildReelPrompt(array $imageUrls, int $duration): string
    {
        $basePrompt = "Create a 15-second vertical Instagram reel video (9:16) from the provided images with cinematic transitions, smooth camera motion, motivational style, and export as MP4 video.";

        // Include image URLs in the prompt for the model to use
        $imageString = implode(' ', array_slice($imageUrls, 0, 4)); // Limit to first 4 images

        return "{$basePrompt}\n\nImages to use:\n{$imageString}";
    }
}
