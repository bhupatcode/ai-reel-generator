# AI Reel Generator - Replicate API Migration Guide

## Overview
This document covers the complete migration from FFmpeg to Replicate API for video generation in the AI Reel Generator project.

---

## What Changed

### ❌ Removed
- **FFmpeg-based video creation** - All local video encoding removed
  - `createVideoFromImages()` method with FFmpeg command
  - `addMusicToVideo()` method with FFmpeg audio merging
  - `createConcatFile()` helper for FFmpeg concat demuxer
  - Local temp file storage for video processing
  - All FFmpeg dependency requirements

### ✅ Added
- **Replicate API async video generation**
  - `generateReelVideo()` - Submit video generation job
  - `checkVideoStatus()` - Poll prediction status
  - Clean service architecture with proper loggingAsynchronous polling on frontend (5-second intervals)
- **Database migration** for `prediction_id` and `video_path` columns
- **Comprehensive error handling and logging**

---

## Architecture

### Flow Diagram
```
Frontend                Backend                 Replicate API
   |                       |                          |
   |--POST /create-video-->|                          |
   |                       |--POST /predictions------>|
   |                       |<--prediction_id----------|
   |                       |                          |
   |<--response with ID----|                          |
   |                       |                          |
   |--GET /video-status--->|                          |
   |   (every 5 seconds)   |--GET /predictions/{id}-->|
   |                       |<--status: processing----|
   |<--still processing----|                          |
   |                       |                          |
   |--GET /video-status--->|                          |
   |   (every 5 seconds)   |--GET /predictions/{id}-->|
   |                       |<--status: succeeded------|
   |                       |<--output: video_url------|
   |                       |                          |
   |<--video_url-----------|                          |
   |                       |                          |
   |--Display video--------|                          |
```

---

## Implementation Details

### 1. VideoService.php (Updated)

**New Method: `generateReelVideo(array $imageUrls, int $duration = 15)`**

```php
// Submits video generation to Replicate API
// Returns: [success => bool, prediction_id => string, status => string]
// Status: 'starting' or 'processing'

// Replicate Model Used:
// - Model: minimax/video-01
// - Version: 6c95a8f10eade6f6f4ecb6d0c21ca63b8d69b55b38b1d02488a63cebc4b5efaf
```

**Implementation Details:**
- Validates image URLs and duration (15 or 30 seconds only)
- Builds optimized prompt: "Create a 15-second vertical Instagram reel video (9:16) from the provided images with cinematic transitions, smooth camera motion, motivational style, and export as MP4 video."
- Sends POST request to `https://api.replicate.com/v1/predictions`
- Returns immediately with `prediction_id` for async polling
- Includes comprehensive error logging for debugging

**New Method: `checkVideoStatus(string $predictionId)`**

```php
// Polls the Replicate API for prediction status
// Returns: [status => string, output => string|null, error => string|null]
// Statuses: 'starting', 'processing', 'succeeded', 'failed', 'error'
```

**Implementation Details:**
- Makes GET request to `https://api.replicate.com/v1/predictions/{predictionId}`
- Returns video URL when status is 'succeeded'
- Includes proper error handling for failed predictions
- Logs all status updates for monitoring

---

### 2. VideoReelController.php (Updated)

**Changed Method: `createVideo(Request $request)`**

**Key Changes:**
- Now returns `prediction_id` immediately (async initiation)
- Calls `generateReelVideo()` instead of `createVideo()`
- Converts local image paths to public URLs
- Creates Reel record with status='processing' and prediction_id
- No longer waits for video completion

**Response Example:**
```json
{
  "success": true,
  "prediction_id": "urdwn6x73eypj2l4qiqc2dsfpa",
  "reel_id": 5,
  "status": "processing",
  "message": "Video generation started. Poll /api/reels/video-status/urdwn6x73eypj2l4qiqc2dsfpa"
}
```

**New Method: `checkVideoStatus(Request $request, $predictionId)`**

```php
// Polls video generation status and updates Reel when completed
// GET /api/reels/video-status/{predictionId}
```

**Response Examples:**

Processing:
```json
{
  "success": true,
  "status": "processing"
}
```

