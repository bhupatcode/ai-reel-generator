# Replicate API Migration - Complete Code Reference

## Quick Copy-Paste Code Sections

### 1. VideoService.php (Complete New Service)

```php
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
            ?? env('REPLICATE_API_TOKEN');
        
        if (!$this->replicateApiKey) {
            Log::error('VideoService: REPLICATE_API_TOKEN not configured');
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
```

---

### 2. VideoReelController.php (Complete New Controller)

```php
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
     */
    public function createVideo(Request $request)
    {
        try {
            $validated = $request->validate([
                'script' => 'required|array',
                'scenes' => 'required|array',
                'captions' => 'required|array',
                'music' => 'nullable|string',
                'duration' => 'required|integer|in:15,30',
            ]);

            Log::info('VideoReelController.createVideo: starting video generation', [
                'scenes_count' => count($validated['scenes']),
                'duration' => $validated['duration']
            ]);

            // Ensure image directory exists
            $imageDir = public_path('reels/images');
            if (!is_dir($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            $imageUrls = [];
            $imagePaths = [];
            $generator = app(\App\Services\ImageGenerationService::class);

            // Generate images from scenes
            foreach ($validated['scenes'] as $idx => $scene) {
                try {
                    Log::debug('VideoReelController.createVideo: generating image', ['scene_index' => $idx]);
                    
                    // Call image generation service
                    $prompt = $scene; // Could expand or refine the prompt
                    $imageData = $generator->generateFromPrompt($prompt);

                    $filename = 'scene_' . $idx . '_' . uniqid() . '.png';
                    $filePath = $imageDir . '/' . $filename;

                    // Handle base64 or binary image data
                    if (strpos($imageData, 'data:image') === 0) {
                        [$meta, $data] = explode(',', $imageData, 2);
                        file_put_contents($filePath, base64_decode($data));
                    } else {
                        file_put_contents($filePath, $imageData);
                    }

                    $imagePaths[] = $filePath;
                    // Generate public URL for the image
                    $imageUrl = url('reels/images/' . $filename);
                    $imageUrls[] = $imageUrl;

                    Log::debug('VideoReelController.createVideo: image generated', [
                        'scene_index' => $idx,
                        'url' => $imageUrl
                    ]);

                } catch (\Exception $e) {
                    Log::error('VideoReelController.createVideo: image generation failed', [
                        'scene_index' => $idx,
                        'scene' => $scene,
                        'error' => $e->getMessage()
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to generate scene image: ' . $e->getMessage()
                    ], 500);
                }
            }

            if (empty($imageUrls)) {
                Log::error('VideoReelController.createVideo: no images generated');
                return response()->json([
                    'success' => false,
                    'error' => 'No images were generated'
                ], 500);
            }

            Log::info('VideoReelController.createVideo: calling generateReelVideo', [
                'image_count' => count($imageUrls),
                'duration' => $validated['duration']
            ]);

            // Call Replicate API to generate video (async)
            $videoResult = $this->videoService->generateReelVideo(
                $imageUrls,
                $validated['duration']
            );

            if (!$videoResult['success']) {
                Log::error('VideoReelController.createVideo: generateReelVideo failed', $videoResult);
                return response()->json($videoResult, 500);
            }

            $predictionId = $videoResult['prediction_id'];

            // Create Reel record with pending status
            $reel = Reel::create([
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

                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'error' => $statusResult['error'] ?? 'Video generation failed'
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
}
```

---

### 3. API Routes (routes/api.php - New Endpoints Section)

```php
// Video creation (Replicate async)
Route::post('/reels/create-video', [\App\Http\Controllers\VideoReelController::class, 'createVideo'])->name('api.reels.create-video');

// Check video generation status
Route::get('/reels/video-status/{predictionId}', [\App\Http\Controllers\VideoReelController::class, 'checkVideoStatus'])->name('api.reels.video-status');

// Reel management
Route::get('/reels/{id}', [ReelController::class, 'show'])->name('api.reels.show');
Route::get('/reels', [\App\Http\Controllers\VideoReelController::class, 'list'])->name('api.reels.list');
Route::delete('/reels/{id}', [\App\Http\Controllers\VideoReelController::class, 'delete'])->name('api.reels.delete');
```

---

### 4. Reel Model (app/Models/Reel.php - Updated Fillable)

```php
protected $fillable = [
    'topic',
    'mood',
    'duration',
    'script',
    'scenes',
    'captions',
    'music',
    'video_path',
    'raw_response',
    'status',
    'prediction_id',  // NEW
];
```

---

