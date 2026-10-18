@extends('layouts.auth')

@section('title', 'Admin Login')
@section('page', 'login')
@section('script', '1')

@section('content')
    <div class="container">
        <div class="brand">
            <div class="brand-icon">🛡️</div>
            <div class="brand-name">FIT<span>PASS</span></div>
        </div>
        <div class="divider"></div>

        <form action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
            @csrf
            <h1>ADMIN <span>SIGN IN</span></h1>
            <p class="subtitle">Restricted area. Authorized staff only.</p>

            <div class="input-box">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Admin Email"
                    autocomplete="off" required autofocus>
                <span class="input-icon">✉️</span>
            </div>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <div class="input-box has-toggle">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password"
                    required>
                <span class="input-icon">🔒</span>
                @include('partials.password-toggle', ['target' => 'password'])
            </div>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn">ADMIN LOGIN</button>

            <div class="switch-link">
                <p>Not an admin? <a href="{{ route('login') }}">Member sign in</a></p>
            </div>
        </form>
    </div>
@endsection