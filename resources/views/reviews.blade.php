@extends('layouts.frontend')

@section('title', 'Our Reviews')

@section('styles')
    <style>
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
        }

        .review-card {
            padding: 30px;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            position: relative;
        }

        .review-card .stars {
            color: #fbbf24;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .review-card p {
            color: var(--text-dim);
            font-style: italic;
            margin-bottom: 25px;
            font-size: 1.05rem;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-info strong {
            display: block;
            color: var(--text-light);
        }

        .submission-section {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(244, 63, 94, 0.05));
            padding: 80px 0;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
        }

        .review-form {
            max-width: 600px;
            margin: 40px auto 0;
            background: var(--bg-dark);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
        }

        .rating-select {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rating-btn {
            padding: 5px 15px;
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-dim);
            transition: all 0.3s ease;
        }

        .rating-btn.active, .rating-btn:hover {
            border-color: var(--primary);
            color: white;
            background: rgba(99, 102, 241, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dim);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            outline: none;
        }

        @media (max-width: 768px) {
            .reviews-grid {
                grid-template-columns: 1fr;
            }
            .submission-section {
                padding: 40px 15px;
                border-radius: 20px;
            }
            .review-form {
                padding: 24px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="section-header">
            <h2>Community Reviews</h2>
            <p>See what creators around the world are saying about ReelGenius AI.</p>
        </div>

        <div class="reviews-grid">
            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="stars">
                        @for($i = 0; $i < $review->rating; $i++)
                            ★
                        @endfor
                    </div>
                    <p>"{{ $review->comment }}"</p>
                    <div class="review-author">
                        <div class="author-info">
                            <strong>{{ $review->name }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Mock Reviews if none in DB --}}
                <div class="review-card">
                    <div class="stars">★★★★★</div>
                    <p>"The fastest way to go from idea to viral reel. I save at least 5 hours of editing every week."</p>
                    <div class="review-author"><strong>Alex Rivera</strong></div>
                </div>
                <div class="review-card">
                    <div class="stars">★★★★★</div>
                    <p>"Google Gemini integration is a game changer for script variety. Highly recommended for faceless channels."</p>
                    <div class="review-author"><strong>Jordan Smith</strong></div>
                </div>
                <div class="review-card">
                    <div class="stars">★★★★</div>
                    <p>"Great output quality. It would be even better with more voice options, but for the price, it's unbeatable."</p>
                    <div class="review-author"><strong>Mia Chen</strong></div>
                </div>
            @endforelse
        </div>

        <div class="submission-section">
            <div class="container">
                <div class="section-header">
                    <h2>Leave a Review</h2>
                    <p>Share your experience with our community.</p>
                </div>

                <div class="review-form">
                    @if(session('success'))
                        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ url('/reviews') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Rating</label>
                            <select name="rating" style="width: 100%; padding: 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px; color: white;">
                                <option value="5">5 Stars - Excellent</option>
                                <option value="4">4 Stars - Very Good</option>
                                <option value="3">3 Stars - Good</option>
                                <option value="2">2 Stars - Fair</option>
                                <option value="1">1 Star - Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <textarea id="comment" name="comment" rows="4" required placeholder="What do you think?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
