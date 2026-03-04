/**
 * STEP-BY-STEP BACKEND FLOW FOR VIDEO CREATION
 * 
 * FLOW OVERVIEW:
 * 1. Receive JSON input with images and metadata
 * 2. Add captions as text overlays on images
 * 3. Create video sequence from images (9:16 vertical)
 * 4. Add background music
 * 5. Return video URL
 */

// ============================================
// STEP 1: INPUT JSON STRUCTURE
// ============================================

POST /api/reels/create-video
Content-Type: application/json

{
  "image_paths": [
    "/storage/images/image_1.jpg",
    "/storage/images/image_2.jpg",
    "/storage/images/image_3.jpg",
    "/storage/images/image_4.jpg",
    "/storage/images/image_5.jpg"
  ],
  "captions": [
    "Your first caption here",
    "Your second caption here",
    "Your third caption here",
    "Your fourth caption here",
    "Your fifth caption here"
  ],
  "music_path": "/storage/music/background_music.mp3"
}

// ============================================
// STEP 2: RESPONSE JSON
// ============================================

{
  "success": true,
  "video_id": "reel_64a3b2c8d5e7f",
  "video_path": "/videos/reel_64a3b2c8d5e7f.mp4",
  "video_url": "http://localhost/videos/reel_64a3b2c8d5e7f.mp4",
  "file_size": 52428800
}

// ============================================
// STEP 3: BACKEND FLOW DETAILS
// ============================================

VideoService::createReelVideo() {
  
  Step 1: Add Captions
  - Iterate through image_paths array
  - For each image, use FFmpeg to overlay caption text
  - Text: white, size 24px, bold, bottom center
  - Background: black with transparency
  - Save captioned images to storage/app/videos/
  
  Step 2: Create Video Sequence (9:16 aspect ratio)
  - Create FFmpeg concat demuxer file
  - Each image displayed for 4 seconds
  - Scale to 1080x1920 (vertical 9:16)
  - Use libx264 codec, CRF 23 (high quality)
  - Output format: MP4
  
  Step 3: Add Audio
  - Input: video file (no audio)
  - Input: music file (MP3/WAV)
  - Use FFmpeg to merge
  - Copy video codec, re-encode audio to AAC
  - Use shortest flag to match durations
  
  Step 4: Cleanup
  - Delete temporary captioned images
  - Keep only final MP4 video
  
  Step 5: Return Response
  - video_id: unique identifier
  - video_path: relative path for storage
  - video_url: full URL for frontend
  - file_size: in bytes
}

// ============================================
// STEP 4: COMPLETE BACKEND WORKFLOW
// ============================================

Frontend (HTML + Bootstrap + jQuery + AJAX):
  1. Generate images using HuggingFace API
  2. Store images locally or get file paths
  3. Collect captions from Gemini response
  4. Collect music file path
  5. POST to /api/reels/create-video with all data
  6. Receive video_url in response
  7. Display video player with generated video

Backend Processing:
  
  POST /api/reels/create-video
    ↓
  ReelProductionController::createReelVideo()
    ↓
  Validate Input:
    - image_paths array (required, min 1)
    - captions array (required, min 1)
    - music_path string (optional)
    ↓
  VideoService::createReelVideo()
    ↓
  1. addCaptions($imagePaths, $captions)
     - For each image + caption pair
     - FFmpeg: drawtext filter overlay
     - Output: captioned PNG files
    ↓
  2. createVideoFromImages($captionedImages)
     - Create concat demuxer file
     - Duration: 4 seconds per image
     - Resolution: 1080x1920 (9:16)
     - Codec: libx264
     - Output: MP4 without audio
    ↓
  3. addAudioToVideo($videoPath, $audioPath)
     - Get video duration
     - Merge video + music
     - Copy video, re-encode audio
     - Output: final MP4 with audio
    ↓
  4. cleanupTempFiles()
     - Delete captioned images
     - Delete concat file
     - Keep final video
    ↓
  Return JSON Response
    ↓
  Frontend displays: http://localhost/videos/reel_xxxxx.mp4

// ============================================
// STEP 5: FILE STRUCTURE
// ============================================

app/
  Services/
    VideoService.php          ← FFmpeg video creation
    ReelProductionService.php ← Image prompt generation
  Http/
    Controllers/
      ReelProductionController.php ← Process & createReelVideo methods

routes/
  api.php → POST /api/reels/create-video

public/
  videos/                     ← Final MP4 videos stored here

storage/
  app/
    videos/                   ← Temporary files during processing

// ============================================
// STEP 6: FFMPEG COMMANDS USED
// ============================================

1. Add Caption Overlay:
   ffmpeg -i image.jpg
           -vf "drawtext=fontfile='Arial.ttf':text='Caption':fontsize=24:fontcolor=white:x=(w-text_w)/2:y=h-100:borderw=2:bordercolor=black"
           -y output.png

2. Create Video from Images:
   ffmpeg -f concat -safe 0 -i concat.txt
          -vf "scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2:black"
          -c:v libx264 -crf 23 -pix_fmt yuv420p
          -y output.mp4

3. Add Audio to Video:
   ffmpeg -i video.mp4 -i music.mp3
          -c:v copy -c:a aac -shortest
          -y final.mp4

4. Get Media Duration:
   ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1:nv=1 file.mp4

// ============================================
// STEP 7: AJAX CALL FROM FRONTEND
// ============================================

$.ajax({
  url: '/api/reels/create-video',
  type: 'POST',
  contentType: 'application/json',
  data: JSON.stringify({
    image_paths: ['/storage/img1.jpg', '/storage/img2.jpg', ...],
    captions: ['Caption 1', 'Caption 2', ...],
    music_path: '/storage/music.mp3'
  }),
  success: function(response) {
    if (response.success) {
      // Display video
      $('#video-player').html(
        '<video width="100%" height="auto" controls>' +
        '<source src="' + response.video_url + '" type="video/mp4">' +
        '</video>'
      );
    }
  },
  error: function(error) {
    console.log('Video creation failed:', error);
  }
});
