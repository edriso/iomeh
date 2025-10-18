@extends('emails.layout')

@section('title', 'Verify Your Email')

@section('content')
    <div class="greeting">Almost there!</div>
    
    <div class="content">
        Hi <strong>{{ $user->name ?? $user->username }}</strong>,
    </div>
    
    <div class="content">
        Thank you for joining <strong>IOMeH - I Owe Me Health</strong>! We're thrilled to have you on this health journey where consistency beats intensity.
    </div>

    <div class="highlight-box">
        <h2 style="margin-top: 0; color: #c4e456;">Verify Your Email Address</h2>
        <p style="margin-bottom: 25px;">Click the button below to verify your email and unlock your health potential. Your first points are waiting!</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verify Email & Start Your Journey</a>
        </div>
    </div>

    <div class="content">
        <strong>What happens after verification?</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li>✅ Your account will be fully activated and secure</li>
            <li>🎯 You'll be ready to log your first activity</li>
            <li>🪙 Start earning points for daily activities</li>
            <li>📊 Track your progress and build streaks</li>
            <li>🏆 Compete on global rankings</li>
        </ul>
    </div>

    <div class="content">
        <strong>Here's what makes IOMeH different:</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li><strong>Global Rankings</strong> - Compete with health enthusiasts worldwide</li>
            <li><strong>Consistency Points</strong> - Rewards daily activities over sporadic bursts</li>
            <li><strong>Holistic Health</strong> - Track workouts, nutrition, wellness, and mindfulness</li>
            <li><strong>Community</strong> - Stay motivated with others on their health journey</li>
            <li><strong>Progress Tracking</strong> - Visualize your journey with streaks and rankings</li>
        </ul>
    </div>

    <div class="security-note">
        <strong>⚠️ Security Note:</strong> This verification link will expire in 24 hours. If you didn't create an account with IOMeH, please ignore this email.
    </div>

    <div class="secondary-text">
        <strong>Can't click the button?</strong> Copy and paste this link into your browser:
        <div class="url-text">{{ $verificationUrl }}</div>
    </div>

    <div class="secondary-text">
        <em>Remember: Every great health journey starts with a single step. You owe it to yourself!</em>
    </div>
@endsection
