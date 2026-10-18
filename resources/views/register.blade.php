@extends('layouts.auth')

@section('title', 'Register')
@section('page', 'register')
@section('script', '1')

@section('content')
    <div class="container">
        <div class="brand">
            <div class="brand-icon">💪</div>
            <div class="brand-name">FIT<span>PASS</span></div>
        </div>
        <div class="divider"></div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <h1>CREATE <span>ACCOUNT</span></h1>
            <p class="subtitle">Start tracking your workouts and diet today.</p>

            <div class="input-box">
                <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Username"
                    autocomplete="username" required>
                <span class="input-icon">👤</span>
            </div>
            @error('username')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email"
                    autocomplete="email" required>
                <span class="input-icon">✉️</span>
            </div>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box has-toggle">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="new-password"
                    required>
                <span class="input-icon">🔒</span>
                @include('partials.password-toggle', ['target' => 'password'])
            </div>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box has-toggle">
                <input type="password" id="confirmPassword" name="password_confirmation" placeholder="Confirm Password"
                    autocomplete="new-password" required>
                <span class="input-icon">✅</span>
                @include('partials.password-toggle', ['target' => 'confirmPassword'])
            </div>

            <button type="submit" class="btn">REGISTER</button>

            <div class="switch-link">
                <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
            </div>
        </form>
    </div>
@endsection