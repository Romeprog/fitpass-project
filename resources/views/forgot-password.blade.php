@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('page', 'forgot-password')
@section('script', '1')

@section('content')
    <div class="container">
        <div class="brand">
            <div class="brand-icon">💪</div>
            <div class="brand-name">FIT<span>PASS</span></div>
        </div>
        <div class="divider"></div>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <h1>FORGOT <span>PASSWORD</span></h1>
            <p class="subtitle">Enter your email and we will send you a link to choose a new password.</p>

            @if (session('status'))
                <p class="status" style="color: var(--green); font-size: 13px; text-align: center; margin: -12px 0 16px;">
                    {{ session('status') }}
                </p>
            @endif

            <div class="input-box">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email"
                    autocomplete="email" required>
                <span class="input-icon">✉️</span>
            </div>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn">SEND RESET LINK</button>

            <div class="switch-link">
                <p>Remembered it? <a href="{{ route('login') }}">Back to Sign In</a></p>
            </div>
        </form>
    </div>
@endsection
