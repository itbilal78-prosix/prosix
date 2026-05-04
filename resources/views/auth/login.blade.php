@extends('layouts.auth')

@section('content')

<div class="auth-body">

    <div class="wrapper-admin">

        <!-- CLOSE BUTTON -->
        <a href="/" class="close-auth-btn" title="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </a>

        <!-- LEFT: LOGIN FORM -->
        <div class="form-box-admin">

            <div class="form-header">
                <h2>Admin Login</h2>
                <p>Enter your credentials to continue</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- EMAIL -->
                <div class="input-group">
                    <label for="emailField">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            id="emailField"
                            name="email"
                            placeholder="admin@example.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div class="input-group">
                    <label for="passwordField">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="passwordField"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="eye-btn" id="togglePassword" aria-label="Toggle password visibility">
                            <!-- Eye OPEN (default) -->
                            <svg id="iconShow" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="display:block">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <!-- Eye OFF (hidden by default) -->
                            <svg id="iconHide" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-admin">
                    Login
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
                   {{-- Forgot Password --}}
                <div style="text-align:center; margin-top:14px;">
                    <a href="{{ route('admin.password.request') }}"
                       style="font-size:13px; color:#888; text-decoration:none;"
                       onmouseover="this.style.color='#111'"
                       onmouseout="this.style.color='#888'">
                        Forgot Password?
                    </a>
                </div>

            </form>

        </div>

        <!-- RIGHT: INFO SIDE -->
        <div class="info-side-admin">

            <div class="logo-circle">
                <img
                    src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                    class="admin-logo"
                    alt="Logo"
                >
            </div>

            <div class="info-text">
                <h3>Welcome Back</h3>
                <p>Manage your dashboard, track activity, and stay in full control of your admin panel.</p>
            </div>

            <div class="info-dots">
                <span></span><span></span><span></span>
            </div>

        </div>

    </div>

</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn      = document.getElementById('togglePassword');
        var field    = document.getElementById('passwordField');
        var iconShow = document.getElementById('iconShow');
        var iconHide = document.getElementById('iconHide');
        var visible  = false;

        if (!btn) return;

        btn.addEventListener('click', function () {
            visible = !visible;
            field.type             = visible ? 'text'  : 'password';
            iconShow.style.display = visible ? 'none'  : 'block';
            iconHide.style.display = visible ? 'block' : 'none';
        });
    });
</script>

<style>

/* ─── RESET ─────────────────────────────────────────── */
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ─── PAGE ───────────────────────────────────────────── */
.auth-body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f4f4f2;
    padding: 1rem;
}

/* ─── CARD ───────────────────────────────────────────── */
.wrapper-admin {
    width: 760px;
    max-width: 100%;
    display: flex;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,0.10);
    border: 1px solid rgba(0,0,0,0.08);
}

/* ─── CLOSE BTN ─────────────────────────────────────── */
.close-auth-btn {
    position: absolute;
    right: 14px;
    top: 14px;
    z-index: 10;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    color: #fff;
    text-decoration: none;
    transition: background 0.2s;
}
.close-auth-btn:hover {
    background: rgba(255,255,255,0.25);
}

/* ─── LEFT FORM ──────────────────────────────────────── */
.form-box-admin {
    width: 52%;
    padding: 52px 44px;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0;
}

.form-header {
    margin-bottom: 28px;
}

.form-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #111;
    letter-spacing: -0.3px;
    margin-bottom: 5px;
}

.form-header p {
    font-size: 13px;
    color: #888;
}

/* ─── ALERT ──────────────────────────────────────────── */
.alert-danger {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff5f5;
    border: 1px solid #fecdcd;
    color: #c0392b;
    font-size: 13px;
    padding: 10px 14px;
    border-radius: 10px;
    margin-bottom: 18px;
}

/* ─── INPUT GROUP ────────────────────────────────────── */
.input-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 18px;
}

.input-group label {
    font-size: 12px;
    font-weight: 600;
    color: #555;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 12px;
    color: #aaa;
    display: flex;
    align-items: center;
    pointer-events: none;
}

.input-wrap input {
    width: 100%;
    padding: 11px 40px 11px 38px;
    font-size: 14px;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    background: #fafafa;
    color: #111;
    outline: none;
    transition: border-color 0.2s, background 0.2s;
}

.input-wrap input:focus {
    border-color: #111;
    background: #fff;
}

.input-wrap input::placeholder {
    color: #bbb;
}

/* ─── EYE BUTTON ─────────────────────────────────────── */
.eye-btn {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    color: #aaa;
    transition: color 0.2s;
}

.eye-btn:hover {
    color: #333;
}

/* ─── FIELD ERROR ────────────────────────────────────── */
.field-error {
    font-size: 12px;
    color: #c0392b;
    margin-top: 2px;
}

/* ─── SUBMIT BUTTON ──────────────────────────────────── */
.btn-admin {
    margin-top: 8px;
    width: 100%;
    padding: 12px 20px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s, transform 0.15s;
}

.btn-admin:hover {
    background: #333;
}

.btn-admin:active {
    transform: scale(0.98);
}

/* ─── RIGHT INFO SIDE ────────────────────────────────── */
.info-side-admin {
    width: 48%;
    background: #111;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 52px 36px;
    gap: 24px;
    text-align: center;
}

.logo-circle {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    border: 1.5px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
}

.admin-logo {
    width: 52px;
    object-fit: contain;
}

.info-text h3 {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
    letter-spacing: -0.2px;
}

.info-text p {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    line-height: 1.75;
    max-width: 220px;
    margin: 0 auto;
}

.info-dots {
    display: flex;
    gap: 6px;
}

.info-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
}

.info-dots span:first-child {
    background: rgba(255,255,255,0.7);
}

/* ─── RESPONSIVE ─────────────────────────────────────── */
@media (max-width: 580px) {
    .wrapper-admin {
        flex-direction: column-reverse;
        border-radius: 14px;
    }

    .form-box-admin,
    .info-side-admin {
        width: 100%;
    }

    .form-box-admin {
        padding: 36px 28px;
    }

    .info-side-admin {
        padding: 36px 28px;
        border-radius: 0;
    }

    .close-auth-btn {
        color: #fff;
    }
}

</style>
