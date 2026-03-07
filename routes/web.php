<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/login', [AuthController::class , 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::get('/register', [AuthController::class , 'showRegisterForm'])->name('register');
Route::post('/check-email', [AuthController::class , 'checkEmail'])->name('check.email');
Route::post('/register', [AuthController::class , 'register']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class , 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class , 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class , 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class , 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
            return view('reel_form');
        }
        );
        Route::get('/profile', [ProfileController::class , 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class , 'update'])->name('profile.update');
        Route::post('/generate-reel', [ReelController::class , 'generate'])->name('generate.reel');

        Route::get('/test-email', function () {
            try {
                \Illuminate\Support\Facades\Mail::raw('This is a test email.', function ($message) {
                            $message->to(Auth::user()->email)->subject('Test Email');
                        }
                        );
                        return 'Email sent successfully!';
                    }
                    catch (\Throwable $e) {
                        return 'Email failed: ' . $e->getMessage();
                    }
                }
                );            });
