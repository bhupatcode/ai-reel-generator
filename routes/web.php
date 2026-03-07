<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;

Route::get('/', function () {
    return view('reel_form');
});


Route::post('/generate-reel', [ReelController::class, 'generate'])->name('generate.reel');

