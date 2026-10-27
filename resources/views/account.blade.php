@extends('layouts.app')

@section('title', 'My Account')
@section('page', 'account')
@section('body_class', 'account-page')

@section('content')
    {{-- Styles for this page only. They reuse the colours from style.css. --}}
    <style>
        body.account-page {
            background: #1d1b26;
            color: #e2e8f0;
            font-family: 'Barlow', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        [data-page="account"] .nav-link {
            color: #ffffff;
            text-decoration: none;
            position: relative;
        }

        [data-page="account"] .btn-logout:hover {
            background-color: var(--red);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 101, 101, 0.4);
        }

        .acc-wrap {
            width: 92%;
            max-width: 720px;
            margin: 0 auto;
        }

        .acc-hero {
            padding: 130px 0 40px;
            background: linear-gradient(180deg, #0f1318 0%, #1d1b26 100%);
        }

        .acc-eyebrow {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 10px;
        }

        .acc-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: clamp(40px, 6vw, 64px);
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            color: #fff;
            margin-bottom: 12px;
        }

        .acc-title span {
            color: var(--red);
        }

        .acc-sub {
            color: var(--muted);
            font-size: 17px;
        }

        .acc-section {
            padding: 10px 0 80px;
        }

        .acc-panel {
            background: var(--panel);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            padding: 24px;
        }

        .acc-panel h3 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            margin-bottom: 4px;
        }

        .acc-panel .acc-note {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 18px;
        }

        .acc-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px;
        }

        .acc-field label {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .acc-field input {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 15px;
            font-family: inherit;
            width: 100%;
        }

        .acc-field input:focus {
            outline: none;
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.15);
        }

        .acc-error {
            color: #ff5c5c;
            font-size: 13px;
            margin-top: 2px;
        }

        .acc-status {
            background: rgba(72, 187, 120, 0.12);
            border-left: 4px solid var(--green);
            color: #c6f6d5;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .acc-show {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
            margin: 4px 0 18px;
            cursor: pointer;
        }

        .acc-btn {
            width: 100%;
            padding: 12px 0;
            background: var(--red);
            color: #fff;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.25s;
            box-shadow: 0 4px 14px rgba(244, 67, 54, 0.3);
        }

        .acc-btn:hover {
            background: var(--red-dark);
            transform: translateY(-2px);
        }
    </style>

    <main>
        <section class="acc-hero">
            <div class="acc-wrap">
                <p class="acc-eyebrow">My Account</p>
                <h1 class="acc-title">Hello, <span>{{ Str::before(trim($user->name), ' ') }}</span></h1>
                <p class="acc-sub">{{ $user->email }}</p>
            </div>
        </section>

        <section class="acc-section">
            <div class="acc-wrap">
                <div class="acc-panel">
                    <h3>Change Password</h3>
                    <p class="acc-note">Use at least 8 characters, and something different from your current password.</p>

                    @if (session('status'))
                        <div class="acc-status" role="status">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('account.password') }}" method="POST">
                        @csrf

                        <div class="acc-field">
                            <label for="current_password">Current password</label>
                            <input type="password" id="current_password" name="current_password"
                                autocomplete="current-password" required>
                            @error('current_password')
                                <p class="acc-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="acc-field">
                            <label for="password">New password</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                minlength="8" required>
                            @error('password')
                                <p class="acc-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="acc-field">
                            <label for="password_confirmation">Confirm new password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                autocomplete="new-password" minlength="8" required>
                        </div>

                        <label class="acc-show" for="showPasswords">
                            <input type="checkbox" id="showPasswords"> Show passwords
                        </label>

                        <button type="submit" class="acc-btn">Change Password</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        // "Show passwords" tick box for the three fields
        document.getElementById('showPasswords').addEventListener('change', function () {
            ['current_password', 'password', 'password_confirmation'].forEach(function (id) {
                document.getElementById(id).type = this.checked ? 'text' : 'password';
            }, this);
        });
    </script>
@endsection
