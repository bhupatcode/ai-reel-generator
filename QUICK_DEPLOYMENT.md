# 🚀 Quick Deployment Commands

## Copy-Paste Ready Commands

### 1. Navigate to Project
```bash
cd c:\wamp64\www\New Start\ai-reel-generator
```

### 2. Run Database Migration
```bash
php artisan migrate
```

### 3. Clear Cache (Recommended)
```bash
php artisan config:clear && php artisan cache:clear
```

### 4. Verify Replicate API Key
```bash
php artisan tinker
echo env('REPLICATE_API_KEY');
exit
```

### 5. Test VideoService
```bash
php artisan tinker

# Create an instance
$service = app(\App\Services\VideoService::class);

# Test with sample URLs
$result = $service->generateReelVideo([
    'https://via.placeholder.com/720x1280/FF6B6B/ffffff?text=Scene+1',
    'https://via.placeholder.com/720x1280/4ECDC4/ffffff?text=Scene+2'
], 15);

# See the result
json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

exit
```

### 6. Check Routes
```bash
php artisan route:list | grep -E "create-video|video-status"
```

### 7. Start Development Server
```bash
php artisan serve
```

### 8. Test via Browser
```
1. Open: http://localhost:8000
2. Fill in the form
3. Click "Generate Video"
4. Watch the "Generating video..." message
5. Wait for video to complete (may take 30-180 seconds)
6. Video player should appear with the MP4
```

### 9. Real-time Log Monitoring
```bash
php artisan log:tail
```

---

## Full Setup Script (Run All At Once)

```bash
cd c:\wamp64\www\New Start\ai-reel-generator && \
php artisan migrate && \
php artisan config:clear && \
php artisan cache:clear && \
echo "Migration complete!" && \
php artisan serve
```

---

## Testing via cURL

### Create Video Request
```bash
curl -X POST http://localhost:8000/api/reels/create-video \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "script": ["Amazing content about nature"],
    "scenes": ["A beautiful sunset over mountains", "Ocean waves crashing on rocks"],
    "captions": ["Nature is beautiful", "Enjoy the views"],
    "duration": 15
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "prediction_id": "urdwn6x73eypj2l4qiqc2dsfpa",
  "reel_id": 1,
  "status": "processing",
  "message": "Video generation started. Poll /api/reels/video-status/urdwn6x73eypj2l4qiqc2dsfpa"
}
```

### Check Status Request
```bash
# Replace XXX with prediction_id from above response
curl -X GET http://localhost:8000/api/reels/video-status/XXX
```

**Response (Still Processing):**
```json
{
  "success": true,
  "status": "processing"
}
```

**Response (Completed):**
```json
{
  "success": true,
  "status": "completed",
  "video_url": "https://replicate.delivery/xxx.mp4"
}
```

### List Reels
```bash
curl http://localhost:8000/api/reels
```

### Delete Reel
```bash
curl -X DELETE http://localhost:8000/api/reels/1
```

---

## Using Postman/Thunder Client

### Create New Request
1. **Method:** POST
2. **URL:** `http://localhost:8000/api/reels/create-video`
3. **Headers:**
   ```
   Content-Type: application/json
   Accept: application/json
   ```
4. **Body (JSON):**
   ```json
   {
     "script": ["Test script"],
     "scenes": ["A beautiful landscape with mountains"],
     "captions": ["Stunning nature"],
     "duration": 15
   }
   ```

### Poll Status
1. **Method:** GET
2. **URL:** `http://localhost:8000/api/reels/video-status/[PREDICTION_ID_FROM_RESPONSE]`
3. **Headers:**
   ```
   Accept: application/json
   ```

---

## Troubleshooting Commands

### Check Migration Status
```bash
php artisan migrate:status
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

### Re-run Specific Migration
```bash
php artisan migrate:refresh --path=database/migrations/2026_03_04_090000_add_replicate_fields_to_reels_table.php
```

### Check Database
```bash
php artisan tinker
DB::table('reels')->count();
DB::table('reels')->first();
exit
```

### List Available Routes
```bash
php artisan route:list
```

### Check Laravel Version
```bash
php artisan --version
```

### Verify Composer Packages
```bash
composer show | grep -E "illuminate|laravel"
```

---

## Debug Mode (For Troubleshooting)

### Enable Debug Logging
```bash
# In .env set:
APP_DEBUG=true
LOG_LEVEL=debug

