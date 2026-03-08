@extends('layouts.frontend')

@section('title', 'Privacy Policy')

@section('styles')
    <style>
        .policy-section {
            padding: 160px 0 100px;
        }

        .policy-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 50px;
            max-width: 900px;
            margin: 0 auto;
            line-height: 1.8;
            color: var(--text-dim);
        }

        .policy-card h3 {
            color: var(--text-light);
            margin: 30px 0 15px;
            font-size: 1.5rem;
        }

        .policy-card p {
            margin-bottom: 20px;
        }

        .policy-card ul {
            margin-bottom: 25px;
            padding-left: 20px;
        }

        .policy-card ul li {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="policy-section reveal">
        <div class="container">
            <div class="section-header">
                <h2>Privacy <span class="gradient-text">Policy</span></h2>
                <p>We take your data security seriously. Learn how we handle your information.</p>
            </div>

            <div class="policy-card">
                <h3>1. Information We Collect</h3>
                <p>At ReelGenius AI, we collect information that you provide directly to us when creating an account, such as your name and email address. We also collect the content you generate using our AI tools to improve our services.</p>

                <h3>2. How We Use Your Data</h3>
                <p>We use the collected data to:</p>
                <ul>
                    <li>Provide, maintain, and improve our AI services.</li>
                    <li>Respond to your comments, questions, and requests.</li>
                    <li>Send you technical notices, updates, and security alerts.</li>
                    <li>Monitor and analyze trends, usage, and activities.</li>
                </ul>

                <h3>3. Data Security</h3>
                <p>We use industry-standard security measures to protect your personal information from unauthorized access, loss, or misuse. However, no method of transmission over the internet is 100% secure.</p>

                <h3>4. Third-Party Services</h3>
                <p>Our application uses third-party AI providers (like OpenRouter) to generate content. These providers may receive the prompts you enter but do not receive your personal identification data.</p>
            </div>
        </div>
    </div>
@endsection
