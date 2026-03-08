<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $featuredReviews = Review::where('is_visible', true)->latest()->take(3)->get();
        return view('landing', compact('featuredReviews'));
    }

    public function about()
    {
        return view('about');
    }

    public function reviews()
    {
        $reviews = Review::where('is_visible', true)->latest()->get();
        return view('reviews', compact('reviews'));
    }
}
