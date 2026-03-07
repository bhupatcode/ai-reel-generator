<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AI Reel Generator</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- AOS Animate on Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #12121a;
            --bg-card: #16161f;
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #f0f0f5;
            --text-secondary: #8b8b9e;
            --accent-purple: #8b5cf6;
            --gradient-primary: linear-gradient(135deg, #8b5cf6 0%, #ec4899 50%, #06b6d4 100%);
            --gradient-button: linear-gradient(135deg, #8b5cf6, #6d3fd4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(139, 92, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: -1;
        }

        .glass-card {
            background: rgba(22, 22, 31, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
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
            display: block;
            text-decoration: none;
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 0.8rem 1.2rem;
        }

        .form-control:focus {
            background: var(--bg-card);
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            color: var(--text-primary);
        }

        .btn-auth {
            background: var(--gradient-button);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            width: 100%;
            margin-top: 1rem;
            transition: transform 0.2s;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    
    <div class="glass-card" data-aos="fade-up">
        <a href="/" class="brand-logo"><i class="bi bi-film"></i> AI Reels</a>
        <h3 class="text-center mb-2">Reset Password</h3>
        <p class="text-center text-secondary mb-4 small">Enter your email address and we'll send you a link to reset your password.</p>

        @if (session('status'))
            <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-20 text-success rounded-3 mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-20 text-danger rounded-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="resetForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-auth">Send Reset Link</button>
        </form>

        <div class="auth-footer">
            Remembered? <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        $(document).ready(function() {
            $("#resetForm").validate({
                rules: { email: { required: true, email: true } },
                messages: { email: { required: "Please enter your email", email: "Invalid email address" } },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
                },
                highlight: function (element) { $(element).addClass('is-invalid'); },
                unhighlight: function (element) { $(element).removeClass('is-invalid'); },
                submitHandler: function(form) {
                    const btn = $(form).find('button[type="submit"]');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
