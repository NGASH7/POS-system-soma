@extends('layouts.auth')

@section('title', 'Login')

@section('hero_title')
    Power your store.<br>Delight your customers.
@endsection

@section('hero_subtitle')
    Complete point of sale solution for modern retailers in Kenya.
@endsection

@section('content')
    <div class="auth-form-header">
        <h2 class="auth-form-title">Welcome back</h2>
        <p class="auth-form-subtitle">Sign in to your POS terminal to continue</p>
    </div>

    @if (session('status'))
        <div class="auth-alert-success">
            <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <div class="auth-input-wrap">
                <i class="fas fa-envelope auth-input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="auth-input" placeholder="admin@somapos.com" required autofocus>
            </div>
            @error('email')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">Password</label>
            <div class="auth-input-wrap">
                <i class="fas fa-lock auth-input-icon"></i>
                <input id="password" type="password" name="password"
                       class="auth-input" placeholder="Enter your password" required>
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password', 'toggle-icon')">
                    <i id="toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-meta">
            <label class="auth-checkbox">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>

    <div class="auth-divider"><span>or</span></div>

    <button type="button" class="auth-btn-secondary" onclick="showQrInfo()">
        <i class="fas fa-qrcode"></i> Sign in with QR Code
    </button>

    @if (Route::has('register'))
        <p class="auth-switch">
            New to Soma POS?
            <a href="{{ route('register') }}" class="auth-link">Create an account</a>
        </p>
    @endif

    <div class="auth-status-bar">
        <span>Terminal TERM-01</span>
        <span class="auth-status-online">
            <span class="auth-status-dot"></span>
            Online
        </span>
    </div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function showQrInfo() {
        alert('QR Code login coming soon!\n\nUse the Soma POS mobile app to scan and login.');
    }
</script>
@endpush
