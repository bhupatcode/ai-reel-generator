<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;
use App\Http\Controllers\ReelProductionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    // Test endpoint
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is working!',
            'timestamp' => now(),
        ]);
    });

    // Reel endpoints
    Route::post('/reels/generate', [ReelController::class, 'generate'])->name('api.reels.generate');
    Route::post('/reels/process', [ReelProductionController::class, 'process'])->name('api.reels.process');

    // Video creation (Replicate async)
    Route::post('/reels/create-video', [\App\Http\Controllers\VideoReelController::class, 'createVideo'])->name('api.reels.create-video');

    // Check video generation status
    Route::get('/reels/video-status/{predictionId}', [\App\Http\Controllers\VideoReelController::class, 'checkVideoStatus'])->name('api.reels.video-status');

    // Reel management
    Route::get('/reels/{id}', [ReelController::class, 'show'])->name('api.reels.show');
    Route::get('/reels', [\App\Http\Controllers\VideoReelController::class, 'list'])->name('api.reels.list');
    Route::delete('/reels/{id}', [\App\Http\Controllers\VideoReelController::class, 'delete'])->name('api.reels.delete');
});

