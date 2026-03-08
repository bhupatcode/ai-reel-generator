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
                                <label for="duration">Duration</label>
                                <select id="duration" name="duration" class="form-select">
                                    <option value="15">15 Seconds</option>
                                    <option value="30">30 Seconds</option>
                                    <option value="60">60 Seconds</option>
                                </select>
                            </div>
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

                            <button onclick="copyJson()" class="btn btn-outline" style="width: 100%; margin-top: 10px; padding: 12px;">
                                Copy Full JSON
                            </button>
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

    document.getElementById('reelForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('generateBtn');
        const loader = document.getElementById('loader');
        const emptyState = document.getElementById('emptyState');
        const contentState = document.getElementById('contentState');
        const resultPanel = document.getElementById('resultPanel');

        btn.disabled = true;
        btn.innerHTML = 'Generating...';
        
        emptyState.style.display = 'none';
        contentState.style.display = 'none';
        loader.style.display = 'flex';
        resultPanel.classList.remove('has-content');

        try {
            const formData = new FormData(e.target);
            const response = await fetch("{{ route('generate.reel') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                currentResult = data.data;
                showToast('success', 'AI Generation Complete!');
                
                // Set text first (hidden)
                document.getElementById('resScript').innerText = data.data.script;
                document.getElementById('resScenes').innerText = data.data.scenes.join(', ');
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
                    await new Promise(r => setTimeout(r, 300));
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
                showToast('error', 'Generation failed: ' + (data.error || 'Unknown error'));
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
            btn.innerHTML = 'Generate Content';
        }
    });

    function backToForm() {
        // Reset View
        document.querySelector('.input-section').classList.remove('hidden');
        document.querySelector('.form-grid').classList.remove('results-active');
        document.getElementById('contentState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'flex';
        document.getElementById('resultPanel').classList.remove('has-content');
        
        // Remove 'show' classes from items for next time
        const items = ['itemScript', 'itemScenes', 'itemCaptions', 'itemMusic'];
        items.forEach(id => document.getElementById(id).classList.remove('show'));
    }

    function copyJson() {
        if (!currentResult) return;
        navigator.clipboard.writeText(JSON.stringify(currentResult, null, 2));
        showToast('success', 'JSON copied to clipboard!');
    }
</script>
@endsection
