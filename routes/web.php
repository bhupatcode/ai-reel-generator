<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReviewController;

Route::get('/', [LandingController::class , 'index'])->name('landing');
Route::get('/about', [LandingController::class , 'about'])->name('about');
Route::get('/reviews', [LandingController::class , 'reviews'])->name('reviews');
Route::get('/faq', [LandingController::class , 'faq'])->name('faq');
Route::get('/privacy', [LandingController::class , 'privacy'])->name('privacy');
Route::get('/terms', [LandingController::class , 'terms'])->name('terms');
Route::post('/reviews', [ReviewController::class , 'submit'])->name('reviews.submit');
Route::get('/contact', [ContactController::class , 'show'])->name('contact');
Route::post('/contact', [ContactController::class , 'submit'])->name('contact.submit');

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
    Route::get('/dashboard', function () {
            return view('reel_form');
        }
        )->name('dashboard');
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
                );
            });
