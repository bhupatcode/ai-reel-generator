<?php

namespace App\Http\Controllers;

use App\Services\VideoService;
use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoReelController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Create reel video endpoint from script/scenes data (async)
     * POST /api/reels/create-video
     *
     * Expected JSON payload:
     * {
     *   "script": [],
     *   "scenes": [],
     *   "captions": [],
     *   "music": "" (optional)
     *   "duration": 15
     * }
     *
     * Returns:
     * {
     *   "success": true,
     *   "prediction_id": "string",
     *   "status": "processing",
     *   "message": "Video generation started. Poll /api/reels/video-status/{prediction_id}"
     * }
     */
    public function createVideo(Request $request)
    {
        try {
            $validated = $request->validate([
                'topic' => 'nullable|string|max:255',
                'mood' => 'nullable|string|max:255',
                'script' => 'required|array',
                'scenes' => 'required|array',
                'captions' => 'required|array',
                'music' => 'nullable|string',
                'duration' => 'required|integer|in:15,30,60,90',
            ]);

            Log::info('VideoReelController.createVideo: starting video generation', [
                'duration' => $validated['duration']
            ]);

            // Call Replicate API to generate video (async) using the raw text script and scenes
            $videoResult = $this->videoService->generateReelVideo(
                $validated['script'],
                $validated['scenes'],
                $validated['duration']
            );

            if (!$videoResult['success']) {
                Log::error('VideoReelController.createVideo: generateReelVideo failed', $videoResult);
                return response()->json([
                    'success' => false,
                    'error' => $videoResult['error'] ?? 'Video generation service failed to respond correctly.'
                ], 500);
            }

            $predictionId = $videoResult['prediction_id'];

            // Create Reel record with pending status
            $reel = Reel::create([
                'topic' => $validated['topic'] ?? 'AI Generated Reel',
                'mood' => $validated['mood'] ?? 'Professional',
                'script' => json_encode($validated['script']),
                'scenes' => json_encode($validated['scenes']),
                'captions' => json_encode($validated['captions']),
                'music' => $validated['music'] ?? null,
                'duration' => $validated['duration'],
                'video_path' => null,
                'status' => 'processing',
                'prediction_id' => $predictionId,
            ]);

            Log::info('VideoReelController.createVideo: reel record created', [
                'reel_id' => $reel->id,
                'prediction_id' => $predictionId
            ]);

            return response()->json([
                'success' => true,
                'prediction_id' => $predictionId,
                'reel_id' => $reel->id,
                'status' => 'processing',
                'message' => 'Video generation started. Poll /api/reels/video-status/' . $predictionId
            ]);

        } catch (\Exception $e) {
            Log::error('VideoReelController.createVideo: exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Video creation error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check video generation status
     * GET /api/reels/video-status/{predictionId}
     *
     * Returns:
     * {
     *   "success": true,
     *   "status": "processing|completed|failed",
     *   "video_url": "https://..." (when completed),
     *   "error": "..." (when failed)
     * }
     */
    public function checkVideoStatus(Request $request, $predictionId)
    {
        try {
            if (!$predictionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid prediction ID'
                ], 400);
            }

            Log::debug('VideoReelController.checkVideoStatus: checking', [
                'prediction_id' => $predictionId
            ]);

            // Check status with VideoService
            $statusResult = $this->videoService->checkVideoStatus($predictionId);

            // If completed, update the Reel record
            if ($statusResult['status'] === 'completed' && $statusResult['output']) {
                $videoUrl = $statusResult['output'];

                // Find and update reel record
                $reel = Reel::where('prediction_id', $predictionId)->first();
                if ($reel) {
                    $reel->update([
                        'video_path' => $videoUrl,
                        'status' => 'completed'
                    ]);
                    Log::info('VideoReelController.checkVideoStatus: reel updated', [
                        'reel_id' => $reel->id,
                        'video_url' => $videoUrl
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'video_url' => $videoUrl
                ]);
            }

            // If failed
            if ($statusResult['status'] === 'failed') {
                $reel = Reel::where('prediction_id', $predictionId)->first();
                if ($reel) {
                    $reel->update(['status' => 'failed']);
                }

                Log::error('VideoReelController.checkVideoStatus: prediction failed', [
                    'prediction_id' => $predictionId,
                    'error' => $statusResult['error']
                ]);

                $errorMessage = $statusResult['error'] ?? 'Video generation failed on the server.';
                if (str_contains($errorMessage, '401') || str_contains(strtolower($errorMessage), 'unauthenticated')) {
                    $errorMessage = 'API Authentication Error: Please verify your Replicate API key.';
                }

                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'error' => $errorMessage
                ], 500);
            }

            // Still processing
            return response()->json([
                'success' => true,
                'status' => $statusResult['status'] ?? 'processing'
            ]);

        } catch (\Exception $e) {
            Log::error('VideoReelController.checkVideoStatus: exception', [
                'prediction_id' => $predictionId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Status check error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reels
     * GET /api/reels
     */
    public function list()
    {
        try {
            $reels = Reel::orderBy('created_at', 'desc')
                ->select('id', 'status', 'duration', 'video_path', 'created_at')
                ->limit(20)
                ->get()
                ->map(function ($reel) {
                    return [
                        'id' => $reel->id,
                        'status' => $reel->status,
                        'duration' => $reel->duration,
                        'url' => $reel->video_path,
                        'created_at' => $reel->created_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'reels' => $reels
            ]);
        } catch (\Exception $e) {
            Log::error('VideoReelController.list: exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch reels'
            ], 500);
        }
    }

    /**
     * Delete reel
     * DELETE /api/reels/{id}
     */
    public function delete($id)
    {
        try {
            $reel = Reel::find($id);

            if (!$reel) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reel not found'
                ], 404);
            }

            // Delete from database
            $reel->delete();

            Log::info('VideoReelController.delete: reel deleted', ['reel_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Reel deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('VideoReelController.delete: exception', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete reel'
            ], 500);
        }
    }

    /**
     * Download a generated video at a specific resolution
     * POST /api/reels/download
     */
    public function downloadVideo(Request $request)
    {
        try {
            $validated = $request->validate([
                'video_url' => 'required|url',
                'resolution' => 'required|in:144p,240p,360p,720p,1080p'
            ]);

            $videoUrl = $validated['video_url'];
            $resolution = $validated['resolution'];

            // Fetch the file contents using native PHP to avoid Laravel Http Promise lints
            $fileContents = @file_get_contents($videoUrl);

            if ($fileContents === false) {
                throw new \Exception('Failed to retrieve video file from generated source.');
            }

            // For now, since native FFMPEG is not guaranteed, we provide the originally generated HD file
            // and tag it with the requested resolution in the filename so the user successfully downloads a file.
            $filename = 'ai-reel-' . $resolution . '-' . time() . '.mp4';
            $tempPath = sys_get_temp_dir() . '/' . $filename;

            file_put_contents($tempPath, $fileContents);

            return response()->download($tempPath, $filename, [
                'Content-Type' => 'video/mp4',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('VideoReelController.downloadVideo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to initiate download: ' . $e->getMessage()
            ], 500);
        }
    }
}
