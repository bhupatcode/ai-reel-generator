@extends('layouts.frontend')

@section('title', 'AI Reel Generator')

@section('styles')
    <style>
        :root {
            --neon-primary: #6366f1;
            --neon-accent: #f43f5e;
            --glass-deep: rgba(15, 23, 42, 0.6);
            --glow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .dashboard-container {
            padding-top: 140px;
            padding-bottom: 100px;
            position: relative;
            z-index: 1;
        }

        /* Mesh Gradient Background */
        .mesh-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: var(--bg-dark);
            overflow: hidden;
        }

        .mesh-sphere {
            position: absolute;
            width: 600px;
            height: 600px;
            filter: blur(120px);
            opacity: 0.4;
            border-radius: 50%;
            animation: move 20s infinite alternate;
        }

        .sphere-1 { background: var(--primary); top: -100px; left: -100px; }
        .sphere-2 { background: var(--accent); bottom: -100px; right: -100px; animation-delay: -5s; }
        .sphere-3 { background: #8b5cf6; top: 40%; left: 50%; animation: move 25s infinite alternate-reverse; }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 100px) scale(1.2); }
        }

        .generator-card {
            background: var(--glass-deep);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            padding: 50px;
            box-shadow: 0 30px 100px rgba(0,0,0,0.6);
            position: relative;
            overflow: hidden;
        }

        .generator-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--accent), transparent);
            animation: scan 3s linear infinite;
        }

        @keyframes scan {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .generator-header {
            margin-bottom: 50px;
            text-align: center;
        }

        .generator-header h2 {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 40px;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-grid.results-active {
            grid-template-columns: 1fr;
        }

        .input-section.hidden {
            display: none;
        }

        .result-side.full-width {
            grid-column: 1 / -1;
        }

        /* Improved Dropdown Styling */
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 20px center;
            padding-right: 45px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 16px 20px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            color: white;
            outline: none;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-select option {
            background-color: var(--bg-dark);
            color: white;
        }

        /* Specifically highlighting the topic input */
        #topic {
            background: rgba(99, 102, 241, 0.05);
            border-color: rgba(99, 102, 241, 0.2);
        }

        #topic:focus {
            background: rgba(99, 102, 241, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.3);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: var(--glow);
            transform: translateY(-2px);
        }

        /* Results Panel */
        .result-panel {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 35px;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.5s ease;
        }

        .result-panel.has-content {
            background: rgba(15, 23, 42, 0.4);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Futuristic Loader */
        .loader-container {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 25px;
            height: 300px;
        }

        .ai-core {
            width: 100px;
            height: 100px;
            position: relative;
        }

        .core-inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            box-shadow: var(--glow);
            animation: pulse 2s infinite;
        }

        .core-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px solid var(--glass-border);
            border-radius: 38% 62% 63% 37% / 41% 44% 56% 59%;
            animation: rotate 3s linear infinite;
        }

        .ring-2 {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border-color: var(--accent);
            animation-duration: 4s;
            animation-direction: reverse;
        }

        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        /* Result Items Staggered Reveal */
        .result-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateX(30px);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .result-item.show {
            opacity: 1;
            transform: translateX(0);
        }

        .result-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .btn-generate {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            padding: 20px;
            border-radius: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
        }

        .btn-generate::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        @media (max-width: 992px) {
            .form-grid { grid-template-columns: 1fr; }
            .generator-card { padding: 30px; }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding-top: 100px;
                padding-bottom: 60px;
            }
            .generator-header h2 {
                font-size: 2rem;
            }
            .generator-card {
                padding: 20px;
                border-radius: 20px;
            }
            .form-grid { 
                gap: 20px; 
            }
        }
        /* Vertical Reel Player */
        .reel-player-container {
            width: 360px;
            height: 640px;
            background: #000;
            border-radius: 30px;
            overflow: hidden;
            position: relative;
            margin: 0 auto 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            border: 1px solid var(--glass-border);
        }

        .reel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reel-slide.active {
            opacity: 1;
        }

        .reel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            transition: transform 4s linear;
        }

        .reel-slide.active img {
            transform: scale(1.15);
        }

        .caption-overlay {
            position: absolute;
            bottom: 60px;
            left: 20px;
            right: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
            font-weight: 800;
            text-shadow: 0 4px 15px rgba(0,0,0,0.9), 0 0 10px rgba(0,0,0,0.5);
            z-index: 10;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            line-height: 1.2;
        }

        .reel-slide.active .caption-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        .progress-bar-container {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
            z-index: 20;
        }

        .progress-segment {
            height: 3px;
            background: rgba(255,255,255,0.3);
            flex: 1;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: white;
            width: 0%;
        }

        .reel-controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="mesh-bg">
        <div class="mesh-sphere sphere-1"></div>
        <div class="mesh-sphere sphere-2"></div>
        <div class="mesh-sphere sphere-3"></div>
    </div>

    <div class="container dashboard-container reveal">
        <div class="generator-card">
            <div class="generator-header">
                <h2>AI Reel Generator</h2>
                <p>Configure your reel and let Gemini AI do the magic.</p>
            </div>

            <div class="form-grid">
                {{-- Input Section --}}
                <div class="input-section">
                    <form id="reelForm">
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="topic">What's your reel about?</label>
                            <input type="text" id="topic" name="topic" class="form-input" placeholder="e.g. 5 morning habits for success" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                            <div class="form-group">
                                <label for="mood">Mood / Style</label>
                                <select id="mood" name="mood" class="form-select">
                                    <option value="Inspirational">Inspirational</option>
                                    <option value="Educational">Educational</option>
                                    <option value="Funny">Funny</option>
                                    <option value="Cinematic">Cinematic</option>
                                    <option value="Energetic">Energetic</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language">Language</label>
                                <select id="language" name="language" class="form-select">
                                    <option value="English" selected>English</option>
                                    <option value="Hindi">Hindi (हिंदी)</option>
                                    <option value="Spanish">Spanish (Español)</option>
                                    <option value="French">French (Français)</option>
                                    <option value="German">German (Deutsch)</option>
                                    <option value="Bhojpuri">Bhojpuri (भोजपुरी)</option>
                                    <option value="Gujarati">Gujarati (ગુજરાતી)</option>
                                    <option value="Marathi">Marathi (मराठी)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="duration">Duration</label>
                            <select id="duration" name="duration" class="form-select">
                                <option value="15">15 Seconds</option>
                                <option value="30">30 Seconds</option>
                                <option value="60">60 Seconds</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="instructions">Additional Instructions (Optional)</label>
                            <textarea id="instructions" name="instructions" class="form-textarea" placeholder="Any specific requirements..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-generate" id="generateBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                            Generate Content
                        </button>
                    </form>
                </div>

                {{-- Result Side --}}
                <div class="result-side">
                    <div id="resultPanel" class="result-panel">
                        {{-- Default State --}}
                        <div id="emptyState">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px; opacity: 0.3;"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m3 9 18-1"/><path d="m3 15 18-1"/></svg>
                            <p>Generate magic to see your reel details here.</p>
                        </div>

                        {{-- Futuristic Loading State --}}
                        <div id="loader" class="loader-container">
                            <div class="ai-core">
                                <div class="core-ring"></div>
                                <div class="core-ring ring-2"></div>
                                <div class="core-inner"></div>
                            </div>
                            <p style="font-weight: 700; color: var(--primary); letter-spacing: 1px;">AI IS SYNTHESIZING...</p>
                        </div>

                        {{-- Content State --}}
                        <div id="contentState" style="display: none; width: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                                <h4 style="color: var(--primary); margin: 0;">Generation Ready!</h4>
                                <button onclick="backToForm()" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">
                                    <i class="bi bi-arrow-left"></i> Back to Form
                                </button>
                            </div>
                            
                            <div class="result-item" id="itemScript">
                                <span class="result-label"><i class="bi bi-file-earmark-text"></i> Script</span>
                                <div id="resScript" style="font-size: 0.95rem; line-height: 1.6;"></div>
                            </div>

                            <div class="result-item" id="itemScenes">
                                <span class="result-label"><i class="bi bi-camera-reels"></i> Visual Scenes</span>
                                <div id="resScenes" style="font-size: 0.9rem;"></div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="result-item" id="itemCaptions">
                                    <span class="result-label"><i class="bi bi-chat-quote"></i> Captions</span>
                                    <div id="resCaptions" style="font-size: 0.85rem;"></div>
                                </div>
                                <div class="result-item" id="itemMusic">
                                    <span class="result-label"><i class="bi bi-music-note-beamed"></i> Music</span>
                                    <div id="resMusic" style="font-size: 0.85rem;"></div>
                                </div>
                            </div>

                            <button onclick="synthesizeVideo()" class="btn btn-primary btn-generate" style="width: 100%; margin-top: 20px; font-size: 1.1rem;">
                                <i class="bi bi-camera-reels-fill"></i> SYNTHESIZE AI VIDEO
                            </button>

                            <button onclick="copyJson()" class="btn btn-outline" style="width: 100%; margin-top: 10px; padding: 12px; border-color: var(--glass-border); color: var(--text-secondary);">
                                <i class="bi bi-braces"></i> Copy Raw JSON
                            </button>
                        </div>
                        
                        {{-- Video Player State (Browser Based) --}}
                        <div id="videoPlayerState" style="display: none; width: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h4 style="color: var(--primary); margin: 0;"><i class="bi bi-magic"></i> AI Reel Ready</h4>
                                <button onclick="backToForm()" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">
                                    Start Over
                                </button>
                            </div>
                            
                            <div class="reel-player-container" id="reelPlayer">
                                {{-- Slides will be injected here --}}
                                <div class="progress-bar-container" id="progressBar"></div>
                            </div>

                            <div class="reel-controls">
                                <button onclick="restartReel()" class="btn btn-outline" style="flex: 1; border-color: var(--glass-border);">
                                    <i class="bi bi-arrow-counterclockwise"></i> Replay
                                </button>
                                <button id="downloadReelBtn" onclick="downloadReel()" class="btn btn-primary" style="flex: 2;">
                                    <i class="bi bi-download"></i> DOWNLOAD REEL
                                </button>
                            </div>

                            <canvas id="recorderCanvas" width="720" height="1280" style="display: none;"></canvas>
                            <audio id="bgMusic" loop crossorigin="anonymous"></audio>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
    let currentResult = null;
    let currentSlide = 0;
    let slideInterval = null;
    let mediaSource = null;
    let audioCtx = null;
    const SCENE_DURATION = 3000; // 3 seconds per scene

    async function ensureAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        if (audioCtx.state === 'suspended') {
            await audioCtx.resume();
        }

        const music = document.getElementById('bgMusic');
        if (music && music.src && !mediaSource) {
            try {
                mediaSource = audioCtx.createMediaElementSource(music);
                mediaSource.connect(audioCtx.destination);
            } catch (e) {
                console.warn("MediaElementSource already created or failed", e);
            }
        }
    }

    document.getElementById('reelForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('generateBtn');
        const loader = document.getElementById('loader');
        const emptyState = document.getElementById('emptyState');
        const contentState = document.getElementById('contentState');
        const resultPanel = document.getElementById('resultPanel');

        btn.disabled = true;
        btn.innerHTML = 'Generating Content & Images...';
        
        emptyState.style.display = 'none';
        contentState.style.display = 'none';
        loader.style.display = 'flex';
        resultPanel.classList.remove('has-content');

        try {
            const formData = new FormData(e.target);
            const response = await fetch("{{ route('api.reels.generate') }}", {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                currentResult = data.data;
                showToast('success', 'AI Generation Complete!');
                
                // Set text
                document.getElementById('resScript').innerText = data.data.script.join(' ');
                document.getElementById('resScenes').innerText = data.data.scenes.join(' | ');
                document.getElementById('resCaptions').innerText = data.data.captions.join(' | ');
                document.getElementById('resMusic').innerText = data.data.music;

                // UI Cleanup
                loader.style.display = 'none';
                contentState.style.display = 'block';
                resultPanel.classList.add('has-content');
                
                // Switch to Full Page View
                document.querySelector('.input-section').classList.add('hidden');
                document.querySelector('.form-grid').classList.add('results-active');

                // Staggered Reveal
                const items = ['itemScript', 'itemScenes', 'itemCaptions', 'itemMusic'];
                for (let i = 0; i < items.length; i++) {
                    await new Promise(r => setTimeout(r, 200));
                    document.getElementById(items[i]).classList.add('show');
                }

                // Celebrate!
                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 },
                        colors: ['#6366f1', '#f43f5e', '#8b5cf6']
                    });
                }
            } else {
                showToast('error', 'Generation failed: ' + (data.message || 'Unknown error'));
                loader.style.display = 'none';
                emptyState.style.display = 'flex';
            }
        } catch (error) {
            console.error(error);
            showToast('error', 'An error occurred. Please try again.');
            loader.style.display = 'none';
            emptyState.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg> Generate Content';
        }
    });

    function backToForm() {
        if (slideInterval) clearInterval(slideInterval);
        const music = document.getElementById('bgMusic');
        music.pause();
        
        document.querySelector('.input-section').classList.remove('hidden');
        document.querySelector('.form-grid').classList.remove('results-active');
        document.getElementById('contentState').style.display = 'none';
        document.getElementById('videoPlayerState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'flex';
        document.getElementById('loader').style.display = 'none';
        document.getElementById('resultPanel').classList.remove('has-content');
        
        const items = ['itemScript', 'itemScenes', 'itemCaptions', 'itemMusic'];
        items.forEach(id => document.getElementById(id).classList.remove('show'));
    }

    async function synthesizeVideo() {
        if (!currentResult) return;
        
        const btn = document.querySelector('button[onclick="synthesizeVideo()"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> SYNTHESIZING...';
        }

        try {
            await ensureAudioContext();
            initSlideshow();
            
            document.getElementById('contentState').style.display = 'none';
            document.getElementById('videoPlayerState').style.display = 'block';
        } catch (e) {
            console.error(e);
            showToast('error', 'Failed to initialize reel player');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-play-circle"></i> SYNTHESIZE AI VIDEO';
            }
        }
    }

    async function ensureAudioContext() {
        const music = document.getElementById('bgMusic');
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        if (audioCtx.state === 'suspended') {
            await audioCtx.resume();
        }

        if (!mediaSource) {
            mediaSource = audioCtx.createMediaElementSource(music);
            mediaSource.connect(audioCtx.destination);
        }
    }

    function initSlideshow() {
        if (slideInterval) clearInterval(slideInterval);
        
        const container = document.getElementById('reelPlayer');
        const progressBar = document.getElementById('progressBar');
        
        // Remove old slides
        container.querySelectorAll('.reel-slide').forEach(s => s.remove());
        progressBar.innerHTML = '';

        // Preload images and create slides
        currentResult.images.forEach((url, i) => {
            const slide = document.createElement('div');
            slide.className = `reel-slide slide-${i}`;
            slide.innerHTML = `
                <img src="${url}" alt="Scene ${i+1}" crossorigin="anonymous" onerror="this.onerror=null; const keywords = '${currentResult.scenes[i]}'.split(' ').slice(0,3).join(','); this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=720&auto=format&fit=crop&sig=' + Math.random();">
                <div class="caption-overlay">${currentResult.captions[i]}</div>
            `;
            container.appendChild(slide);

            const segment = document.createElement('div');
            segment.className = 'progress-segment';
            segment.innerHTML = `<div class="progress-fill fill-${i}"></div>`;
            progressBar.appendChild(segment);
        });

        startSlideshow();
        
        // Dynamic music selection
        const music = document.getElementById('bgMusic');
        const aiGenre = (currentResult.music || '').toLowerCase();
        const formMood = (document.getElementById('mood').value || '').toLowerCase();
        
        const musicMap = {
            'energetic': ['{{ asset("audio/energetic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}'],
            'upbeat': ['{{ asset("audio/upbeat.mp3") }}', '{{ asset("audio/energetic.mp3") }}'],
            'epic': ['{{ asset("audio/energetic.mp3") }}', '{{ asset("audio/cinematic.mp3") }}'],
            'tech': ['{{ asset("audio/energetic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}'],
            'electronic': ['{{ asset("audio/energetic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}'],
            'funky': ['{{ asset("audio/upbeat.mp3") }}', '{{ asset("audio/energetic.mp3") }}'],
            'cinematic': ['{{ asset("audio/cinematic.mp3") }}', '{{ asset("audio/calm.mp3") }}'],
            'luxury': ['{{ asset("audio/cinematic.mp3") }}'],
            'corporate': ['{{ asset("audio/cinematic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}'],
            'inspirational': ['{{ asset("audio/cinematic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}', '{{ asset("audio/calm.mp3") }}'],
            'calm': ['{{ asset("audio/calm.mp3") }}'],
            'relaxed': ['{{ asset("audio/calm.mp3") }}'],
            'lofi': ['{{ asset("audio/calm.mp3") }}', '{{ asset("audio/cinematic.mp3") }}'],
            'ambient': ['{{ asset("audio/calm.mp3") }}', '{{ asset("audio/cinematic.mp3") }}'],
            'chillout': ['{{ asset("audio/calm.mp3") }}'],
            'sad': ['{{ asset("audio/calm.mp3") }}', '{{ asset("audio/cinematic.mp3") }}'],
            'happy': ['{{ asset("audio/upbeat.mp3") }}', '{{ asset("audio/energetic.mp3") }}'],
            'motivation': ['{{ asset("audio/energetic.mp3") }}', '{{ asset("audio/upbeat.mp3") }}'],
        };

        const getRandomTrack = (tracks) => {
            if (!tracks || tracks.length === 0) return '{{ asset("audio/cinematic.mp3") }}';
            return tracks[Math.floor(Math.random() * tracks.length)];
        };

        // Selection priority: Form Mood > AI Genre > Default
        // We prioritize Form Mood because the user explicitly chose it
        let musicUrl = '{{ asset("audio/cinematic.mp3") }}';
        
        // Initial fallback from Form Mood
        for (let key in musicMap) {
            if (formMood.includes(key)) {
                musicUrl = getRandomTrack(musicMap[key]);
                break;
            }
        }

        // Overwrite if AI has a very specific suggestion
        if (aiGenre && aiGenre !== 'cinematic') {
            for (let key in musicMap) {
                if (aiGenre.includes(key)) {
                    musicUrl = getRandomTrack(musicMap[key]);
                    break;
                }
            }
        }
        
        music.src = musicUrl;
        music.play().catch(e => {
            console.log('Audio autoplay prevented, will start on user interaction');
        });
    }

    function startSlideshow() {
        currentSlide = 0;
        showSlide(currentSlide);
        
        slideInterval = setInterval(() => {
            currentSlide++;
            if (currentSlide >= currentResult.images.length) {
                currentSlide = 0;
                // Reset progress bars
                document.querySelectorAll('.progress-fill').forEach(f => f.style.width = '0%');
            }
            showSlide(currentSlide);
        }, SCENE_DURATION);
    }

    function showSlide(index) {
        document.querySelectorAll('.reel-slide').forEach(s => s.classList.remove('active'));
        document.querySelector(`.slide-${index}`).classList.add('active');
        
        const fill = document.querySelector(`.fill-${index}`);
        fill.style.transition = 'none';
        fill.style.width = '0%';
        
        setTimeout(() => {
            fill.style.transition = `width ${SCENE_DURATION}ms linear`;
            fill.style.width = '100%';
        }, 50);
    }

    function restartReel() {
        initSlideshow();
    }

    // Clean up variables to avoid duplicates if re-inserted

    async function downloadReel() {
        const btn = document.getElementById('downloadReelBtn');
        const music = document.getElementById('bgMusic');
        
        if (!music.src) {
            showToast('error', 'Music is not loaded yet.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> CAPTURING REEL...';
        
        const canvas = document.getElementById('recorderCanvas');
        const ctx = canvas.getContext('2d');
        
        // Setup Audio Capture
        let dest;
        try {
            await ensureAudioContext();
            dest = audioCtx.createMediaStreamDestination();
            mediaSource.connect(dest);
        } catch (e) {
            console.error("Audio capture failed", e);
            showToast('warning', 'Audio capture may have issues on this browser.');
        }

        const canvasStream = canvas.captureStream(30);
        const tracks = [...canvasStream.getVideoTracks()];
        if (dest) tracks.push(...dest.stream.getAudioTracks());
        
        const combinedStream = new MediaStream(tracks);

        const recorder = new MediaRecorder(combinedStream, { 
            mimeType: 'video/webm;codecs=vp9,opus',
            videoBitsPerSecond: 5000000 
        });
        
        const chunks = [];

        recorder.ondataavailable = e => chunks.push(e.data);
        recorder.onstop = () => {
            const blob = new Blob(chunks, { type: 'video/webm' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ai-reel-${Date.now()}.webm`;
            a.click();
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-download"></i> DOWNLOAD REEL';
            
            // Clean up audio routing after download
            if (dest && mediaSource) mediaSource.disconnect(dest);
            
            showToast('success', 'Reel downloaded successfully!');
        };

        // Restart everything for a clean recording
        music.currentTime = 0;
        music.play();
        recorder.start();
        
        // Manual rendering loop onto canvas for the duration of the reel
        const totalDuration = currentResult.images.length * SCENE_DURATION;
        const startTime = Date.now();
        
        const images = [];
        await Promise.all(currentResult.images.map(url => {
            return new Promise(resolve => {
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = () => { images.push(img); resolve(); };
                img.onerror = () => { console.error("Failed to load image", url); resolve(); };
                img.src = url;
            });
        }));

        const renderFrame = () => {
            const elapsed = Date.now() - startTime;
            if (elapsed >= totalDuration) {
                recorder.stop();
                music.pause();
                return;
            }

            const frameIndex = Math.floor(elapsed / SCENE_DURATION);
            const slideElapsed = elapsed % SCENE_DURATION;
            const progress = slideElapsed / SCENE_DURATION;
            const img = images[frameIndex % images.length];

            // Clear canvas
            ctx.fillStyle = 'black';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            if (img) {
                // Apply Zoom effect on canvas
                const scale = 1 + (progress * 0.15);
                const w = canvas.width * scale;
                const h = canvas.height * scale;
                const x = (canvas.width - w) / 2;
                const y = (canvas.height - h) / 2;
                ctx.drawImage(img, x, y, w, h);
            }

            // Draw Caption Background (Semi translucent gradient)
            const grad = ctx.createLinearGradient(0, canvas.height - 300, 0, canvas.height);
            grad.addColorStop(0, 'transparent');
            grad.addColorStop(1, 'rgba(0,0,0,0.7)');
            ctx.fillStyle = grad;
            ctx.fillRect(0, canvas.height - 300, canvas.width, 300);

            // Draw Caption
            ctx.fillStyle = 'white';
            ctx.font = 'bold 45px Arial';
            ctx.textAlign = 'center';
            ctx.shadowColor = 'rgba(0,0,0,0.8)';
            ctx.shadowBlur = 15;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 4;
            
            const caption = currentResult.captions[frameIndex % currentResult.captions.length];
            if (caption) {
                const words = caption.split(' ');
                let line = '';
                let yPos = canvas.height - 150;
                let lines = [];
                
                for (let n = 0; n < words.length; n++) {
                    let testLine = line + words[n] + ' ';
                    let metrics = ctx.measureText(testLine);
                    if (metrics.width > canvas.width - 100 && n > 0) {
                        lines.push(line);
                        line = words[n] + ' ';
                    } else {
                        line = testLine;
                    }
                }
                lines.push(line);
                
                // Adjustment for multiple lines
                yPos -= (lines.length - 1) * 30;
                lines.forEach(l => {
                    ctx.fillText(l.trim(), canvas.width/2, yPos);
                    yPos += 55;
                });
            }

            requestAnimationFrame(renderFrame);
        };

        renderFrame();
    }
</script>
@endsection
