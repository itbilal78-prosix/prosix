<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Prosix Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f4f4f2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border: 1.5px solid #e6e6e3;
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }

        .logo {
            font-family: 'DM Mono', monospace;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 32px;
        }

        .logo span { color: #0d0d0d; }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0d0d0d;
            letter-spacing: -.4px;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 13.5px;
            color: #888;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .alert-success {
            background: #eaf7f0;
            border: 1.5px solid #a7f3d0;
            color: #065f46;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #888;
            margin-bottom: 7px;
        }

        .field input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1.5px solid #e6e6e3;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #0d0d0d;
            background: #fafaf8;
            transition: all .2s;
            outline: none;
        }

        .field input:focus {
            border-color: #0d0d0d;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(13,13,13,.06);
        }

        .btn {
            width: 100%;
            height: 50px;
            background: #0d0d0d;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            margin-bottom: 20px;
        }

        .btn:hover { background: #333; }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            color: #888;
            text-decoration: none;
            transition: color .2s;
        }

        .back-link:hover { color: #0d0d0d; }
    </style>
</head>
<body>
<div class="card">

    <div class="logo"><span>Prosix</span> · Admin</div>

    <h1>Forgot Password?</h1>
    <p class="subtitle">Enter your admin email and we'll send you a link to reset your password.</p>

    @if(session('success'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.send') }}">
        @csrf
        <div class="field">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="admin@prosix.com" value="{{ old('email') }}" required autofocus />
            @error('email')
                <span style="font-size:12px;color:#dc2626;margin-top:4px;display:block;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn">Send Reset Link</button>
    </form>

    <a href="{{ route('admin.login') }}" class="back-link">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Login
    </a>
</div>
</body>
</html>
