@extends('layouts.frontend')

@section('title', 'Profile')

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
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin: 0 auto;
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
            display: block;
            margin-bottom: 0.5rem;
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
            background: var(--bg-card);
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
            outline: none;
        }
        
        .form-control:disabled {
            background: rgba(15, 23, 42, 0.5);
            opacity: 0.7;
            cursor: not-allowed;
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
        }
    </style>
@endsection

@section('content')
<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 120px 20px 60px; position: relative;">
    <div class="bg-grid"></div>
    
    <div class="glass-card" data-aos="fade-up">
        <h3 class="text-center mb-4" style="text-align: center; margin-bottom: 1.5rem;">Your Profile</h3>

        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-20 text-success mb-4 rounded-3" style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
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
                <input type="file" name="profile_image" id="profile_image" style="display: none;" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                <small class="text-muted" style="color: var(--text-secondary); font-size: 0.8em; margin-top: 4px; display: block;">Email cannot be changed.</small>
            </div>

            <button type="submit" class="btn-auth">Update Profile</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection
