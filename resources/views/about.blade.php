@extends('layouts.frontend')

@section('title', 'About Us')

@section('content')
    <section class="container" style="padding-top: 180px;">
        <div class="section-header reveal">
            <h2>Our Story</h2>
            <p>Empowering creators with the next generation of AI content tools.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 80px;" class="reveal delay-1">
            <div style="line-height: 1.8; color: var(--text-dim);">
                <p style="margin-bottom: 30px;">
                    At ReelGenius AI, we believe that high-quality video content shouldn't be reserved for those with expensive equipment or advanced editing skills. Our mission is to democratize content creation by leveraging the power of Artificial Intelligence.
                </p>
                <p>
                    Founded in 2026, we've quickly grown from a small experimental project into a robust platform that helps thousands of entrepreneurs, marketers, and influencers scale their organic reach across Instagram, TikTok, and YouTube.
                </p>
            </div>
            <div style="position: relative; border-radius: 24px; overflow: hidden; border: 1px solid var(--glass-border);">
                <img src="{{ asset('assets/images/about_hero.png') }}" alt="Our Creative Process" style="width: 100%; display: block;">
            </div>
        </div>
        
        <div style="background: var(--card-bg); border: 1px solid var(--glass-border); padding: 60px; border-radius: 40px;" class="reveal delay-2">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h3 style="color: var(--text-light); margin-bottom: 20px; font-size: 2rem;">Why we built this?</h3>
                <p style="color: var(--text-dim); line-height: 1.8;">
                    The digital landscape is shifting towards short-form vertical video. However, the time required to script, film, and edit these videos is a major bottleneck for most people. ReelGenius AI removes that friction, allowing you to focus on your strategy while we handle the production.
                </p>
            </div>
        </div>
    </section>
@endsection