### 5. Database Migration (2026_03_04_090000_add_replicate_fields_to_reels_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            if (!Schema::hasColumn('reels', 'video_path')) {
                $table->string('video_path', 500)->nullable()->after('music');
            }
            
            if (!Schema::hasColumn('reels', 'prediction_id')) {
                $table->string('prediction_id', 255)->nullable()->unique()->after('video_path');
            }

            try {
                $table->string('status', 50)->change();
            } catch (\Exception $e) {
                // Column might already be correct type
            }
        });
    }

    public function down(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            if (Schema::hasColumn('reels', 'video_path')) {
                $table->dropColumn('video_path');
            }
            if (Schema::hasColumn('reels', 'prediction_id')) {
                $table->dropColumn('prediction_id');
            }
        });
    }
};
```

---

### 6. Frontend JavaScript (Async Polling Logic for reel_form.blade.php)

```javascript
// ── Generate Video From JSON (with Replicate async polling) ──
$('#generateVideoBtn').on('click', function () {
    var raw = $('#reelJson').val().trim();
    if (!raw) {
        alert('Please paste the reel JSON first.');
        return;
    }

    var payload;
    try {
        payload = JSON.parse(raw);
    } catch (e) {
        alert('Invalid JSON: ' + e.message);
        return;
    }

    payload.duration = payload.duration || 15;

    $('#jsonLoader').show();
    $('#generatedVideo').empty();

    // Step 1: Submit video generation request
    $.ajax({
        url: '/api/reels/create-video',
        type: 'POST',
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify(payload),
        success: function(res) {
            if (res.success && res.prediction_id) {
                console.log('Video generation started. Prediction ID:', res.prediction_id);
                // Step 2: Start polling for status
                pollVideoStatus(res.prediction_id);
            } else {
                $('#jsonLoader').hide();
                $('#generatedVideo').html('<div class="alert alert-danger">Failed to start video generation: ' + (res.error || 'Unknown error') + '</div>');
            }
        },
        error: function(xhr) {
            $('#jsonLoader').hide();
            var errorMsg = 'Error starting video generation';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#generatedVideo').html('<div class="alert alert-danger">' + errorMsg + '</div>');
        }
    });
});

// ── Poll video generation status every 5 seconds ──
let pollInterval = null;
let pollAttempts = 0;
const maxPollAttempts = 240; // 20 minutes (240 * 5 seconds)

function pollVideoStatus(predictionId) {
    pollAttempts = 0;
    
    function checkStatus() {
        pollAttempts++;

        if (pollAttempts > maxPollAttempts) {
            clearInterval(pollInterval);
            $('#jsonLoader').hide();
            $('#generatedVideo').html('<div class="alert alert-warning">Video generation timed out (20+ minutes). Please check back later.</div>');
            return;
        }

        // Update loading text with poll count
        var displayMinutes = Math.floor((pollAttempts * 5) / 60);
        var displaySeconds = (pollAttempts * 5) % 60;
        var timeStr = (displayMinutes > 0 ? displayMinutes + 'm ' : '') + displaySeconds + 's';
        
        $('#jsonLoader').html(
            '<div style="text-align: center;"><div class="spinner-border text-info mb-3" role="status"></div>' +
            '<div class="loader-text">Generating video<span class="dot-animation"></span></div>' +
            '<small style="color: var(--text-secondary);">Elapsed: ' + timeStr + '</small></div>'
        );

        $.ajax({
            url: '/api/reels/video-status/' + predictionId,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                console.log('Status check:', res);

                if (res.status === 'completed' && res.video_url) {
                    clearInterval(pollInterval);
                    $('#jsonLoader').hide();
                    
                    // Display the generated video
                    var html = '<video width="100%" controls autoplay muted style="border-radius: 12px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);">' +
                               '<source src="' + res.video_url + '" type="video/mp4">' +
                               'Your browser does not support the video tag.' +
                               '</video>';
                    $('#generatedVideo').html(html);
                    
                    // Show success alert
                    showAlert('success', 'Video generated successfully! It took ' + timeStr + '.');
                } 
                else if (res.status === 'failed' || res.error) {
                    clearInterval(pollInterval);
                    $('#jsonLoader').hide();
                    $('#generatedVideo').html('<div class="alert alert-danger">Video generation failed: ' + (res.error || 'Unknown error') + '</div>');
                }
                // If still processing, next poll will happen automatically
            },
            error: function(xhr) {
                if (xhr.status === 500) {
                    clearInterval(pollInterval);
                    $('#jsonLoader').hide();
                    var errorMsg = 'Error checking status';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    $('#generatedVideo').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
                // Otherwise continue polling
            }
        });
    }

    // First check immediately
    checkStatus();
    
    // Then poll every 5 seconds
    pollInterval = setInterval(checkStatus, 5000);
}
```

---

## Quick Migration Steps

```bash
# 1. Update VideoService.php (already done - see above)
# 2. Update VideoReelController.php (already done - see above)
# 3. Update routes/api.php (already done - see above)
# 4. Update Reel model (already done - see above)
# 5. Create migration
php artisan make:migration add_replicate_fields_to_reels_table

# 6. Run migration
php artisan migrate

# 7. Clear cache
php artisan config:clear

# 8. Test the API
php artisan tinker
# $service = app(\App\Services\VideoService::class);
# $result = $service->generateReelVideo(['https://...'], 15);
```

---

## Environment Check

```bash
# Verify API key is set
grep REPLICATE_API_TOKEN .env

# Should output:
# REPLICATE_API_TOKEN=r8_N13EPP6q0vlKIgjStHF1Lcg4CdH4yYD0UrxER
```

---

## Testing cURL Commands

```bash
# Test 1: Create video
curl -X POST http://localhost/api/reels/create-video \
  -H "Content-Type: application/json" \
  -d '{
    "script": ["Test script"],
    "scenes": ["A beautiful landscape"],
    "captions": ["Test caption"],
    "duration": 15
  }'

# Response:
# {
#   "success": true,
#   "prediction_id": "xxx...",
#   "reel_id": 5,
#   "status": "processing"
# }

# Test 2: Check status
curl -X GET http://localhost/api/reels/video-status/[prediction_id]

# Response (processing):
# {
#   "success": true,
#   "status": "processing"
# }

# Response (completed):
# {
#   "success": true,
#   "status": "completed",
#   "video_url": "https://replicate.delivery/..."
# }
```

---

## That's it! Complete migration from FFmpeg to Replicate API ✓
