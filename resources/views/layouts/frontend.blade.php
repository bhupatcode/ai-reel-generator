<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AI Reel Generator')) - Premium AI Content</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #f43f5e;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-light: #f8fafc;
            --text-dim: #94a3b8;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(244, 63, 94, 0.1) 0%, transparent 40%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar */
        nav {
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dim);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--text-light);
        }

        .btn {
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--glass);
            border-color: var(--text-light);
        }

        /* Footer */
        footer {
            padding: 80px 0 40px;
            border-top: 1px solid var(--glass-border);
            margin-top: 100px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo {
            margin-bottom: 20px;
        }

        .footer-p {
            color: var(--text-dim);
            font-size: 0.9rem;
        }

        .footer-col h4 {
            margin-bottom: 20px;
            font-weight: 700;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: var(--text-dim);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-col ul li a:hover {
            color: var(--text-light);
        }

        .copyright {
            padding-top: 40px;
            border-top: 1px solid var(--glass-border);
            text-align: center;
            color: var(--text-dim);
            font-size: 0.85rem;
        }

        /* Hero & Sections */
        section {
            padding: 120px 0 60px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .section-header p {
            color: var(--text-dim);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.2s; }
        .delay-3 { transition-delay: 0.3s; }
        .delay-4 { transition-delay: 0.4s; }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none; /* Mobile menu logic omitted for brevity, focusing on premium look */
            }
            .section-header h2 {
                font-size: 2rem;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <nav>
        <div class="container nav-content">
            <a href="{{ url('/') }}" class="logo">ReelGenius AI</a>
            <div class="nav-links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/about') }}">About</a>
                <a href="{{ url('/reviews') }}">Reviews</a>
                <a href="{{ url('/contact') }}">Contact</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo footer-logo">ReelGenius AI</div>
                    <p class="footer-p">Transform your ideas into stunning AI-generated reels in seconds. The future of content creation is here.</p>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ url('/about') }}">About Us</a></li>
                        <li><a href="{{ url('/reviews') }}">Our Reviews</a></li>
                        <li><a href="{{ url('/privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                        <li><a href="{{ url('/faq') }}">FAQ</a></li>
                        <li><a href="{{ url('/terms') }}">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Connect</h4>
                    <ul>
                        <li><a href="#">X (Twitter)</a></li>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} ReelGenius AI. All rights reserved.
            </div>
        </div>
    </footer>

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Toast Utility
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: 'rgba(15, 23, 42, 0.9)',
            color: '#f8fafc',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.showToast = function(icon, title) {
            Toast.fire({
                icon: icon,
                title: title
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