# Then run:
php artisan config:clear
```

### Watch Full Logs
```bash
php artisan log:tail --lines=50
```

### Filter Logs by String
```bash
php artisan log:tail | grep -i "video\|replicate\|prediction"
```

---

## Common Issues & Fixes

### Issue: "REPLICATE_API_KEY not configured"
```bash
# Check .env
grep REPLICATE_API_KEY .env

# Should show key starting with r8_
# If not, update with valid key from https://replicate.com/account

# Then clear cache
php artisan config:clear
```

### Issue: "Table reels has no column prediction_id"
```bash
# Run migration
php artisan migrate

# Check table structure
php artisan tinker
Schema::getColumnListing('reels');
exit
```

### Issue: "Route [api.reels.video-status] not defined"
```bash
# Make sure routes are loaded
php artisan route:clear
php artisan route:cache

# Or just restart server with:
php artisan serve
```

### Issue: "Video generation times out"
```bash
# Increase timeout in JavaScript
# Edit: resources/views/reel_form.blade.php
# Find: maxPollAttempts = 240
# Change to: maxPollAttempts = 600  (for 50 minutes)
```

---

## Performance Monitoring

### Check PostgreSQL Slowest Queries
```bash
# View recent logs with execution time
php artisan log:tail | grep -E "SELECT|INSERT|UPDATE"
```

### Monitor API Response Times
```bash
# In browser console:
// Paste this when generating video
console.time('video-generation');
// ... generate video ...
console.timeEnd('video-generation');
```

### Check Disk Usage
```bash
du -sh storage/
# Should be relatively small since videos are on Replicate
```

---

## Deployment to Production

### 1. Before Deploying
```bash
# Make sure all changes are committed
git status

# Run tests (if you have any)
php artisan test

# Build assets if using Vite
npm run build
```

### 2. Deploy Code
```bash
git pull origin main
composer install --optimize-autoloader --no-dev
```

### 3. Run Migrations
```bash
php artisan migrate --force
```

### 4. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 5. Verify
```bash
curl https://yourdomain.com/api/test
# Should return: {"success":true,"message":"API is working!"}
```

---

## Monitoring in Production

### Real-time Logs
```bash
# SSH into server
ssh user@your-server.com
cd /path/to/project

# Watch logs
tail -f storage/logs/laravel.log | grep "VideoService\|VideoReel"
```

### Database Backups
```bash
# Backup reels table before maintenance
php artisan tinker
$count = DB::table('reels')->count();
echo "Backed up $count reels";
exit
```

### API Health Check
```bash
# From your monitoring system
curl -s https://yourdomain.com/api/test | jq '.success'
# Should return: true
```

---

## Next Commands to Run

### Immediate (Required for Setup)
```bash
php artisan migrate
php artisan config:clear
```

### After Setup (For Testing)
```bash
php artisan serve
# Then open http://localhost:8000 in browser
```

### Ongoing (For Monitoring)
```bash
php artisan log:tail
```

---

## Quick Reference - Files Changed

```bash
# View changes to VideoService
git diff app/Services/VideoService.php

# View changes to Controller
git diff app/Http/Controllers/VideoReelController.php

# View changes to routes
git diff routes/api.php

# View changes to Model
git diff app/Models/Reel.php

# View changes to frontend
git diff resources/views/reel_form.blade.php
```

---

## Verification Checklist

```bash
# ✓ Files exist and have no syntax errors
php -l app/Services/VideoService.php
php -l app/Http/Controllers/VideoReelController.php

# ✓ Replicate API key is set
grep REPLICATE_API_KEY .env | head -1

# ✓ Database migrations are created
ls -la database/migrations/ | grep replicate

# ✓ Routes are defined
php artisan route:list | grep -E "create-video|video-status"

# ✓ Model is updated
grep "prediction_id" app/Models/Reel.php
```

---

## Ready to Go! 🎉

```bash
# One-liner to do everything
cd c:\wamp64\www\New Start\ai-reel-generator && php artisan migrate && php artisan config:clear && php artisan serve
```

Then test at: `http://localhost:8000`

---

**For detailed information:**
- 📘 See: `REPLICATE_MIGRATION_GUIDE.md`
- 💾 See: `REPLICATE_CODE_REFERENCE.md`
- ✅ See: `MIGRATION_CHECKLIST.md`
