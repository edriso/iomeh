<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - IOMeH</title>
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fffdf5;
        }
        .container {
            background: #fffdf5;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(44, 62, 80, 0.1);
            border: 1px solid #c5d4dd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dae0dd;
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
            color: #bbd0dc;
            margin-bottom: 8px;
        }
        .tagline {
            color: #5a6c7d;
            font-size: 16px;
            font-style: italic;
        }
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            font-size: 16px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            background-color: #bbd0dc;
            color: #2c3e50 !important;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(187, 208, 220, 0.3);
            transition: all 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(187, 208, 220, 0.4);
            background-color: #afc0d4;
        }
        .secondary-text {
            font-size: 14px;
            color: #5a6c7d;
            margin: 15px 0;
        }
        .url-text {
            background: #dae0dd;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 10px 0;
            color: #2c3e50;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #c5d4dd;
            color: #5a6c7d;
            font-size: 14px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f8fdf0 0%, #f0f9e8 100%);
            border-left: 4px solid #bbd0dc;
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
            <img src="{{ config('app.url') }}/iomeh.png" alt="IOMeH" class="logo">
            <div class="app-name">IOMeH</div>
            <div class="tagline">I Owe Me Health</div>
        </div>

        @yield('content')

        <div class="footer">
            <p>Stay healthy!<br>IOMeH Team</p>
            <p style="font-size: 12px; color: #5a6c7d;">
                This email was sent to {{ $user->email ?? 'you' }}. If you didn't create an account, you can safely ignore this email.
            </p>
        </div>
    </div>
</body>
</html>
