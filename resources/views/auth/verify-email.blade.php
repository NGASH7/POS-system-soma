@extends('layouts.auth')

@section('title', 'Verify Email')

@section('hero_title')
    Verify your<br>email address.
@endsection

@section('hero_subtitle')
    Confirm your email to secure your Soma POS account and access all features.
@endsection

@section('content')
    <div class="auth-form-header">
        <h2 class="auth-form-title">Email verification</h2>
        <p class="auth-form-subtitle">Check your inbox for the verification link we sent you</p>
    </div>

    <p class="mb-4 text-sm text-slate-500">
        Thanks for signing up! Before getting started, verify your email address by clicking the link we emailed you. If you did not receive it, we can send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-alert-success mb-4">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-btn-primary">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-link text-sm">Log Out</button>
        </form>
    </div>
@endsection
