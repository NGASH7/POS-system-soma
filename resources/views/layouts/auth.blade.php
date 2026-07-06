<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Soma POS') }}@hasSection('title') - @yield('title')@endif</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: #f8fafc;
            color: #0f172a;
        }

        .auth-layout {
            display: flex;
            min-height: 100vh;
        }

        .auth-hero {
            flex: 1;
            background: linear-gradient(160deg, #06162f 0%, #0a2444 45%, #102a52 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
        }

        .auth-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(37, 99, 235, 0.18) 0%, transparent 45%),
                        radial-gradient(circle at 80% 80%, rgba(79, 70, 229, 0.12) 0%, transparent 40%);
        }

        .auth-hero-content {
            position: relative;
            z-index: 1;
            max-width: 480px;
            color: #fff;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }

        .auth-brand-icon {
            width: 48px;
            height: 48px;
            background: #2563eb;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.35);
        }

        .auth-brand-text {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .auth-brand-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .auth-hero-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .auth-hero-subtitle {
            font-size: 17px;
            line-height: 1.6;
            color: #cbd5e1;
            margin-bottom: 36px;
        }

        .auth-features {
            list-style: none;
            display: grid;
            gap: 18px;
        }

        .auth-feature {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .auth-feature-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            background: rgba(37, 99, 235, 0.18);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93c5fd;
            font-size: 16px;
        }

        .auth-feature h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .auth-feature p {
            font-size: 13px;
            line-height: 1.5;
            color: #94a3b8;
        }

        .auth-hero-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: #64748b;
        }

        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: #f8fafc;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 440px;
        }

        .auth-mobile-brand {
            display: none;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .auth-mobile-brand .auth-brand-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .auth-mobile-brand .auth-brand-text {
            font-size: 22px;
        }

        .auth-form-header {
            margin-bottom: 28px;
        }

        .auth-form-title {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .auth-form-subtitle {
            font-size: 15px;
            color: #64748b;
        }

        .auth-field {
            margin-bottom: 18px;
        }

        .auth-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .auth-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            background: #fff;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-input.no-icon {
            padding-left: 14px;
        }

        .auth-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .auth-input::placeholder {
            color: #94a3b8;
        }

        .auth-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
        }

        .auth-toggle-password:hover {
            color: #475569;
        }

        .auth-error {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
        }

        .auth-alert-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .auth-btn-primary {
            width: 100%;
            padding: 13px 16px;
            border: none;
            border-radius: 12px;
            background: #2563eb;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .auth-btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
        }

        .auth-btn-secondary {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            color: #2563eb;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: border-color 0.2s, background 0.2s;
        }

        .auth-btn-secondary:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .auth-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .auth-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .auth-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
        }

        .auth-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
        }

        .auth-divider {
            position: relative;
            text-align: center;
            margin: 22px 0;
        }

        .auth-divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e2e8f0;
        }

        .auth-divider span {
            position: relative;
            background: #f8fafc;
            padding: 0 12px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .auth-switch {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
        }

        .auth-status-bar {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: #94a3b8;
        }

        .auth-status-online {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-weight: 600;
        }

        .auth-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        @media (max-width: 1024px) {
            .auth-layout {
                flex-direction: column;
            }

            .auth-hero {
                padding: 36px 24px;
                min-height: auto;
            }

            .auth-hero-title {
                font-size: 30px;
            }

            .auth-features {
                display: none;
            }

            .auth-hero-footer {
                display: none;
            }

            .auth-mobile-brand {
                display: flex;
            }

            .auth-form-panel {
                padding: 32px 20px 40px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-layout">
        <aside class="auth-hero">
            <div class="auth-hero-content">
                <div class="auth-brand">
                    <div class="auth-brand-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <div class="auth-brand-text">SOMA POS</div>
                        <div class="auth-brand-sub">Retail terminal</div>
                    </div>
                </div>

                <h1 class="auth-hero-title">@yield('hero_title')</h1>
                <p class="auth-hero-subtitle">@yield('hero_subtitle')</p>

                <ul class="auth-features">
                    <li class="auth-feature">
                        <div class="auth-feature-icon"><i class="fas fa-bolt"></i></div>
                        <div>
                            <h4>Fast checkout</h4>
                            <p>Process sales, payments, and receipts in seconds.</p>
                        </div>
                    </li>
                    <li class="auth-feature">
                        <div class="auth-feature-icon"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <h4>Live insights</h4>
                            <p>Track sales, inventory, and performance in real time.</p>
                        </div>
                    </li>
                    <li class="auth-feature">
                        <div class="auth-feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <h4>Secure & reliable</h4>
                            <p>Built for busy shops with M-Pesa and cash support.</p>
                        </div>
                    </li>
                </ul>

                <div class="auth-hero-footer">
                    &copy; {{ date('Y') }} Soma POS. All rights reserved.
                </div>
            </div>
        </aside>

        <main class="auth-form-panel">
            <div class="auth-form-wrap">
                <div class="auth-mobile-brand">
                    <div class="auth-brand-icon"><i class="fas fa-store"></i></div>
                    <div class="auth-brand-text">SOMA POS</div>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
