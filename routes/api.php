<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;

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
    Route::get('/reels/{id}', [ReelController::class, 'show'])->name('api.reels.show');
    Route::get('/reels', [ReelController::class, 'index'])->name('api.reels.list');
});

