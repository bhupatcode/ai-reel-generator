@extends('layouts.frontend')

@section('title', 'Set New Password')

@section('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-purple: #6366f1;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #f43f5e 50%, #06b6d4 100%);
            --gradient-button: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(139, 92, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: -1;
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin: 0 auto;
        }

        .brand-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 2rem;
            display: inline-block;
            text-decoration: none;
        }

        .header-action {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 2rem;
        }

        .back-btn {
            position: absolute;
            left: 0;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: var(--text-primary);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            margin-top: 1rem;
        }

        .form-control {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 0.8rem 1.2rem;
            width: 100%;
        }

        .form-control:focus {
            background: var(--bg-secondary);
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
            outline: none;
        }

        .btn-auth {
            background: var(--gradient-button);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            width: 100%;
            margin-top: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 2rem 1.5rem;
            }
            .brand-logo {
                font-size: 1.6rem;
            }
        }
    </style>
@endsection

@section('content')
<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 120px 20px 60px; position: relative;">
    <div class="bg-grid"></div>
    
    <div class="glass-card" data-aos="fade-up">
        <div class="header-action">
            <a href="javascript:history.length > 1 ? history.back() : window.location.href='{{ route('login') }}'" class="back-btn"><i class="bi bi-arrow-left-short"></i></a>
            <a href="/" class="brand-logo mb-0"><i class="bi bi-film"></i> AI Reels</a>
        </div>
        <h3 class="text-center mb-4" style="text-align: center; margin-bottom: 1.5rem;">Set New Password</h3>

        @if (session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-20 text-danger rounded-3 mb-4" style="background: rgba(244, 63, 94, 0.1); border: 1px solid #f43f5e; color: #f43f5e; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="newPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" required readonly>
                @error('email')
                    <span class="invalid-feedback text-danger small" style="color: #f43f5e; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
                @error('password')
                    <span class="invalid-feedback text-danger small" style="color: #f43f5e; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-auth">Reset Password</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        $(document).ready(function() {
            $("#newPasswordForm").validate({
                rules: {
                    password: { required: true, minlength: 8 },
                    password_confirmation: { required: true, equalTo: "#password" }
                },
                messages: {
                    password: { required: "Enter new password", minlength: "Min 8 characters" },
                    password_confirmation: { required: "Confirm your password", equalTo: "Passwords don't match" }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.css({color: '#f43f5e', fontSize: '0.8rem', marginTop: '4px', display: 'block'});
                    element.parent().append(error);
                },
                highlight: function (element) { $(element).css('border-color', '#f43f5e'); },
                unhighlight: function (element) { $(element).css('border-color', 'var(--border-color)'); },
                submitHandler: function(form) {
                    const btn = $(form).find('button[type="submit"]');
                    btn.prop('disabled', true).text('Processing...');
                    form.submit();
                }
            });
        });
    </script>
@endsection
