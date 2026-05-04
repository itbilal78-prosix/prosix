<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; background: #f4f4f2; margin: 0; padding: 40px 20px; }
        .wrap { max-width: 520px; margin: 0 auto; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; border: 1.5px solid #e6e6e3; }
        .logo { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: #999; margin-bottom: 32px; font-weight: 600; }
        .logo span { color: #0d0d0d; }
        h1 { font-size: 22px; font-weight: 700; color: #0d0d0d; margin-bottom: 12px; letter-spacing: -.3px; }
        p { font-size: 14px; color: #666; line-height: 1.7; margin-bottom: 16px; }
        .btn { display: inline-block; background: #0d0d0d; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-size: 14px; font-weight: 700; margin: 8px 0 24px; }
        .url-box { background: #f4f4f2; border: 1px solid #e6e6e3; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #888; word-break: break-all; margin-bottom: 24px; }
        .footer { font-size: 12px; color: #bbb; margin-top: 24px; text-align: center; }
        .warn { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #92400e; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="logo"><span>Prosix</span> · Admin Panel</div>

        <h1>Password Reset Request</h1>

        <p>Hi <strong>{{ $admin->name }}</strong>,</p>
        <p>We received a request to reset your admin account password. Click the button below to set a new password. This link will expire in <strong>60 minutes</strong>.</p>

        <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>

        <div class="warn">
            ⚠️ If you did not request this, ignore this email. Your password will remain unchanged.
        </div>

        <p style="font-size:13px;color:#aaa;">If the button doesn't work, copy this link:</p>
        <div class="url-box">{{ $resetUrl }}</div>

        <div class="footer">
            Prosix Sports Admin · This is an automated email, do not reply.
        </div>
    </div>
</div>
</body>
</html>
