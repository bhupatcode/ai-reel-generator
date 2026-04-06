<?php

namespace App\Http\Controllers;

use App\Models\Reel;
use App\Services\OpenRouterService;
use App\Services\ImageGenerationService;
use App\Services\ReelProductionService;
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
            'topic' => 'required|string|max:255',
            'mood' => 'required|string|max:100',
            'language' => 'required|string|max:50',
            'duration' => 'required|integer|min:5|max:60',
        ]);

        $reel = Reel::create([
                'topic' => $validated['topic'],
                'mood' => $validated['mood'],
                'language' => $validated['language'],
                'duration' => $validated['duration'],
            'status' => 'pending',
        ]);

        try {
            $ai = new OpenRouterService();
            $imageService = new ImageGenerationService();
            $productionService = new ReelProductionService();

            $aiResult = $ai->generateReel(
                $validated['topic'],
                $validated['mood'],
                (int)$validated['duration'],
                $validated['language']
            );

            $script = $aiResult['script'] ?? [];
            $scenes = $aiResult['scenes'] ?? [];
            $captions = $aiResult['captions'] ?? [];
            
            // Generate images for each scene
            $imageUrls = [];
            foreach ($scenes as $scene) {
                // Generate a more descriptive prompt for the image
                $imagePrompt = $productionService->processReelData(['scenes' => [$scene]])['image_prompts'][0];
                $imageUrls[] = $imageService->generateFromPrompt($imagePrompt);
            }

            $reel->update([
                'script' => $script,
                'scenes' => $scenes,
                'captions' => $captions,
                'images' => $imageUrls,
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
                    'images' => $reel->images,
                    'music' => $reel->music,
                ],
            ]);
        } catch (Exception $e) {
            $reel->update(['status' => 'failed']);

            Log::error('Reel generation failed', [
                'reel_id' => $reel->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Reel generation failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $reel = Reel::find($id);

            if (!$reel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reel not found',
                ], 404);
            }

            $reel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reel deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete reel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
