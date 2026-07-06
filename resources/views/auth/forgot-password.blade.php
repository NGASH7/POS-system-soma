@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('hero_title')
    Reset your<br>access quickly.
@endsection

@section('hero_subtitle')
    We'll send a secure link to your email so you can get back to your terminal.
@endsection

@section('content')
    <div class="auth-form-header">
        <h2 class="auth-form-title">Forgot password?</h2>
        <p class="auth-form-subtitle">Enter your email and we'll send you a reset link</p>
    </div>

    @if (session('status'))
        <div class="auth-alert-success">
            <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-paper-plane"></i> Email Reset Link
        </button>
    </form>

    <p class="auth-switch">
        <a href="{{ route('login') }}" class="auth-link"><i class="fas fa-arrow-left mr-1"></i> Back to sign in</a>
    </p>
@endsection
