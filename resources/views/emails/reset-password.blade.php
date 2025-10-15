@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
    <div class="greeting">Password Reset Request</div>
    
    <div class="content">
        Hi <strong>{{ $user->name ?? $user->username }}</strong>,
    </div>
    
    <div class="content">
        We received a request to reset your password for your <strong>IOMEH</strong> account.
    </div>

    <div class="highlight-box">
        <h2 style="margin-top: 0; color: #c4e456;">Reset Your Password</h2>
        <p style="margin-bottom: 25px;">Click the button below to create a new password and get back to your health journey!</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>
    </div>

    <div class="security-note">
        <strong>⚠️ Important Security Information:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>This link will expire in 60 minutes for your security</li>
            <li>If you didn't request this reset, please ignore this email</li>
            <li>Your current password will remain unchanged until you click the link</li>
        </ul>
    </div>

    <div class="content">
        <strong>Once you reset your password:</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li>✅ You'll be able to log in to your account</li>
            <li>🎯 Continue your health journey where you left off</li>
            <li>🪙 Keep earning those consistency points</li>
            <li>📊 Your progress, streaks, and rankings are safely stored</li>
        </ul>
    </div>

    <div class="secondary-text">
        <strong>Can't click the button?</strong> Copy and paste this link into your browser:
        <div class="url-text">{{ $resetUrl }}</div>
    </div>

    <div class="secondary-text">
        <em>Don't let a forgotten password stop your health momentum. Get back on track with just one click!</em>
    </div>
@endsection
