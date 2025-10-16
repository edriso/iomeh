<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - IOMeW</title>
    <style>
        body {
            font-family: 'Nunito', Georgia, serif;
            line-height: 1.6;
            color: #0a0a0a;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            display: block;
        }
        .app-name {
            font-size: 28px;
            font-weight: 700;
            color: #c4e456;
            margin-bottom: 8px;
        }
        .tagline {
            color: #64748b;
            font-size: 16px;
            font-style: italic;
        }
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            font-size: 16px;
            margin-bottom: 20px;
            color: #374151;
        }
        .button {
            display: inline-block;
            background-color: #c4e456;
            color: #0a0a0a !important;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(196, 228, 86, 0.3);
            transition: all 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(196, 228, 86, 0.4);
        }
        .secondary-text {
            font-size: 14px;
            color: #64748b;
            margin: 15px 0;
        }
        .url-text {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f8fdf0 0%, #f0f9e8 100%);
            border-left: 4px solid #c4e456;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .security-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ config('app.url') }}/iomew.png" alt="IOMeW" class="logo">
            <div class="app-name">IOMeW</div>
            <div class="tagline">I Owe Me Wellness</div>
        </div>

        @yield('content')

        <div class="footer">
            <p>Stay well!<br>IOMeW Team</p>
            <p style="font-size: 12px; color: #94a3b8;">
                This email was sent to {{ $user->email ?? 'you' }}. If you didn't create an account, you can safely ignore this email.
            </p>
        </div>
    </div>
</body>
</html>
