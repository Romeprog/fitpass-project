@extends('layouts.auth')

@section('title', 'Reset Password')
@section('page', 'reset-password')
@section('script', '1')

@section('content')
    <div class="container">
        <div class="brand">
            <div class="brand-icon">💪</div>
            <div class="brand-name">FIT<span>PASS</span></div>
        </div>
        <div class="divider"></div>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <h1>NEW <span>PASSWORD</span></h1>
            <p class="subtitle">Choose a new password for {{ old('email', $email) }}.</p>

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box has-toggle">
                <input type="password" id="password" name="password" placeholder="New password"
                    autocomplete="new-password" required>
                <span class="input-icon">🔒</span>
                @include('partials.password-toggle', ['target' => 'password'])
            </div>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box has-toggle">
                <input type="password" id="confirmPassword" name="password_confirmation"
                    placeholder="Confirm new password" autocomplete="new-password" required>
                <span class="input-icon">✅</span>
                @include('partials.password-toggle', ['target' => 'confirmPassword'])
            </div>

            <button type="submit" class="btn">CHANGE PASSWORD</button>

            <div class="switch-link">
                <p>Link not working? <a href="{{ route('password.request') }}">Ask for a new one</a></p>
            </div>
        </form>
    </div>
@endsection
