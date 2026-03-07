<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - AI Reel Generator</title>
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
        <h3 class="text-center mb-4">Join Us</h3>

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn-auth">Sign Up</button>
        </form>


        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init();

        $(document).ready(function() {
            // Setup CSRF for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Custom method for bad words (example check)
            const badWords = ['badword1', 'badword2', 'spam', 'admin', 'root'];
            $.validator.addMethod("nobadwords", function(value, element) {
                const lowerValue = value.toLowerCase();
                return !badWords.some(word => lowerValue.includes(word));
            }, "Please avoid inappropriate language in your name.");

            // jQuery Validation
            $("#registerForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        nobadwords: true
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: "{{ route('check.email') }}",
                            type: "post",
                            data: {
                                email: function() {
                                    return $("#email").val();
                                }
                            },
                            dataFilter: function(data) {
                                var json = JSON.parse(data);
                                if (json.exists === true) {
                                    return JSON.stringify("This email is already registered.");
                                } else {
                                    return JSON.stringify(true);
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    name: {
                        required: "Please enter your name",
                        minlength: "Name must be at least 2 characters"
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 8 characters long"
                    },
                    password_confirmation: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    const btn = $(form).find('button[type="submit"]');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
