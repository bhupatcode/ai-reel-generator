@extends('layouts.frontend')

@section('title', 'AI Reel Generator')

@section('styles')
    <style>
        .dashboard-container {
            padding-top: 160px;
            padding-bottom: 100px;
        }

        .generator-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }

        .generator-header {
            margin-bottom: 40px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 20px;
        }

        .generator-header h2 {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .generator-header p {
            color: var(--text-dim);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: var(--text-light);
            font-weight: 600;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 14px 20px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: white;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .result-panel {
            background: rgba(15, 23, 42, 0.3);
            border: 1px dashed var(--glass-border);
            border-radius: 24px;
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--text-dim);
        }

        .result-panel.has-content {
            border-style: solid;
            background: var(--bg-dark);
            align-items: flex-start;
            text-align: left;
            justify-content: flex-start;
            color: var(--text-light);
        }

        .loader-container {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-generate {
            width: 100%;
            padding: 18px;
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .result-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            width: 100%;
        }

        .result-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 5px;
            display: block;
        }

        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
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
                                    <option value="15s">15 Seconds</option>
                                    <option value="30s">30 Seconds</option>
                                    <option value="60s">60 Seconds</option>
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

                        {{-- Loading State --}}
                        <div id="loader" class="loader-container">
                            <div class="loader"></div>
                            <p>AI is crafting your story...</p>
                        </div>

                        {{-- Content State --}}
                        <div id="contentState" style="display: none; width: 100%;">
                            <h4 style="margin-bottom: 20px; color: var(--primary);">Generation Ready!</h4>
                            
                            <div class="result-item">
                                <span class="result-label">Script</span>
                                <div id="resScript" style="font-size: 0.95rem; line-height: 1.6;"></div>
                            </div>

                            <div class="result-item">
                                <span class="result-label">Visual Scenes</span>
                                <div id="resScenes" style="font-size: 0.9rem;"></div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="result-item">
                                    <span class="result-label">Captions</span>
                                    <div id="resCaptions" style="font-size: 0.85rem;"></div>
                                </div>
                                <div class="result-item">
                                    <span class="result-label">Music Suggestions</span>
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
                currentResult = data.reel;
                
                document.getElementById('resScript').innerText = data.reel.script;
                document.getElementById('resScenes').innerText = data.reel.scenes.join(', ');
                document.getElementById('resCaptions').innerText = data.reel.captions.join(' | ');
                document.getElementById('resMusic').innerText = data.reel.music;

                loader.style.display = 'none';
                contentState.style.display = 'block';
                resultPanel.classList.add('has-content');
            } else {
                alert('Generation failed: ' + (data.error || 'Unknown error'));
                loader.style.display = 'none';
                emptyState.style.display = 'flex';
            }
        } catch (error) {
            console.error(error);
            alert('An error occurred. Please try again.');
            loader.style.display = 'none';
            emptyState.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Generate Content';
        }
    });

    function copyJson() {
        if (!currentResult) return;
        navigator.clipboard.writeText(JSON.stringify(currentResult, null, 2));
        alert('JSON copied to clipboard!');
    }
</script>
@endsection