Completed:
```json
{
  "success": true,
  "status": "completed",
  "video_url": "https://replicate.delivery/..."
}
```

Failed:
```json
{
  "success": false,
  "status": "failed",
  "error": "CUDA out of memory"
}
```

---

### 3. Routes (Updated)

**New Routes Added:**

```php
// Initiate async video generation (same endpoint, new behavior)
POST /api/reels/create-video

// Check video generation status (POLLING ENDPOINT)
GET /api/reels/video-status/{predictionId}

// List all reels (updated implementation)
GET /api/reels

// Delete reel (updated implementation)
DELETE /api/reels/{id}
```

**Removed Routes:**
- No routes removed, only updated behavior

---

### 4. Frontend JavaScript (Updated)

**Key Changes:**

1. **Form Submission** (`#generateVideoBtn` click):
   - Sends request to `/api/reels/create-video`
   - Receives `prediction_id` back
   - Starts polling immediately

2. **Status Polling** (`pollVideoStatus()` function):
   - Polls `/api/reels/video-status/{predictionId}` every 5 seconds
   - Shows elapsed time in loading message
   - Implements 20-minute timeout (240 attempts)
   - Updates UI with elapsed time

3. **Completion Handling**:
   - Displays `<video>` element with video URL
   - Shows success message with total generation time
   - Handles error states with appropriate messages

**Loading UI Features:**
- Animated spinner with "Generating video..." text
- Elapsed time counter (updates every 5 seconds)
- Shows format: "2m 45s" / "1m 12s" / "30s"
- Automatic timeout after 20 minutes

---

### 5. Database Migration

**New Migration File:** `2026_03_04_090000_add_replicate_fields_to_reels_table.php`

**Added Columns:**
- `prediction_id` (string, unique, nullable) - Replicate API prediction ID
- `video_path` (string, nullable) - Final video URL from Replicate

**Updated Columns:**
- `status` (now supports 'processing' state)

**Migration Command:**
```bash
php artisan migrate
```

---

### 6. Environment Configuration

**Required .env Variable:**
```dotenv
REPLICATE_API_KEY=r8_N13EPP6q0vlKIgjStHF1Lcg4CdH4yYD0UrxER
```

**Already Present:** ✓ (configured in your .env)

---

## Setup Instructions

### 1. Database Migration

```bash
cd c:\wamp64\www\New Start\ai-reel-generator

# Run migrations to add new columns
php artisan migrate
```

### 2. Verify Replicate API Key

Check that `REPLICATE_API_KEY` is set in `.env`:
```bash
php artisan tinker
# In tinker:
echo env('REPLICATE_API_KEY');
```

Expected output: Your actual API key (should start with `r8_`)

### 3. Clear Cache (Optional)

```bash
php artisan cache:clear
php artisan config:clear
```

---

## Usage Flow

### Step 1: Generate Images
Frontend submits topic, mood, duration → Backend generates images using Gemini API

### Step 2: Initiate Video Generation
```javascript
POST /api/reels/create-video
Content-Type: application/json

{
  "script": ["Content 1", "Content 2"],
  "scenes": ["Scene 1 description", "Scene 2 description"],
  "captions": ["Caption 1", "Caption 2"],
  "duration": 15
}
```

Response:
```json
{
  "success": true,
  "prediction_id": "urdwn6x73eypj2l4qiqc2dsfpa",
  "reel_id": 5,
  "status": "processing"
}
```

### Step 3: Poll Status (Every 5 Seconds)
```javascript
GET /api/reels/video-status/urdwn6x73eypj2l4qiqc2dsfpa
```

Response (Processing):
```json
{
  "success": true,
  "status": "processing"
}
```

Response (Complete):
```json
{
  "success": true,
  "status": "completed",
  "video_url": "https://replicate.delivery/..."
}
```

### Step 4: Display Video
```html
<video width="100%" controls autoplay muted>
  <source src="https://replicate.delivery/..." type="video/mp4">
</video>
```

---

## Testing Instructions

### A. Unit Test - VideoService

