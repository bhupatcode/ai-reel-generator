@extends('layouts.frontend')

@section('title', 'Login')

@section('styles')
    <style>
        :root {
            --auth-bg-dark: #020617;
            --auth-accent: #6366f1;
            --auth-accent-glow: rgba(99, 102, 241, 0.4);
        }

        /* Override main content to be full width/height for auth */
        main {
            padding: 0 !important;
            min-height: 100vh;
            display: flex;
            background: var(--auth-bg-dark);
            overflow: hidden;
        }

        .auth-split-layout {
            display: flex;
            width: 100%;
            height: 100vh;
            padding-top: 80px; /* Account for fixed navbar */
        }

        /* Left Side: Visual/Branding */
        .auth-visual-side {
            flex: 1.2;
            position: relative;
            background: linear-gradient(rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.8)), url('{{ asset('assets/images/landing_hero.png') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            overflow: hidden;
        }

        .auth-visual-side::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at center, transparent 0%, var(--auth-bg-dark) 100%);
            opacity: 0.6;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            max-width: 500px;
        }

        .visual-content h2 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -2px;
        }

        .visual-content p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .stat-item h4 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--auth-accent);
            margin-bottom: 4px;
        }

        .stat-item span {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Right Side: Form */
        .auth-form-side {
            flex: 1;
            background: var(--auth-bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
            animation: fadeInRight 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .form-card h3 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: white;
        }

        .form-card p {
            color: var(--text-dim);
            margin-bottom: 40px;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-dim);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .input-wrapper input {
            width: 100%;
            padding: 16px 20px 16px 52px;
            background: rgba(30, 41, 59, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            background: rgba(30, 41, 59, 0.5);
            border-color: var(--auth-accent);
            box-shadow: 0 0 0 4px var(--auth-accent-glow);
            outline: none;
        }

        .input-wrapper input:focus + i {
            color: var(--auth-accent);
        }

        .btn-premium {
            width: 100%;
            padding: 16px;
            background: var(--auth-accent);
            color: white;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
            margin-top: 10px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            background: #4f46e5;
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
        }

        .auth-links {
            text-align: center;
            margin-top: 32px;
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        .auth-links a {
            color: var(--auth-accent);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 10px;
            color: var(--text-dim);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--auth-accent);
        }

        /* Floating particles background */
        .particles {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .auth-visual-side { display: none; }
            .auth-form-side { flex: 1; padding: 40px 20px; }
        }

        @media (max-width: 480px) {
            .auth-form-side {
                padding: 20px 15px;
            }
            .form-card h3 {
                font-size: 1.8rem;
            }
            .form-card p {
                font-size: 0.9rem;
                margin-bottom: 30px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="auth-split-layout">
        {{-- Left: Visual Side --}}
        <div class="auth-visual-side">
            <div class="visual-content reveal">
                <h2>Scale your vision <br> with <span class="gradient-text">Gemini AI</span></h2>
                <p>Join over 10,000+ creators who are automating their video content and growing 10x faster using our professional AI tools.</p>
                
                <div class="stat-grid">
                    <div class="stat-item reveal delay-1">
                        <h4>1M+</h4>
                        <span>Reels Created</span>
                    </div>
                    <div class="stat-item reveal delay-2">
                        <h4>98%</h4>
                        <span>Satisfaction</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Form Side --}}
        <div class="auth-form-side">
            <div class="form-card">
                <!-- <div style="margin-bottom: 40px; display: flex; align-items: center; justify-content: space-between;">
                    <a href="javascript:history.length > 1 ? history.back() : window.location.href='/'" style="text-decoration: none; display: flex; align-items: center; gap: 8px; color: var(--text-dim); font-weight: 600; transition: color 0.3s ease;" onmouseover="this.style.color='white'" onmouseout="this.style.color='var(--text-dim)'">
                        <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <i class="bi bi-arrow-left" style="font-size: 1.2rem;"></i>
                        </div>
                        <span style="font-size: 0.95rem;">Back</span>
                    </a>

                    <a href="/" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.2rem; font-weight: 800; color: white; letter-spacing: -0.5px;">AI REELS</span>
                        <div style="width: 32px; height: 32px; background: var(--auth-accent); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m3 9 18-1"/><path d="m3 15 18-1"/></svg>
                        </div>
                    </a>
                </div> -->

                <h3>Welcome back</h3>
                <p>Please enter your details to access your dashboard.</p>

                @if (session('status'))
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                            <i class="bi bi-envelope-at" style="pointer-events: none;"></i>
                        </div>
                        @error('email')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 8px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" required placeholder="••••••••">
                            <i class="bi bi-shield-lock" style="pointer-events: none;"></i>
                            <i class="bi bi-eye toggle-password" style="position: absolute; right: 20px; left: auto; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-dim); z-index: 10;"></i>
                        </div>
                        @error('password')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 8px; display: block;">{{ $message }}</span>
                        @enderror
                        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-premium">Sign In</button>
                </form>

                <div class="auth-links">
                    Don't have an account? <a href="{{ route('register') }}">Create an account</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $("#loginForm").validate({
            rules: {
                email: { required: true, email: true },
                password: { required: true }
            },
            messages: {
                email: "Please enter a valid email address",
                password: "Please enter your password"
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.css({ 'color': '#f43f5e', 'font-size': '0.8rem', 'margin-top': '8px', 'display': 'block' });
                element.parent().parent().append(error);
            }
        });

        // Toggle Password visibility
        $('.toggle-password').on('click', function() {
            const input = $(this).siblings('input');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('bi-eye bi-eye-slash');
        });
    });
</script>
@endsection
