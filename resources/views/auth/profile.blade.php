<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AI Reel Generator</title>
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

        .navbar-custom {
            background: rgba(10, 10, 15, 0.8);
            backdrop-filter: blur(20px);
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
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .glass-card {
            background: rgba(22, 22, 31, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 600px;
            margin: 40px auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        .profile-img-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--accent-purple);
            background: var(--bg-secondary);
        }

        .img-upload-label {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--gradient-button);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
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
    </style>
</head>
<body>
    <div class="bg-grid"></div>

    <nav class="navbar-custom">
        <div class="container">
            <a href="/" class="brand-logo">
                <i class="bi bi-camera-reels-fill"></i>
                ReelForge AI
            </a>
        </div>
    </nav>
    
    <div class="glass-card" data-aos="fade-up">
        <h3 class="text-center mb-4">Your Profile</h3>

        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-20 text-success mb-4 rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-img-container">
                <img src="{{ $user->profile_image ? asset($user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8b5cf6&color=fff&size=128' }}" 
                     class="profile-img" id="profilePreview">
                <label for="profile_image" class="img-upload-label">
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                <small class="text-muted">Email cannot be changed.</small>
            </div>

            <button type="submit" class="btn-auth">Update Profile</button>
            <a href="/" class="btn btn-link w-100 mt-2 text-decoration-none" style="color: var(--text-secondary);">Back to Generator</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        $('#profile_image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
