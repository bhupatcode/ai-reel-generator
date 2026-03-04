# API Reel Generator - API Testing Guide

## 🚀 API is Now Working!

Your Laravel API is running successfully on `http://localhost:8000`

### Base URL
```
http://localhost:8000/api
```

---

## 📝 API Endpoints

### 1. Test API Status
**GET** `/api/test`

Check if the API is running and responding correctly.

```bash
curl -X GET http://localhost:8000/api/test
```

**Response:**
```json
{
  "success": true,
  "message": "API is working!",
  "timestamp": "2026-03-04T09:38:20.278108Z"
}
```

---

### 2. Generate New Reel
**POST** `/api/reels/generate`

Generate a new AI-powered reel with script, scenes, captions, and music recommendations.

**Required Parameters:**
- `topic` (string, max 200 chars) - The topic for the reel
- `mood` (string) - The mood/vibe (e.g., Happy, Funny, Serious, Energetic)
- `duration` (integer) - Duration in seconds (15, 30, 60, or 90)

```bash
curl -X POST http://localhost:8000/api/reels/generate \
  -H "Content-Type: application/json" \
  -d '{
    "topic": "How to make Indian biryani",
    "mood": "Happy and Energetic",
    "duration": 30
  }'
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Reel generated successfully",
  "data": {
    "id": 4,
    "topic": "How to make Indian biryani",
    "mood": "Happy and Energetic",
    "duration": 30,
    "status": "completed",
    "script": [
      "Scene 1: Introduction",
      "Scene 2: Ingredients preparation",
      ...
    ],
    "scenes": [
      "Show ingredients on table",
      ...
    ],
    "captions": [
      "Welcome to cooking show",
      ...
    ],
    "music": "Upbeat Indian instrumental"
  }
}
```

---

### 3. List All Reels
**GET** `/api/reels`

Get a paginated list of all generated reels.

```bash
curl -X GET http://localhost:8000/api/reels
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "topic": "How to make Indian biryani",
      "mood": "Happy and Energetic",
      "duration": 30,
      "status": "completed",
      "created_at": "2026-03-04T09:40:00.000000Z",
      ...
    }
  ],
  "pagination": {
    "total": 4,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### 4. Get Specific Reel
**GET** `/api/reels/{id}`

Get details of a specific reel by its ID.

```bash
curl -X GET http://localhost:8000/api/reels/4
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 4,
    "topic": "How to make Indian biryani",
    "mood": "Happy and Energetic",
    "duration": 30,
    "status": "completed",
    "script": [...],
    "scenes": [...],
    "captions": [...],
    "music": "Upbeat Indian instrumental",
    ...
  }
}
```

---

## 🧪 Testing with Thunder Client

### Import Collection
1. Open **Thunder Client** in VS Code
2. Click **Import** → Select `thunder-client-collection.json` from the project root
3. All requests will be available to test

### Manual Testing
You can create the following requests in Thunder Client:

#### Request 1: Test API
- **Method:** GET
- **URL:** `http://localhost:8000/api/test`
- **Headers:** None required

#### Request 2: Generate Reel
- **Method:** POST
- **URL:** `http://localhost:8000/api/reels/generate`
- **Headers:** `Content-Type: application/json`
- **Body:**
```json
{
  "topic": "Indian Street Food",
  "mood": "Exciting and Appetizing",
  "duration": 30
}
```

#### Request 3: List Reels
- **Method:** GET
- **URL:** `http://localhost:8000/api/reels`
- **Headers:** None required

#### Request 4: Get Reel by ID
- **Method:** GET
- **URL:** `http://localhost:8000/api/reels/1`
- **Headers:** None required

---

## 🔧 Testing with PowerShell

```powershell
# Test API Status
Invoke-RestMethod -Uri "http://localhost:8000/api/test" -Method Get | ConvertTo-Json

# Generate Reel
$body = @{
    topic = "How to make biryani"
    mood = "Happy and Energetic"
    duration = 30
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/reels/generate" `
  -Method Post `
  -Body $body `
  -ContentType "application/json" | ConvertTo-Json -Depth 5

# List All Reels
Invoke-RestMethod -Uri "http://localhost:8000/api/reels" -Method Get | ConvertTo-Json
```

---

## 💾 Database
- **Connection:** MySQL via WAMP64
- **Database:** `ai_reel_generator`
- **Table:** `reels`

All generated reels are stored in the database with their complete metadata.

---

## 🔐 Environment Variables
- **OpenRouter API Key:** Configured in `.env` file
- **API Endpoint:** `https://openrouter.ai/api/v1/chat/completions`
- **Model:** `openai/gpt-4o-mini`

---

## ⚠️ Error Handling
The API returns standard HTTP status codes:
- **200** - Successful request
- **400** - Validation error (missing/invalid parameters)
- **404** - Reel not found
- **500** - Server error (check logs)

---

## 📊 Project Status
✅ API Routes configured  
✅ Controller methods implemented  
✅ Database models set up  
✅ OpenRouter AI Service integrated  
✅ Error handling in place  
✅ Validation rules applied  
✅ Testing endpoints available  

**Everything is ready to use!**
