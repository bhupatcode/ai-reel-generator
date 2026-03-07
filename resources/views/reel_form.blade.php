<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI Reel Generator - Create stunning short-form video reels powered by artificial intelligence. Choose your topic, mood, and duration to generate engaging content.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Reel Generator — Create Stunning Reels Instantly</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #12121a;
            --bg-card: #16161f;
            --bg-card-hover: #1c1c28;
            --border-color: rgba(255, 255, 255, 0.06);
            --border-glow: rgba(139, 92, 246, 0.3);
            --text-primary: #f0f0f5;
            --text-secondary: #8b8b9e;
            --text-muted: #5a5a6e;
            --accent-purple: #8b5cf6;
            --accent-purple-dark: #6d3fd4;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --accent-cyan: #06b6d4;
            --gradient-primary: linear-gradient(135deg, #8b5cf6 0%, #ec4899 50%, #06b6d4 100%);
            --gradient-button: linear-gradient(135deg, #8b5cf6, #6d3fd4);
            --gradient-button-hover: linear-gradient(135deg, #9d6ff8, #7c4ee0);
            --shadow-glow: 0 0 40px rgba(139, 92, 246, 0.15);
            --shadow-card: 0 4px 24px rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* === Animated Background === */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(139, 92, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 12s ease-in-out infinite;
        }

        .bg-orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.25), transparent 70%);
            top: -150px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.2), transparent 70%);
            bottom: -100px;
            right: -80px;
            animation-delay: -4s;
        }

        .bg-orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.15), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -8s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 15px) scale(0.95); }
        }

        /* === Nav === */
        .navbar-custom {
            background: rgba(10, 10, 15, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s ease;
        }

        .brand-logo:hover {
            opacity: 0.85;
        }

        .brand-logo i {
            -webkit-text-fill-color: var(--accent-purple);
            font-size: 1.3rem;
        }

        .nav-badge {
            font-size: 0.65rem;
            font-weight: 600;
            background: var(--gradient-primary);
            padding: 2px 8px;
            border-radius: 20px;
            color: #fff;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* === Main Content === */
        .main-wrapper {
            position: relative;
            z-index: 1;
            padding: 3rem 0 5rem;
        }

        /* === Hero Header === */
        .hero-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .hero-header h1 .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* === Glass Card === */
        .glass-card {
            background: rgba(22, 22, 31, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-card);
            transition: border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .glass-card:hover {
            border-color: rgba(139, 92, 246, 0.15);
            box-shadow: var(--shadow-card), var(--shadow-glow);
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            flex-shrink: 0;
        }

        .card-header-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-header-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* === Form Styles === */
        .form-label-custom {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            letter-spacing: 0.3px;
        }

        .form-label-custom i {
            color: var(--accent-purple);
            font-size: 0.9rem;
        }

        .form-control-custom,
        .form-select-custom {
            background: var(--bg-secondary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            color: var(--text-primary) !important;
            padding: 0.85rem 1.15rem !important;
            font-size: 0.95rem !important;
            font-family: 'Inter', sans-serif !important;
            transition: all 0.3s ease !important;
            height: auto !important;
        }

        .form-control-custom::placeholder {
            color: var(--text-muted) !important;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: var(--accent-purple) !important;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15) !important;
            background: var(--bg-card) !important;
            outline: none !important;
        }

        .form-select-custom {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%238b8b9e' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            background-size: 14px 10px !important;
            padding-right: 2.5rem !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }

        .form-select-custom option {
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 0.5rem;
        }

        /* === Generate Button === */
        .btn-generate {
            background: var(--gradient-button);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            padding: 1rem 2rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            margin-top: 0.5rem;
            letter-spacing: 0.3px;
        }

        .btn-generate::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-button-hover);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.35);
        }

        .btn-generate:hover::before {
            opacity: 1;
        }

        .btn-generate:active {
            transform: translateY(0);
        }

        .btn-generate span,
        .btn-generate i {
            position: relative;
            z-index: 1;
        }

        .btn-generate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* === Loader === */
        .loader-overlay {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            gap: 1.5rem;
        }

        .loader-overlay.active {
            display: flex;
        }

        .loader-spinner {
            width: 64px;
            height: 64px;
            position: relative;
        }

        .loader-spinner::before,
        .loader-spinner::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .loader-spinner::before {
            border-top-color: var(--accent-purple);
            border-right-color: var(--accent-pink);
            animation: spinLoader 1s linear infinite;
        }

        .loader-spinner::after {
            inset: 6px;
            border-bottom-color: var(--accent-cyan);
            border-left-color: var(--accent-blue);
            animation: spinLoader 0.6s linear reverse infinite;
        }

        @keyframes spinLoader {
            to { transform: rotate(360deg); }
        }

        .loader-text {
            font-size: 1rem;
            color: var(--text-secondary);
            text-align: center;
        }

        .loader-text .dot-animation::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%  { content: ''; }
            25% { content: '.'; }
            50% { content: '..'; }
            75% { content: '...'; }
        }

        .loader-progress {
            width: 200px;
            height: 4px;
            background: var(--bg-secondary);
            border-radius: 4px;
            overflow: hidden;
        }

        .loader-progress-bar {
            height: 100%;
            width: 30%;
            background: var(--gradient-primary);
            border-radius: 4px;
            animation: progressSweep 2s ease-in-out infinite;
        }

        @keyframes progressSweep {
            0%   { width: 0%; margin-left: 0%; }
            50%  { width: 60%; margin-left: 20%; }
            100% { width: 0%; margin-left: 100%; }
        }

        /* === Result Section === */
        .result-section {
            display: none;
        }

        .result-section.active {
            display: block;
            animation: fadeSlideUp 0.6s ease;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Empty state placeholder */
        .empty-state-box {
            border-radius: 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 4rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: var(--text-muted);
            min-height: 300px;
        }

        .empty-state-box i {
            font-size: 3rem;
            opacity: 0.4;
        }

        .empty-state-box span {
            font-size: 0.9rem;
        }

        /* Result content block */
        .result-content-block {
            margin-bottom: 1.5rem;
        }

        .result-content-block:last-child {
            margin-bottom: 0;
        }

        .result-block-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.85rem;
        }

        .result-block-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .result-block-icon.purple { background: linear-gradient(135deg, #8b5cf6, #6d3fd4); }
        .result-block-icon.pink   { background: linear-gradient(135deg, #ec4899, #d63384); }
        .result-block-icon.cyan   { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .result-block-icon.amber  { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .result-block-title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-primary);
        }

        /* Numbered list items */
        .result-item {
            display: flex;
            gap: 0.85rem;
            padding: 0.85rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: border-color 0.3s ease, background 0.3s ease;
            animation: fadeSlideUp 0.4s ease backwards;
        }

        .result-item:hover {
            border-color: rgba(139, 92, 246, 0.2);
            background: var(--bg-card-hover);
        }

        .result-item-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(139, 92, 246, 0.15);
            color: var(--accent-purple);
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .result-item-text {
            font-size: 0.88rem;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* Music badge */
        .music-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(245, 158, 11, 0.02));
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            font-size: 0.92rem;
            color: #fbbf24;
            font-weight: 500;
            width: 100%;
        }

        .music-badge i {
            font-size: 1.2rem;
        }

        /* Result meta badges */
        .result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-color);
        }

        .result-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            padding: 0.4rem 0.85rem;
            font-size: 0.8rem;
            color: var(--accent-purple);
            font-weight: 500;
        }

        /* Action buttons */
        .result-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }

        .btn-action {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-action-primary {
            background: var(--gradient-button);
            border: none;
            color: #fff;
        }

        .btn-action-primary:hover {
            box-shadow: 0 6px 24px rgba(139, 92, 246, 0.3);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-action-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-action-outline:hover {
            border-color: var(--accent-purple);
            color: var(--accent-purple);
            background: rgba(139, 92, 246, 0.05);
        }

        /* Scrollable result panel */
        .result-scroll-area {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .result-scroll-area::-webkit-scrollbar {
            width: 4px;
        }

        .result-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .result-scroll-area::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.3);
            border-radius: 4px;
        }

        /* === Alert Styles === */
        .alert-custom {
            border-radius: 12px;
            border: 1px solid;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.9rem;
            animation: fadeSlideUp 0.4s ease;
        }

        .alert-custom-error {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .alert-custom-success {
            background: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .alert-custom i {
            font-size: 1.1rem;
            margin-top: 1px;
        }

        /* === Feature Pills === */
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: rgba(22, 22, 31, 0.8);
            border: 1px solid var(--border-color);
            font-size: 0.8rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .feature-pill:hover {
            border-color: rgba(139, 92, 246, 0.3);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .feature-pill i {
            color: var(--accent-purple);
        }

        /* === Footer === */
        .footer-custom {
            border-top: 1px solid var(--border-color);
            padding: 2rem 0;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .hero-header h1 {
                font-size: 2rem;
            }

            .glass-card {
                padding: 1.5rem;
                border-radius: 16px;
            }

            .main-wrapper {
                padding: 2rem 0 3rem;
            }

            .result-actions {
                flex-direction: column;
            }

            .feature-pills {
                gap: 0.5rem;
            }
        }

        /* === Smooth form group spacing === */
        .form-group-custom {
            margin-bottom: 1.5rem;
        }

        .form-group-custom:last-of-type {
            margin-bottom: 0;
        }

        /* === Input character counter === */
        .input-helper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.4rem;
        }

        .input-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .char-count {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="bg-grid"></div>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <!-- Navigation -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="/" class="brand-logo" id="brand-logo">
                    <i class="bi bi-camera-reels-fill"></i>
                    ReelForge AI
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="nav-badge">Beta</span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-primary);">
                            <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: var(--gradient-button) !important;">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset(Auth::user()->profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="bi bi-person-fill text-white"></i>
                                @endif
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-0" style="background: var(--bg-card); border-radius: 12px; margin-top: 10px; padding: 8px;">
                            <li>
                                <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 mb-1" style="border-radius: 8px;">
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-10"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius: 8px;">
                                        <i class="bi bi-box-arrow-right text-danger"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-wrapper">
        <div class="container">
            <!-- Hero Header -->
            <div class="hero-header">
                <h1>
                    Generate <span class="gradient-text">AI-Powered</span><br>
                    Video Reels
                </h1>
                <p class="hero-subtitle">
                    Transform your ideas into captivating short-form videos in seconds.
                    Just describe your vision and let AI do the rest.
                </p>
                <div class="feature-pills">
                    <span class="feature-pill"><i class="bi bi-lightning-charge-fill"></i> Instant Generation</span>
                    <span class="feature-pill"><i class="bi bi-palette-fill"></i> Custom Moods</span>
                    <span class="feature-pill"><i class="bi bi-clock-fill"></i> Multiple Durations</span>
                    <span class="feature-pill"><i class="bi bi-stars"></i> AI Powered</span>
                </div>
            </div>

            <div class="row justify-content-center g-4">
                <!-- Form Column -->
                <div class="col-lg-6">
                    <div class="glass-card" id="form-card">
                        <div class="card-header-custom">
                            <div class="card-header-icon">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <div>
                                <div class="card-header-title">Reel Configuration</div>
                                <div class="card-header-subtitle">Set up your reel parameters below</div>
                            </div>
                        </div>

                        <form id="reel-form" novalidate>
                            <!-- Topic Input -->
                            <div class="form-group-custom">
                                <label for="topic" class="form-label-custom">
                                    <i class="bi bi-chat-square-text"></i>
                                    Topic
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-custom"
                                    id="topic"
                                    name="topic"
                                    placeholder="e.g., Top 5 productivity tips for developers"
                                    maxlength="200"
                                    required
                                    autocomplete="off"
                                >
                                <div class="input-helper">
                                    <span class="input-hint">Describe what your reel should be about</span>
                                    <span class="char-count" id="topic-char-count">0 / 200</span>
                                </div>
                            </div>

                            <!-- Mood Dropdown -->
                            <div class="form-group-custom">
                                <label for="mood" class="form-label-custom">
                                    <i class="bi bi-emoji-smile"></i>
                                    Mood
                                </label>
                                <select class="form-select form-select-custom" id="mood" name="mood" required>
                                    <option value="" disabled selected>Select a mood...</option>
                                    <option value="energetic">🔥 Energetic</option>
                                    <option value="calm">🌿 Calm</option>
                                    <option value="professional">💼 Professional</option>
                                    <option value="funny">😂 Funny</option>
                                    <option value="inspirational">✨ Inspirational</option>
                                    <option value="dramatic">🎭 Dramatic</option>
                                    <option value="educational">📚 Educational</option>
                                    <option value="cinematic">🎬 Cinematic</option>
                                </select>
                            </div>

                            <!-- Duration Dropdown -->
                            <div class="form-group-custom">
                                <label for="duration" class="form-label-custom">
                                    <i class="bi bi-hourglass-split"></i>
                                    Duration
                                </label>
                                <select class="form-select form-select-custom" id="duration" name="duration" required>
                                    <option value="" disabled selected>Select duration...</option>
                                    <option value="15">⚡ 15 seconds — Quick Hook</option>
                                    <option value="30">🎯 30 seconds — Short & Sweet</option>
                                    <option value="60">📖 60 seconds — Story Format</option>
                                    <option value="90">🎬 90 seconds — Extended Cut</option>
                                </select>
                            </div>

                            <!-- Error Alert -->
                            <div id="form-alert" class="mt-3" style="display: none;"></div>

                            <!-- Generate Button -->
                            <div class="mt-4">
                                <button type="submit" class="btn-generate" id="btn-generate">
                                    <i class="bi bi-stars"></i>
                                    <span>Generate Reel</span>
                                </button>
                            </div>
                        </form>

                        <!-- Loader -->
                        <div class="loader-overlay" id="loader">
                            <div class="loader-spinner"></div>
                            <div class="loader-text">
                                Crafting your reel<span class="dot-animation"></span>
                            </div>
                            <div class="loader-progress">
                                <div class="loader-progress-bar"></div>
                            </div>
                            <div class="input-hint" style="margin-top: 0.5rem;">This may take a moment...</div>
                        </div>
                    </div>
                </div>

                <!-- Result / Preview Column -->
                <div class="col-lg-6">
                    <div class="glass-card">
                        <div class="card-header-custom">
                            <div class="card-header-icon">
                                <i class="bi bi-file-earmark-richtext"></i>
                            </div>
                            <div>
                                <div class="card-header-title">Generated Reel</div>
                                <div class="card-header-subtitle">AI-generated script, scenes & more</div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="empty-state" class="empty-state-box">
                            <i class="bi bi-stars"></i>
                            <span>Your AI reel content will appear here</span>
                        </div>

                        <!-- Result State -->
                        <div id="result-section" class="result-section">
                            <div class="result-scroll-area">
                                <!-- Script -->
                                <div class="result-content-block">
                                    <div class="result-block-header">
                                        <div class="result-block-icon purple"><i class="bi bi-journal-text"></i></div>
                                        <div class="result-block-title">Script</div>
                                    </div>
                                    <div id="result-script"></div>
                                </div>

                                <!-- Scenes -->
                                <div class="result-content-block">
                                    <div class="result-block-header">
                                        <div class="result-block-icon pink"><i class="bi bi-image"></i></div>
                                        <div class="result-block-title">Scene Descriptions</div>
                                    </div>
                                    <div id="result-scenes"></div>
                                </div>

                                <!-- Captions -->
                                <div class="result-content-block">
                                    <div class="result-block-header">
                                        <div class="result-block-icon cyan"><i class="bi bi-chat-left-quote"></i></div>
                                        <div class="result-block-title">On-Screen Captions</div>
                                    </div>
                                    <div id="result-captions"></div>
                                </div>

                                <!-- Music -->
                                <div class="result-content-block">
                                    <div class="result-block-header">
                                        <div class="result-block-icon amber"><i class="bi bi-music-note-beamed"></i></div>
                                        <div class="result-block-title">Background Music</div>
                                    </div>
                                    <div id="result-music"></div>
                                </div>
                            </div>

                            <div class="result-meta" id="result-meta"></div>

                            <div class="result-actions">
                                <button class="btn-action btn-action-primary" id="btn-copy-json">
                                    <i class="bi bi-clipboard"></i>
                                    Copy JSON
                                </button>
                                <button class="btn-action btn-action-outline" id="btn-new-reel">
                                    <i class="bi bi-arrow-repeat"></i>
                                    New Reel
                                </button>
                            </div>

                            <!-- Paste existing JSON to generate video -->
                            <div class="card mt-4">
                                <div class="card-header">Create Video from JSON</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="reelJson" class="form-label">Reel JSON</label>
                                        <textarea id="reelJson" class="form-control" rows="6"
                                                  placeholder='{"script":[], "scenes":[], "captions":[], "music":"", "duration":15}'></textarea>
                                    </div>
                                    <button id="generateVideoBtn" class="btn btn-success">
                                        <i class="bi bi-film"></i> Generate Video
                                    </button>
                                    <span id="jsonLoader" class="spinner-border text-success ms-2" style="display:none;"></span>
                                    <div id="generatedVideo" class="mt-3"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p>© {{ date('Y') }} ReelForge AI — Powered by artificial intelligence</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
    $(function () {

        // ── Character counter ──
        $('#topic').on('input', function () {
            const len = $(this).val().length;
            $('#topic-char-count').text(len + ' / 200');
        });

        // ── UI helpers ──
        function showLoader() {
            $('#reel-form').fadeOut(200, function () {
                $('#loader').addClass('active').hide().fadeIn(300);
            });
            $('#btn-generate').prop('disabled', true);
        }

        function hideLoader() {
            $('#loader').fadeOut(200, function () {
                $(this).removeClass('active');
                $('#reel-form').fadeIn(300);
            });
            $('#btn-generate').prop('disabled', false);
        }

        function showAlert(type, message) {
            const icon = type === 'error'
                ? '<i class="bi bi-exclamation-triangle-fill"></i>'
                : '<i class="bi bi-check-circle-fill"></i>';
            const cls = type === 'error' ? 'alert-custom-error' : 'alert-custom-success';

            $('#form-alert').html(
                '<div class="alert-custom ' + cls + '">' +
                    icon +
                    '<div>' + message + '</div>' +
                '</div>'
            ).fadeIn(200);
        }

        function hideAlert() {
            $('#form-alert').fadeOut(200);
        }

        // Store latest result for copy
        let latestResultJson = null;

        function buildItemsHtml(items) {
            let html = '';
            items.forEach(function (item, idx) {
                const delay = (idx * 0.08).toFixed(2);
                const safeText = $('<span>').text(item).html();
                html += '<div class="result-item" style="animation-delay:' + delay + 's">' +
                            '<div class="result-item-num">' + (idx + 1) + '</div>' +
                            '<div class="result-item-text">' + safeText + '</div>' +
                        '</div>';
            });
            return html;
        }

        function showResult(data, meta) {
            latestResultJson = data;

            // Script lines
            if (data.script && data.script.length) {
                $('#result-script').html(buildItemsHtml(data.script));
            }

            // Scene descriptions
            if (data.scenes && data.scenes.length) {
                $('#result-scenes').html(buildItemsHtml(data.scenes));
            }

            // Captions
            if (data.captions && data.captions.length) {
                $('#result-captions').html(buildItemsHtml(data.captions));
            }

            // Music
            if (data.music) {
                const safeMusicText = $('<span>').text(data.music).html();
                $('#result-music').html(
                    '<div class="music-badge"><i class="bi bi-music-note-beamed"></i> ' + safeMusicText + '</div>'
                );
            }

            // Meta badges
            let badges = '';
            if (meta.topic)    badges += '<span class="result-meta-badge"><i class="bi bi-chat-square-text"></i> ' + $('<span>').text(meta.topic).html() + '</span>';
            if (meta.mood)     badges += '<span class="result-meta-badge"><i class="bi bi-emoji-smile"></i> ' + $('<span>').text(meta.mood).html() + '</span>';
            if (meta.duration) badges += '<span class="result-meta-badge"><i class="bi bi-clock"></i> ' + meta.duration + 's</span>';
            $('#result-meta').html(badges);

            // Toggle visibility
            $('#empty-state').hide();
            $('#result-section').addClass('active');
        }

        function resetResult() {
            $('#result-section').removeClass('active');
            $('#empty-state').show();
            $('#result-script, #result-scenes, #result-captions, #result-music, #result-meta').empty();
            latestResultJson = null;
        }

        // ── Form validation ──
        function validateForm() {
            let valid = true;

            if (!$('#topic').val().trim()) {
                valid = false;
            }
            if (!$('#mood').val()) {
                valid = false;
            }
            if (!$('#duration').val()) {
                valid = false;
            }

            return valid;
        }

        // ── Form Submission ──
        $('#reel-form').on('submit', function (e) {
            e.preventDefault();
            hideAlert();

            if (!validateForm()) {
                showAlert('error', 'Please fill in all fields before generating.');
                return;
            }

            showLoader();

            $.ajax({
                url: '/generate-reel',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    topic:    $('#topic').val().trim(),
                    mood:     $('#mood').val(),
                    duration: $('#duration').val()
                },
                dataType: 'json',
                success: function (response) {
                    hideLoader();

                    if (response.success && response.data) {
                        showResult(response.data, response.meta || {
                            topic:    $('#topic').val().trim(),
                            mood:     $('#mood').val(),
                            duration: $('#duration').val()
                        });
                        showAlert('success', response.message || 'Your reel has been generated successfully!');
                    } else {
                        showAlert('error', response.message || 'Something went wrong. Please try again.');
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    let msg = 'An unexpected error occurred. Please try again.';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            msg = Object.values(errors).flat().join('<br>');
                        }
                    }

                    showAlert('error', msg);
                }
            });
        });

        // ── Copy JSON ──
        $('#btn-copy-json').on('click', function () {
            if (!latestResultJson) return;
            const jsonStr = JSON.stringify(latestResultJson, null, 2);
            navigator.clipboard.writeText(jsonStr).then(function () {
                const $btn = $('#btn-copy-json');
                $btn.html('<i class="bi bi-check-lg"></i> Copied!');
                setTimeout(function () {
                    $btn.html('<i class="bi bi-clipboard"></i> Copy JSON');
                }, 2000);
            });
        });

        // ── New Reel Button ──
        $('#btn-new-reel').on('click', function () {
            resetResult();
            $('#reel-form')[0].reset();
            $('#topic-char-count').text('0 / 200');
            hideAlert();
        });

        // ── Generate Video From JSON (with Replicate async polling) ──
        $('#generateVideoBtn').on('click', function () {
            var raw = $('#reelJson').val().trim();
            if (!raw) {
                alert('Please paste the reel JSON first.');
                return;
            }

            var payload;
            try {
                payload = JSON.parse(raw);
            } catch (e) {
                alert('Invalid JSON: ' + e.message);
                return;
            }

            payload.duration = payload.duration || 15;

            $('#jsonLoader').show();
            $('#generatedVideo').empty();

            // Step 1: Submit video generation request
            $.ajax({
                url: '/api/reels/create-video',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify(payload),
                success: function(res) {
                    if (res.success && res.prediction_id) {
                        console.log('Video generation started. Prediction ID:', res.prediction_id);
                        // Step 2: Start polling for status
                        pollVideoStatus(res.prediction_id);
                    } else {
                        $('#jsonLoader').hide();
                        $('#generatedVideo').html('<div class="alert alert-danger">Failed to start video generation: ' + (res.error || 'Unknown error') + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#jsonLoader').hide();
                    var errorMsg = 'Error starting video generation';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    $('#generatedVideo').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        });

        // ── Poll video generation status every 5 seconds ──
        let pollInterval = null;
        let pollAttempts = 0;
        const maxPollAttempts = 240; // 20 minutes (240 * 5 seconds)

        function pollVideoStatus(predictionId) {
            pollAttempts = 0;

            function checkStatus() {
                pollAttempts++;

                if (pollAttempts > maxPollAttempts) {
                    clearInterval(pollInterval);
                    $('#jsonLoader').hide();
                    $('#generatedVideo').html('<div class="alert alert-warning">Video generation timed out (20+ minutes). Please check back later.</div>');
                    return;
                }

                // Update loading text with poll count
                var displayMinutes = Math.floor((pollAttempts * 5) / 60);
                var displaySeconds = (pollAttempts * 5) % 60;
                var timeStr = (displayMinutes > 0 ? displayMinutes + 'm ' : '') + displaySeconds + 's';

                $('#jsonLoader').html(
                    '<div style="text-align: center;"><div class="spinner-border text-info mb-3" role="status"></div>' +
                    '<div class="loader-text">Generating video<span class="dot-animation"></span></div>' +
                    '<small style="color: var(--text-secondary);">Elapsed: ' + timeStr + '</small></div>'
                );

                $.ajax({
                    url: '/api/reels/video-status/' + predictionId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        console.log('Status check:', res);

                        if (res.status === 'completed' && res.video_url) {
                            clearInterval(pollInterval);
                            $('#jsonLoader').hide();

                            // Display the generated video
                            var html = '<video width="100%" controls autoplay muted style="border-radius: 12px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);">' +
                                       '<source src="' + res.video_url + '" type="video/mp4">' +
                                       'Your browser does not support the video tag.' +
                                       '</video>';
                            $('#generatedVideo').html(html);

                            // Show success alert
                            showAlert('success', 'Video generated successfully! It took ' + timeStr + '.');
                        }
                        else if (res.status === 'failed' || res.error) {
                            clearInterval(pollInterval);
                            $('#jsonLoader').hide();
                            $('#generatedVideo').html('<div class="alert alert-danger">Video generation failed: ' + (res.error || 'Unknown error') + '</div>');
                        }
                        // If still processing, next poll will happen automatically
                    },
                    error: function(xhr) {
                        if (xhr.status === 500) {
                            clearInterval(pollInterval);
                            $('#jsonLoader').hide();
                            var errorMsg = 'Error checking status';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                            $('#generatedVideo').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        }
                        // Otherwise continue polling
                    }
                });
            }

            // First check immediately
            checkStatus();

            // Then poll every 5 seconds
            pollInterval = setInterval(checkStatus, 5000);
        }
    </script>
</body>
</html>
