@extends('emails.layout')

@section('title', 'Welcome to IOMEH!')

@section('content')
    <div class="greeting">Welcome to IOMEH!</div>
    
    <div class="content">
        Hi <strong>{{ $user->name ?? $user->username }}</strong>,
    </div>
    
    <div class="content">
        Welcome to <strong>IOMEH - I Owe Me Health</strong>! You've just joined a global health and fitness community where <em>consistency beats intensity</em>. Your health journey starts now!
    </div>

    <div class="highlight-box">
        <strong>💪 Your Health Mission:</strong><br>
        Track your workouts, nutrition, wellness, and mindfulness activities. Earn points for daily progress, build streaks, and compete on global rankings!
    </div>
    
    <div style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="button">
            Verify Email & Start Your Journey
        </a>
    </div>

    <div class="content">
        <strong>What makes IOMEH different:</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li><strong>Global Rankings</strong> - Compete with health enthusiasts worldwide across seasons and years.</li>
            <li><strong>Consistency Points</strong> - Earn points for daily activities, build streaks, and stay motivated.</li>
            <li><strong>Holistic Health</strong> - Track workouts, nutrition, wellness, and mindfulness all in one place.</li>
            <li><strong>Community</strong> - Join others on their health journey and stay accountable.</li>
        </ul>
    </div>

    <div class="secondary-text">
        <em>Remember: You owe it to yourself to be healthy. Every activity counts towards a better you!</em>
    </div>
@endsection
