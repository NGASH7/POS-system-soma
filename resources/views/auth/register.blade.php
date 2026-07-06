@extends('layouts.auth')

@section('title', 'Register')

@section('hero_title')
    Join Soma POS.<br>Start selling today.
@endsection

@section('hero_subtitle')
    Create your account and manage sales, stock, and payments from one place.
@endsection

@section('content')
    <div class="auth-form-header">
        <h2 class="auth-form-title">Create account</h2>
        <p class="auth-form-subtitle">Get started with your Soma POS workspace</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="name">Full name</label>
            <div class="auth-input-wrap">
                <i class="fas fa-user auth-input-icon"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="auth-input" placeholder="John Doe" required autofocus>
            </div>
            @error('name')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <div class="auth-input-wrap">
                <i class="fas fa-envelope auth-input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="auth-input" placeholder="admin@somapos.com" required>
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
                       class="auth-input" placeholder="Create a password" required>
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password', 'password-toggle-icon')">
                    <i id="password-toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">Confirm password</label>
            <div class="auth-input-wrap">
                <i class="fas fa-lock auth-input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="auth-input" placeholder="Confirm your password" required>
                <button type="button" class="auth-toggle-password" onclick="togglePassword('password_confirmation', 'confirm-toggle-icon')">
                    <i id="confirm-toggle-icon" class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </form>

    <p class="auth-switch">
        Already have an account?
        <a href="{{ route('login') }}" class="auth-link">Sign in</a>
    </p>

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
</script>
@endpush
