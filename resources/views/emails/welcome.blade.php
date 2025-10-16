@extends('emails.layout')

@section('title', 'Welcome to IOMeW!')

@section('content')
    <div class="greeting">Welcome to IOMeW!</div>
    
    <div class="content">
        Hi <strong>{{ $user->name ?? $user->username }}</strong>,
    </div>
    
    <div class="content">
        Welcome to <strong>IOMeW - I Owe Me Wellness</strong>! You've just joined a global wellness community where <em>consistency beats intensity</em>. Your wellness journey starts now!
    </div>

    <div class="highlight-box">
        <strong>💪 Your Wellness Mission:</strong><br>
        Track your workouts, nutrition, wellness, and mindfulness activities. Earn points for daily progress, build streaks, and compete on global rankings!
    </div>
    
    <div style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="button">
            Verify Email & Start Your Journey
        </a>
    </div>

    <div class="content">
        <strong>What makes IOMeW different:</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li><strong>Global Rankings</strong> - Compete with wellness enthusiasts worldwide across seasons and years.</li>
            <li><strong>Consistency Points</strong> - Earn points for daily activities, build streaks, and stay motivated.</li>
            <li><strong>Holistic Wellness</strong> - Track workouts, nutrition, wellness, and mindfulness all in one place.</li>
            <li><strong>Community</strong> - Join others on their wellness journey and stay accountable.</li>
        </ul>
    </div>

    <div class="secondary-text">
        <em>Remember: You owe it to yourself to prioritize wellness. Every activity counts towards a better you!</em>
    </div>
@endsection
