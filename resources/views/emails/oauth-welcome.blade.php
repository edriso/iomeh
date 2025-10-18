@extends('emails.layout')

@section('title', 'Welcome to IOMeH!')

@section('content')
    <div class="greeting">Welcome to IOMeH, {{ $user->name ?? $user->username }}! 🎉</div>
    
    <div class="content">
        Hi <strong>{{ $user->name ?? $user->username }}</strong>,
    </div>
    
    <div class="content">
        You've successfully joined <strong>IOMeH - I Owe Me Health</strong> using your {{ $provider }} account! We're thrilled to have you on this health journey where <em>consistency beats intensity</em>.
    </div>

    <div class="highlight-box">
        <h2 style="margin-top: 0; color: #c4e456;">You're All Set!</h2>
        <p style="margin-bottom: 25px;">Your account is ready to go. No email verification needed - you're signed in with {{ $provider }} and ready to start tracking your health!</p>
        
        <div style="text-align: center;">
            <a href="{{ $dashboardUrl }}" class="button">Start Your Health Journey</a>
        </div>
    </div>

    @if(!empty($passwordResetUrl))
        <div class="highlight-box" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 2px solid #334155;">
            <h2 style="margin-top: 0; color: #c4e456;">Optional: Set Up Backup Password</h2>
            <p style="color: #ffffff;">While you can always sign in with {{ $provider }}, you might want to set up a password as a backup login method.</p>
            
            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ $passwordResetUrl }}" class="button" style="background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);">Set Up Password (Optional)</a>
            </div>
            
            <div class="security-note">
                <strong>⚠️ Security Note:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This link expires in 60 minutes for your security</li>
                    <li>You can always set up a password later from your account settings</li>
                    <li>Your {{ $provider }} login will continue to work regardless</li>
                </ul>
            </div>
        </div>
    @endif

    <div class="content">
        <strong>What makes IOMeH different:</strong>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li><strong>Global Rankings</strong> - Compete with health enthusiasts worldwide across seasons and years.</li>
            <li><strong>Consistency Points</strong> - Earn points for daily activities, build streaks, and stay motivated.</li>
            <li><strong>Holistic Health</strong> - Track workouts, nutrition, wellness, and mindfulness all in one place.</li>
            <li><strong>Community</strong> - Join others on their health journey and stay accountable.</li>
        </ul>
    </div>

    <div class="secondary-text">
        <em>Remember: You owe it to yourself to prioritize health. Every activity counts towards a better you!</em>
    </div>

    <div class="content">
        <strong>Need help?</strong> We're here for you! Reply to this email or contact us at hello@iomeh.com
    </div>
@endsection