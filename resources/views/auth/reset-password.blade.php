@extends('layouts.auth')

@section('title', 'Reset Password')

@section('hero_title')
    Set a new<br>secure password.
@endsection

@section('hero_subtitle')
    Choose a strong password to protect your Soma POS account.
@endsection

@section('content')
    <div class="auth-form-header">
        <h2 class="auth-form-title">Reset password</h2>
        <p class="auth-form-subtitle">Enter your new password below</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <div class="auth-input-wrap">
                <i class="fas fa-envelope auth-input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                       class="auth-input" required autofocus autocomplete="username">
            </div>
            @error('email')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">New password</label>
            <div class="auth-input-wrap">
                <i class="fas fa-lock auth-input-icon"></i>
                <input id="password" type="password" name="password"
                       class="auth-input" placeholder="New password" required autocomplete="new-password">
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
                       class="auth-input" placeholder="Confirm password" required autocomplete="new-password">
            </div>
            @error('password_confirmation')
                <p class="auth-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-key"></i> Reset Password
        </button>
    </form>

    <p class="auth-switch">
        <a href="{{ route('login') }}" class="auth-link"><i class="fas fa-arrow-left mr-1"></i> Back to sign in</a>
    </p>
@endsection
