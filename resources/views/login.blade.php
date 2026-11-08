@extends('layouts.auth')

@section('title', 'Login')
@section('page', 'login')
@section('script', '1')

@section('content')
    <div class="container">
        <div class="brand">
            <div class="brand-icon">💪</div>
            <div class="brand-name">FIT<span>PASS</span></div>
        </div>
        <div class="divider"></div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <h1>SIGN <span>IN</span></h1>
            <p class="subtitle">Welcome back. Your workouts and meals are waiting.</p>

            @if (session('status'))
                <p class="status" style="color: var(--green); font-size: 13px; text-align: center; margin: -12px 0 16px;">
                    {{ session('status') }}
                </p>
            @endif

            <div class="input-box">
                <input type="email" id="email" name="email" value="{{ old('email', $rememberedEmail) }}"
                    placeholder="Email" autocomplete="email" required>
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

            <div class="remember-forgot">
                <label><input type="checkbox" id="remember" name="remember" @checked(old('remember', $rememberedEmail))>
                    Remember me</label>
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">LOGIN</button>

            <div class="switch-link">
                <p>Don't have an account? <a href="{{ route('register') }}">Sign Up</a></p>
            </div>
        </form>

        {{-- Demo accounts (for professor / testers) --}}
        <div class="acct-box">
            <div class="acct-divider"><span>DEMO ACCOUNTS</span></div>
            <div class="acct-sub">Use these to try out the system</div>

            <div class="acct-card acct-user">
                <div class="acct-head">
                    <span class="acct-icon">👤</span>
                    <span class="acct-role">USER</span>
                    <span class="acct-tag">THIS PAGE</span>
                </div>
                <div class="acct-row"><span class="acct-label">Email</span><code>ronald123@gmail.com</code></div>
                <div class="acct-row"><span class="acct-label">Password</span><code>ronald123</code></div>
                <div class="acct-link">
                    <a href="https://fit-pass.ccs-hinigaran.com/login">fit-pass.ccs-hinigaran.com/login</a>
                </div>
            </div>

            <div class="acct-card acct-admin">
                <div class="acct-head">
                    <span class="acct-icon">🛠️</span>
                    <span class="acct-role">ADMIN</span>
                    <span class="acct-tag">SEPARATE LOGIN</span>
                </div>
                <div class="acct-row"><span class="acct-label">Email</span><code>fitpass.admin@gmail.com</code></div>
                <div class="acct-row"><span class="acct-label">Password</span><code>fitpass_admin123</code></div>
                <a class="acct-btn" href="https://fit-pass.ccs-hinigaran.com/admin/login">GO TO ADMIN LOGIN →</a>
                <div class="acct-link">
                    <a href="https://fit-pass.ccs-hinigaran.com/admin/login">fit-pass.ccs-hinigaran.com/admin/login</a>
                </div>
            </div>
        </div>
    </div>


@endsection
