<?php

namespace App\Http\Controllers;

use App\Services\ReelProductionService;
use App\Services\VideoService;
use Illuminate\Http\Request;

class ReelProductionController extends Controller
{
    protected $reelService;
    protected $videoService;

    public function __construct(ReelProductionService $reelService, VideoService $videoService)
    {
        $this->reelService = $reelService;
        $this->videoService = $videoService;
    }

    public function process(Request $input)
    {
        $validated = $input->validate([
            'script' => 'required|array|size:5',
            'scenes' => 'required|array|size:5',
            'captions' => 'required|array|size:5',
            'music' => 'required|string'
        ]);

        $result = $this->reelService->processReelData($validated);

        return response()->json($result);
    }

}
