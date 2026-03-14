@extends('layouts.frontend')

@section('title', 'Frequently Asked Questions')

@section('styles')
    <style>
        .faq-section {
            padding: 160px 0 100px;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .accordion-item {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .accordion-item:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .accordion-header {
            padding: 24px 30px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-light);
        }

        .accordion-content {
            padding: 0 30px 24px;
            color: var(--text-dim);
            display: none;
            line-height: 1.7;
        }

        .accordion-icon {
            transition: transform 0.3s ease;
            color: var(--primary);
        }

        .accordion-item.active .accordion-content {
            display: block;
        }

        .accordion-item.active .accordion-icon {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .faq-section {
                padding: 100px 0 60px;
            }
            .accordion-header {
                padding: 20px;
                font-size: 1rem;
            }
            .accordion-content {
                padding: 0 20px 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="faq-section reveal">
        <div class="container">
            <div class="section-header">
                <h2>Got Questions? <br> <span class="gradient-text">We Have Answers</span></h2>
                <p>Everything you need to know about our AI Reel Generator and how to take your content to the next level.</p>
            </div>

            <div class="faq-container">
                <div class="accordion-item reveal delay-1">
                    <div class="accordion-header">
                        How does the AI generator work?
                        <i class="bi bi-chevron-down accordion-icon"></i>
                    </div>
                    <div class="accordion-content">
                        Our system uses advanced AI models (like Gemini and GPT) to analyze your topic. It then structures a professional script, suggests visual scenes, generates engaging captions, and even recommends music that matches the mood of your content.
                    </div>
                </div>

                <div class="accordion-item reveal delay-2">
                    <div class="accordion-header">
                        Is there a free trial?
                        <i class="bi bi-chevron-down accordion-icon"></i>
                    </div>
                    <div class="accordion-content">
                        Yes! Every new account comes with free credits to test our "World Class" AI generator. You can start creating viral reels immediately after registration without adding a credit card.
                    </div>
                </div>

                <div class="accordion-item reveal delay-3">
                    <div class="accordion-header">
                        What kind of videos can I create?
                        <i class="bi bi-chevron-down accordion-icon"></i>
                    </div>
                    <div class="accordion-content">
                        You can create any type of short-form content including inspirational quotes, educational tips, funny skits, cinematic stories, and energetic promos. Simply tell the AI your topic and choose your preferred mood.
                    </div>
                </div>

                <div class="accordion-item reveal delay-4">
                    <div class="accordion-header">
                        Can I customize the generated scripts?
                        <i class="bi bi-chevron-down accordion-icon"></i>
                    </div>
                    <div class="accordion-content">
                        Absolutely. Once the AI generates your content, you can copy the full JSON output or individual sections and tweak them to perfectly fit your personal brand or specific requirements.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.accordion-header');
        
        items.forEach(item => {
            item.addEventListener('click', () => {
                const parent = item.parentElement;
                const isActive = parent.classList.contains('active');
                
                // Close all other items
                document.querySelectorAll('.accordion-item').forEach(el => {
                    el.classList.remove('active');
                });
                
                if (!isActive) {
                    parent.classList.add('active');
                }
            });
        });
    });
</script>
@endsection
