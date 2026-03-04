<?php

namespace App\Http\Controllers;

use App\Models\Reel;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class ReelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reels = Reel::latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reels->items(),
            'pagination' => [
                'total' => $reels->total(),
                'per_page' => $reels->perPage(),
                'current_page' => $reels->currentPage(),
                'last_page' => $reels->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $reel = Reel::find($id);

        if (!$reel) {
            return response()->json([
                'success' => false,
                'message' => 'Reel not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reel,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:200',
            'mood' => 'required|string',
            'duration' => 'required|integer|in:15,30,60,90',
        ]);

        $reel = Reel::create([
            'topic' => $validated['topic'],
            'mood' => $validated['mood'],
            'duration' => (int)$validated['duration'],
            'status' => 'pending',
        ]);

        try {
            $ai = new OpenRouterService();

            $aiResult = $ai->generateReel(
                $validated['topic'],
                $validated['mood'],
                (int)$validated['duration']
            );

            $reel->update([
                'script' => $aiResult['script'] ?? [],
                'scenes' => $aiResult['scenes'] ?? [],
                'captions' => $aiResult['captions'] ?? [],
                'music' => $aiResult['music'] ?? '',
                'raw_response' => json_encode($aiResult),
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reel generated successfully',
                'data' => [
                    'id' => $reel->id,
                    'topic' => $reel->topic,
                    'mood' => $reel->mood,
                    'duration' => $reel->duration,
                    'status' => $reel->status,
                    'script' => $reel->script,
                    'scenes' => $reel->scenes,
                    'captions' => $reel->captions,
                    'music' => $reel->music,
                ],
            ]);
        }
        catch (Exception $e) {
            $reel->update(['status' => 'failed']);

            Log::error('Reel generation failed', [
                'reel_id' => $reel->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate reel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