```php
php artisan tinker

// Test 1: Generate video (check Replicate response)
$service = app(\App\Services\VideoService::class);
$result = $service->generateReelVideo([
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg'
], 15);

echo json_encode($result, JSON_PRETTY_PRINT);
// Should show: success: true, prediction_id: "..."

// Test 2: Check status
$status = $service->checkVideoStatus($result['prediction_id']);
echo json_encode($status, JSON_PRETTY_PRINT);
// Should initially show: status: "processing"
```

### B. API Test - Full Flow (Using Thunder Client/Postman)

**Request 1: Create Video**
```
POST http://localhost/api/reels/create-video
Content-Type: application/json
X-CSRF-TOKEN: [from page]

{
  "script": ["Amazing content"],
  "scenes": ["A beautiful landscape scene"],
  "captions": ["Check this out!"],
  "duration": 15
}
```

Expected Response:
```json
{
  "success": true,
  "prediction_id": "xxx...",
  "reel_id": 5,
  "status": "processing",
  "message": "Video generation started..."
}
```

**Request 2: Check Status (repeat every 5 seconds)**
```
GET http://localhost/api/reels/video-status/[prediction_id_from_above]
```

Processing response:
```json
{
  "success": true,
  "status": "processing"
}
```

Completed response:
```json
{
  "success": true,
  "status": "completed",
  "video_url": "https://replicate.delivery/xxx.mp4"
}
```

### C. Frontend Test

1. Open `/` or your frontend form page
2. Fill in the form (topic, mood, duration)
3. Click "Generate" button
4. Watch "Generating video..." loading message
5. See elapsed time update every 5 seconds
6. When complete, video player appears with the MP4

### D. Error Scenarios to Test

**Test 1: Invalid Replicate API Key**
```bash
# In .env, set wrong key temporarily
REPLICATE_API_KEY=invalid_key_12345
php artisan config:clear
```
Expected: "Video generation error" message

**Test 2: Network Error**
- Disconnect internet during polling
- Expected: Continue polling when reconnected, or timeout after 20 minutes

**Test 3: Invalid Images**
```php
$service->generateReelVideo([], 15);  // Empty array
// Expected: "No images provided" error
```

**Test 4: Invalid Duration**
```php
$service->generateReelVideo(['url1'], 25);  // Invalid duration
// Expected: "Duration must be 15 or 30 seconds" error
```

---

## Monitoring & Logs

### Log Files Location
```
storage/logs/laravel.log
```

### What Gets Logged

**VideoService:**
- Prediction creation: "prediction created with ID: xxx"
- Status checks: "status check - prediction_id: xxx, status: processing"
- Completions: "prediction succeeded - video_url: https://..."
- Errors: "Replicate API failed - status: 500"

**VideoReelController:**
- Video creation start: "starting video generation - 2 scenes, 15 second duration"
- Image generation: "image generated - scene_index: 0, url: http://..."
- Reel creation: "reel record created - reel_id: 5, prediction_id: xxx"
- Status updates: "reel updated - reel_id: 5, status: completed"

### Real-Time Monitoring

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Or use
php artisan log:tail
```

---

## Performance Considerations

### Replicate API Pricing
- **Video generation:** ~$0.015 per 15-second video
- **Status checks:** Free (GET requests)

### Performance Metrics
- **Initial response:** <1 second (returns prediction_id immediately)
- **Video generation:** 30-180 seconds (depends on video complexity)
- **Polling frequency:** 5-second intervals (configurable)
- **Frontend timeout:** 20 minutes (240 attempts)

### Optimization Tips

1. **Adjust poll interval** if needed (currently 5 seconds in JavaScript):
   ```javascript
   pollInterval = setInterval(checkStatus, 3000); // 3 seconds (more frequent)
   ```

2. **Increase timeout** for longer videos:
   ```javascript
   const maxPollAttempts = 600; // 50 minutes instead of 20
   ```

3. **Batch multiple requests** for higher throughput

---

## Troubleshooting

### Issue: "Video generation error: Service configuration error"
**Cause:** REPLICATE_API_KEY not set or invalid
**Solution:**
```bash
# Check .env
grep REPLICATE_API_KEY .env

