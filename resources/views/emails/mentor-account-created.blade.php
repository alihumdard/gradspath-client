<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Grads Paths Mentor Account Credentials</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fa;
            color: #111827;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .email-shell {
            width: 100%;
            background-color: #f4f6fa;
            padding: 8px 18px 34px;
        }

        .logo {
            width: 300px;
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
            border: 0;
            outline: none;
            text-decoration: none;
        }

        .card {
            max-width: 540px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e6e8ef;
            border-radius: 10px;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.10);
            padding: 38px 40px 34px;
        }

        .card h1 {
            margin: 0 0 16px;
            color: #111827;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .card p {
            margin: 0 0 16px;
            color: #4b5563;
            font-size: 15px;
            line-height: 1.7;
        }

        .card p strong {
            color: #344054;
            font-weight: 700;
        }

        .credentials-panel {
            margin: 24px 0;
            padding: 24px;
            background-color: #fafbfe;
            border: 1px solid #e1e4ec;
            border-radius: 12px;
        }

        .credential-row {
            margin-bottom: 12px;
        }

        .credential-row:last-child {
            margin-bottom: 0;
        }

        .credential-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .credential-value {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            word-break: break-all;
            letter-spacing: 0.02em;
        }

        .cta-btn {
            display: inline-block;
            margin-top: 12px;
            padding: 14px 28px;
            background-color: #3b82f6;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
        }

        .divider {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 24px 0;
        }

        .footer {
            max-width: 540px;
            margin: 24px auto 0;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer a {
            color: #4f7fd9;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>
<body bgcolor="#f4f6fa">
    <div class="email-shell" bgcolor="#f4f6fa">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="brand" style="max-width:640px;margin:0 auto 6px;">
            <tr>
                <td align="center" bgcolor="#f4f6fa" style="background-color:#f4f6fa;padding:4px 18px;">
                    <img src="{{ asset('gradspaths_logo/Gradspaths_logo_transparent.png') }}" alt="Grads Paths" class="logo" width="330" border="0" style="width:330px;max-width:330px;height:auto;display:block;margin:0 auto;border:0;outline:none;text-decoration:none;">
                </td>
            </tr>
        </table>

        <div class="card">
            <h1>Welcome to Grads Paths!</h1>
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>An administrator has created a Mentor account for you on Grads Paths. Below are your login credentials:</p>

            <div class="credentials-panel">
                <div class="credential-row">
                    <div class="credential-label">Email Address</div>
                    <div class="credential-value">{{ $email }}</div>
                </div>
                <div class="credential-row" style="margin-top: 16px;">
                    <div class="credential-label">Password</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>

            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-btn">Log In to Your Mentor Account</a>
            </p>

            <hr class="divider">

            <p style="font-size: 13px; color: #6b7280;">For security purposes, we recommend updating your password after your initial login.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Grads Paths &nbsp;&bull;&nbsp;
            <a href="mailto:support@gradspaths.com">Support</a>
        </div>
    </div>
</body>
</html>
