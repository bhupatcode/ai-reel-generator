@extends('layouts.frontend')

@section('title', 'AI Video Reel Generator')

@section('styles')
    <style>
        .hero {
            padding: 160px 0 100px;
            text-align: center;
            position: relative;
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 25px;
            letter-spacing: -2px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #f43f5e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-dim);
            max-width: 700px;
            margin: 0 auto 40px;
        }

        .hero-btns {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 60px;
        }

        .hero-visual {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .hero-visual img {
            width: 100%;
            border-radius: 12px;
            filter: brightness(0.8);
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            padding: 40px;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: var(--text-dim);
        }

        /* Review Section */
        .reviews-preview {
            background: rgba(15, 23, 42, 0.5);
            padding: 100px 0;
            border-radius: 60px;
            margin: 60px 0;
        }

        .review-slider {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 0;
            scrollbar-width: none;
        }

        .review-card {
            min-width: 350px;
            padding: 30px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
        }

        .review-card .stars {
            color: #fbbf24;
            margin-bottom: 15px;
        }

        .review-card p {
            font-style: italic;
            margin-bottom: 20px;
        }

        .review-author {
            font-weight: 600;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 100px 0 60px;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero-btns {
                flex-direction: column;
                gap: 15px;
            }
            .hero-btns .btn {
                width: 100%;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .review-card {
                min-width: 280px;
            }
        }
    </style>
@section('content')
    <section class="hero" style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('{{ asset('assets/images/landing_hero.png') }}'); background-size: cover; background-position: center;">
        <div class="container reveal">
            <h1 class="animate-float">Create Stunning Reels <br> <span class="gradient-text">Powered by AI</span></h1>
            <p class="reveal delay-1">Transform your scripts and ideas into high-quality vertical videos for TikTok, Instagram, and YouTube Shorts. No video editing skills required.</p>
            <div class="hero-btns reveal delay-2">
                <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="padding: 16px 40px; font-size: 1.1rem;">Start Creating Now</a>
                <a href="#features" class="btn btn-outline" style="padding: 16px 40px; font-size: 1.1rem;">How it Works</a>
            </div>
            <div class="hero-visual reveal delay-3">
                <div style="aspect-ratio: 16/9; background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; border-radius: 12px; border: 1px solid var(--glass-border);">
                    <div class="animate-pulse" style="width: 80px; height: 80px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 12L3 21V3L21 12Z" fill="white"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="container">
        <div class="section-header reveal">
            <h2>Everything you need</h2>
            <p>Powerful tools designed to help you scale your content creation without the overhead.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal delay-1">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <h3>AI Content Engine</h3>
                <p>Generate scripts, scenes, and captions automatically using Google Gemini integration.</p>
            </div>
            <div class="feature-card reveal delay-2">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m3 9 18-1"/><path d="m3 15 18-1"/></svg>
                </div>
                <h3>Vertical Optimized</h3>
                <p>All videos are generated in 9:16 aspect ratio, perfectly formatted for modern social media platforms.</p>
            </div>
            <div class="feature-card reveal delay-3">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Cost Effective</h3>
                <p>Scale your organic reach with high-frequency posting without hiring expensive editors.</p>
            </div>
        </div>
    </section>

    <section class="reviews-preview">
        <div class="container">
            <div class="section-header reveal">
                <h2>Loved by creators</h2>
                <p>Join thousands of content creators who are scaling their brand with AI.</p>
            </div>
            <div class="review-slider">
                @forelse($featuredReviews as $review)
                    <div class="review-card reveal">
                        <div class="stars">
                            @for($i = 0; $i < $review->rating; $i++)
                                ★
                            @endfor
                        </div>
                        <p>"{{ $review->comment }}"</p>
                        <div class="review-author">{{ $review->name }}</div>
                    </div>
                @empty
                    <div class="review-card reveal">
                        <div class="stars">★★★★★</div>
                        <p>"This tool has completely changed how I create content. I can now post 3 times a day without any stress."</p>
                        <div class="review-author">Sarah Johnson</div>
                    </div>
                    <div class="review-card reveal delay-1">
                        <div class="stars">★★★★★</div>
                        <p>"The AI script generation is surprisingly good. It captures my brand voice perfectly every time."</p>
                        <div class="review-author">Mark Davis</div>
                    </div>
                @endforelse
            </div>
            <div style="text-align: center; margin-top: 40px;" class="reveal">
                <a href="{{ url('/reviews') }}" class="btn btn-outline">Read All Reviews</a>
            </div>
        </div>
    </section>

    <section class="container reveal" style="text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(244, 63, 94, 0.1)); padding: 80px; border-radius: 40px;">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to go viral?</h2>
        <p style="margin-bottom: 40px;">Create your first AI reel in under 60 seconds.</p>
        <a href="{{ url('/register') }}" class="btn btn-primary" style="padding: 16px 40px;">Get Started for Free</a>
    </section>
@endsection
