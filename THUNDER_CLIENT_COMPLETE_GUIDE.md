# 🌩️ THUNDER CLIENT - COMPLETE SETUP GUIDE

## ⚡ Quick Import (Recommended)

1. Open **Thunder Client** in VS Code
2. Click **Collections** (folder icon on left)
3. Click **Import** button
4. Select: **ThunderClient_Collection.json**
5. ✅ Done! All 5 requests ready to use

---

## 📋 OR Manually Create These 5 Requests

### REQUEST 1: Test API ✅

**Name:** `01. Test API Status`

```
Method:   GET
URL:      http://localhost:8000/api/test
Headers:  (Leave empty - no headers needed)
Body:     (Leave empty)
```

**Description:** Quick test to check if API is working

---

### REQUEST 2: List All Reels ✅

**Name:** `02. List All Reels`

```
Method:   GET
URL:      http://localhost:8000/api/reels
Headers:  (Leave empty)
Body:     (Leave empty)
```

**Description:** Get list of all generated reels

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "topic": "Indian Biryani",
      "mood": "Happy",
      "duration": 30,
      "status": "completed"
    }
  ],
  "pagination": {
    "total": 3,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### REQUEST 3: Get Specific Reel ✅

**Name:** `03. Get Reel by ID`

```
Method:   GET
URL:      http://localhost:8000/api/reels/1
Headers:  (Leave empty)
Body:     (Leave empty)
```

**Description:** Get details of one reel by ID (change the number at end)

**Response Example:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "topic": "Indian Biryani",
    "mood": "Happy and Energetic",
    "duration": 30,
    "script": ["Scene 1...", "Scene 2..."],
    "scenes": ["Description 1...", "Description 2..."],
    "captions": ["Caption 1...", "Caption 2..."],
    "music": "Upbeat instrumentals",
    "status": "completed"
  }
}
```

---

### REQUEST 4: Generate Reel - Food 🍕

**Name:** `04. Generate Reel - Food`

```
Method:   POST
URL:      http://localhost:8000/api/reels/generate
```

**Headers:**
```
Header Name:  Content-Type
Header Value: application/json
```

**Body (JSON):**
```json
{
  "topic": "How to make Indian Biryani at Home",
  "mood": "Happy and Energetic",
  "duration": 30
}
```

**What It Does:** Generates a complete reel with AI-created script, scenes, captions, and music recommendation

---

### REQUEST 5: Generate Reel - Education 🎓

**Name:** `05. Generate Reel - Education`

```
Method:   POST
URL:      http://localhost:8000/api/reels/generate
```

**Headers:**
```
Header Name:  Content-Type
Header Value: application/json
```

**Body (JSON):**
```json
{
  "topic": "Learn Python Programming for Beginners",
  "mood": "Professional and Informative",
  "duration": 60
}
```

---

### REQUEST 6: Generate Reel - Entertainment 😂

**Name:** `06. Generate Reel - Entertainment`

```
Method:   POST
URL:      http://localhost:8000/api/reels/generate
```

**Headers:**
```
Header Name:  Content-Type
Header Value: application/json
```

**Body (JSON):**
```json
{
  "topic": "Funny Pet Videos Compilation",
  "mood": "Funny and Hilarious",
  "duration": 15
}
```

---

## 🎯 Duration Options

```
15 seconds  ✅
30 seconds  ✅
60 seconds  ✅
90 seconds  ✅
```

---

## 😊 Mood Examples

```
Happy and Energetic
Fun and Entertaining
Professional and Serious
Funny and Hilarious
Exciting and Dramatic
Educational and Informative
Relaxing and Calm
Motivational and Inspiring
Sad and Emotional
Mysterious and Suspenseful
```

Any mood works! These are just examples.

---

## 📝 More Body Examples You Can Try

### Example 1: Travel
```json
{
  "topic": "Top 10 Best Beaches in Goa, India",
  "mood": "Relaxing and Tropical",
  "duration": 60
}
```

### Example 2: DIY
```json
{
  "topic": "Easy DIY Home Decoration Ideas",
  "mood": "Creative and Fun",
  "duration": 30
}
```

### Example 3: Health
```json
{
  "topic": "10 Minute Morning Yoga Routine",
  "mood": "Calm and Motivational",
  "duration": 15
}
```

### Example 4: Technology
```json
{
  "topic": "Latest AI Technology Trends 2026",
  "mood": "Professional and Educational",
  "duration": 60
}
```

### Example 5: Cooking
```json
{
  "topic": "Quick and Easy Samosa Recipe",
  "mood": "Happy and Appetizing",
  "duration": 30
}
```

---

## ✅ How to Use in Thunder Client

### Step 1: Click on Request
Click any request from the left sidebar

### Step 2: View Details
You'll see:
- URL field at top
- METHOD dropdown (GET/POST)
- Headers section
- Body section

### Step 3: Click Send
Big blue "Send" button at the top-right

### Step 4: View Response
Response appears in right panel (JSON format)

---

## 🔴 Common Mistakes to Avoid

❌ **Wrong:**
```json
{
  topic: "Something",   // Missing quotes
  mood: Happy,          // Missing quotes
  duration: "30"        // Should be number, not string
}
```

✅ **Correct:**
```json
{
  "topic": "Something",
  "mood": "Happy",
  "duration": 30
}
```

---

## 🎬 Response Structure

When you generate a reel, you'll get:

```json
{
  "success": true,
  "message": "Reel generated successfully",
  "data": {
    "id": 4,
    "topic": "Your topic here",
    "mood": "Your mood here",
    "duration": 30,
    "status": "completed",
    "script": [
      "Line 1 of script",
      "Line 2 of script",
      "Line 3 of script",
      "Line 4 of script",
      "Line 5 of script"
    ],
    "scenes": [
      "Scene 1 description",
      "Scene 2 description",
      "Scene 3 description",
      "Scene 4 description",
      "Scene 5 description"
    ],
    "captions": [
      "Caption 1",
      "Caption 2",
      "Caption 3",
      "Caption 4",
      "Caption 5"
    ],
    "music": "Music recommendation"
  }
}
```

---

## 🔍 Testing Flow

1️⃣ **Start with Test Endpoint**
   - Request: `01. Test API Status`
   - Should see: `"API is working!"`

2️⃣ **List Existing Reels**
   - Request: `02. List All Reels`
   - Should see: List of all reels from database

3️⃣ **Generate New Reel**
   - Request: `04. Generate Reel - Food` (or any)
   - Wait ~13 seconds for AI to process
   - Should see: Complete reel with script, scenes, captions, music

4️⃣ **View Generated Reel**
   - Request: `03. Get Reel by ID`
   - Change ID to the one from step 3
   - Should see: Same reel data

---

## ⏱️ Response Times

```
GET /api/test            → ~0.5ms  (instant)
GET /api/reels           → ~1ms    (instant)
GET /api/reels/1         → ~0.5ms  (instant)
POST /api/reels/generate → ~13s    (AI processing)
```

---

## 🐛 Troubleshooting

### Server not responding?
Make sure server is running:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Getting Errors?
Check the response carefully. Common issues:
- Missing required fields (topic, mood, duration)
- Invalid duration (must be 15, 30, 60, or 90)
- Wrong headers (Content-Type must be application/json for POST)

### API Returns Empty Data?
That's fine! Generate a new reel first with POST request.

---

## 💡 Pro Tips

✅ You can create multiple versions of the same endpoint with different bodies
✅ Copy any request and modify it for testing
✅ View response in pretty JSON or raw format
✅ Check Status Code (should be 200 for success, 500 for errors)
✅ Look at response time to see performance

---

## 🎉 You're Ready!

Import the collection and start testing! 🚀

Questions? Check the other documentation files:
- API_TESTING_GUIDE.md
- PROJECT_STATUS.md
- SETUP_COMPLETE.md
