@extends('layouts.frontend')

@section('title', 'Contact Us')

@section('styles')
    <style>
        .contact-container {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            padding-top: 60px;
        }

        .contact-info h3 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .contact-info p {
            color: var(--text-dim);
            margin-bottom: 40px;
        }

        .info-item {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-icon {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .contact-form {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            padding: 40px;
            border-radius: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dim);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--primary);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #10b981;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <section class="container" style="padding-top: 180px;">
        <div class="section-header reveal">
            <h2>Get in Touch</h2>
            <p>Have questions about our AI tools? Our team is here to help.</p>
        </div>

        <div class="contact-container reveal delay-1">
            <div class="contact-info">
                <h3>Let's talk about your content strategy.</h3>
                <p>We're here to help you navigate the world of AI content creation. Whether you're a solo creator or a large agency, we have solutions for you.</p>
                
                <div style="margin-bottom: 40px; border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border);">
                    <img src="{{ asset('assets/images/contact_hero.png') }}" alt="Connect with us" style="width: 100%; display: block;">
                </div>

                <div class="info-item reveal delay-2">
                    <div class="info-icon">📍</div>
                    <div>
                        <strong>Location</strong>
                        <p>Global Remote Team</p>
                    </div>
                </div>
                <div class="info-item reveal delay-3">
                    <div class="info-icon">📧</div>
                    <div>
                        <strong>Email</strong>
                        <p>support@reelgenius.ai</p>
                    </div>
                </div>
            </div>

            <div class="contact-form reveal delay-2">
                @if(session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ url('/contact') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required placeholder="How can we help?">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Your message here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                </form>
            </div>
        </div>
    </section>
@endsection