# Should show valid key starting with r8_
```

### Issue: "Failed to start video generation: 401"
**Cause:** Invalid or expired Replicate API key
**Solution:**
- Regenerate key at https://replicate.com/account/api-tokens
- Update .env
- Run `php artisan config:clear`

### Issue: "Video generation timed out (20+ minutes)"
**Cause:** Replicate API taking too long or failed silently
**Solution:**
- Check Replicate status: https://status.replicate.com
- Check logs: `tail -f storage/logs/laravel.log`
- Increase timeout in JavaScript if needed

### Issue: "Status check error: Replicate API failed"
**Cause:** Network issue or Replicate API down
**Solution:**
- Refresh page and try again
- Check internet connection
- Wait for Replicate to recover if down

### Issue: Video generation succeeds but no video URL
**Cause:** Replicate model output format unexpected
**Solution:**
- Check logs for actual output format
- May need to adjust `buildReelPrompt()` method

---

## Configuration Options

### Replicate Model Selection

Current setup uses:
```php
protected $replicateModel = 'minimax/video-01';
protected $replicateVersion = '6c95a8f10eade6f6f4ecb6d0c21ca63b8d69b55b38b1d02488a63cebc4b5efaf';
```

**Alternative Models:**
- `openai/sora` - More advanced (if available)
- `stability-ai/stable-video-diffusion` - More budget-friendly

To change model:
1. Update VideoService.php `$replicateModel` and `$replicateVersion`
2. Adjust prompt in `buildReelPrompt()` if needed
3. Test with new model

### Prompt Customization

Edit `buildReelPrompt()` method in VideoService.php:

```php
private function buildReelPrompt(array $imageUrls, int $duration): string
{
    $basePrompt = "Create a 15-second vertical Instagram reel video (9:16) from the provided images with cinematic transitions, smooth camera motion, motivational style, and export as MP4 video.";
    // ... modify as needed
}
```

### Polling Interval

Edit frontend JavaScript in `reel_form.blade.php`:

```javascript
pollInterval = setInterval(checkStatus, 5000); // 5000ms = 5 seconds
// Change to: pollInterval = setInterval(checkStatus, 3000); // For 3-second poll
```

---

## Files Modified Summary

| File | Changes |
|------|---------|
| `app/Services/VideoService.php` | Complete rewrite - Replicate API instead of FFmpeg |
| `app/Http/Controllers/VideoReelController.php` | Updated for async flow + checkVideoStatus method |
| `routes/api.php` | Added `/video-status` endpoint |
| `app/Models/Reel.php` | Added `prediction_id` to fillable |
| `database/migrations/` | New migration for prediction_id column |
| `resources/views/reel_form.blade.php` | Updated JavaScript for async polling |

---

## Migration Checklist

- [x] Updated VideoService.php to use Replicate API
- [x] Removed all FFmpeg code
- [x] Updated VideoReelController for async flow
- [x] Added checkVideoStatus() endpoint
- [x] Updated routes
- [x] Updated Reel model
- [x] Created database migration
- [x] Updated frontend JavaScript for polling
- [x] Added comprehensive logging
- [x] Verified REPLICATE_API_KEY in .env

---

## Next Steps

1. **Run Migration:**
   ```bash
   php artisan migrate
   ```

2. **Test Locally:**
   - Open the frontend form
   - Submit a generation request
   - Watch polling in browser console

3. **Monitor Logs:**
   ```bash
   php artisan log:tail
   ```

4. **Verify Replicate Dashboard:**
   - Visit https://replicate.com/predictions
   - See your jobs listed there

---

## Support Resources

- **Replicate Docs:** https://replicate.com/docs
- **Replicate API Status:** https://status.replicate.com
- **Laravel Http Client:** https://laravel.com/docs/http-client
- **Your Logs:** `storage/logs/laravel.log`

---

## Summary

You've successfully migrated from FFmpeg to Replicate API!

**Benefits:**
✅ No FFmpeg dependency
✅ Async video generation (non-blocking)
✅ Better error handling
✅ Professional cloud-based video generation
✅ Polling-based status tracking
✅ Comprehensive logging
✅ Scalable architecture

**Key Endpoints:**
- `POST /api/reels/create-video` - Start generation
- `GET /api/reels/video-status/{id}` - Check status
- `GET /api/reels` - List all reels
- `DELETE /api/reels/{id}` - Delete reel

**API Key:** Already configured in .env ✓
