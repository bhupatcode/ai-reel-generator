@extends('layouts.frontend')

@section('title', 'Terms & Conditions')

@section('styles')
    <style>
        .policy-section {
            padding: 160px 0 100px;
        }

        .policy-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 50px;
            max-width: 900px;
            margin: 0 auto;
            line-height: 1.8;
            color: var(--text-dim);
        }

        .policy-card h3 {
            color: var(--text-light);
            margin: 30px 0 15px;
            font-size: 1.5rem;
        }

        .policy-card p {
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="policy-section reveal">
        <div class="container">
            <div class="section-header">
                <h2>Terms & <span class="gradient-text">Conditions</span></h2>
                <p>Please read these terms carefully before using our premium AI services.</p>
            </div>

            <div class="policy-card">
                <h3>1. Acceptance of Terms</h3>
                <p>By accessing or using ReelGenius AI, you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our services.</p>

                <h3>2. Use of AI Service</h3>
                <p>Our service allows you to generate video scripts and content using AI. You are responsible for the content you generate and must ensure it does not violate any laws or third-party rights.</p>

                <h3>3. Intellectual Property</h3>
                <p>The templates, UI design, and branding of ReelGenius AI are our property. The AI-generated content (scripts, scenes) is yours to use for your personal or commercial projects.</p>

                <h3>4. Limitation of Liability</h3>
                <p>We provide our services "as is" and are not responsible for any inaccuracies in the AI-generated content or any damages resulting from the use of our platform.</p>
            </div>
        </div>
    </div>
@endsection
