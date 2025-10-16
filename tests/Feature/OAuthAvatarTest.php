<?php

use App\Models\User;
use App\Models\SocialLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

uses(RefreshDatabase::class);

it('sets avatar from Google OAuth during new user registration', function () {
    // Mock Google user
    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn('123456789');
    $googleUser->shouldReceive('getEmail')->andReturn('test@gmail.com');
    $googleUser->shouldReceive('getName')->andReturn('Test User');
    $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/test-avatar.jpg');
    $googleUser->token = 'fake-access-token';
    $googleUser->refreshToken = 'fake-refresh-token';
    $googleUser->expiresIn = 3600;

    // Mock Socialite
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock([
            'user' => $googleUser
        ]));

    // Simulate OAuth callback for registration
    session(['oauth_context' => 'register']);
    
    $response = $this->get('/auth/google/callback');

    // Verify user was created with avatar
    $user = User::where('email', 'test@gmail.com')->first();
    expect($user)->not()->toBeNull();
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/test-avatar.jpg');
    expect($user->name)->toBe('Test User');

    // Verify social login was created
    $socialLogin = SocialLogin::where('user_id', $user->id)->first();
    expect($socialLogin)->not()->toBeNull();
    expect($socialLogin->provider)->toBe('google');
    expect($socialLogin->provider_id)->toBe('123456789');
});

it('updates avatar from Google OAuth during existing user login', function () {
    // Create existing user
    $user = User::factory()->create([
        'email' => 'test@gmail.com',
        'avatar' => 'https://old-avatar.com/image.jpg'
    ]);

    // Create social login for user
    SocialLogin::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_id' => '123456789',
        'token' => 'old-token',
    ]);

    // Mock Google user with new avatar
    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn('123456789');
    $googleUser->shouldReceive('getEmail')->andReturn('test@gmail.com');
    $googleUser->shouldReceive('getName')->andReturn('Test User');
    $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/new-avatar.jpg');
    $googleUser->token = 'new-access-token';
    $googleUser->refreshToken = 'new-refresh-token';
    $googleUser->expiresIn = 3600;

    // Mock Socialite
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock([
            'user' => $googleUser
        ]));

    // Simulate OAuth callback for login
    session(['oauth_context' => 'login']);
    
    $response = $this->get('/auth/google/callback');

    // Verify avatar was updated
    $user->refresh();
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/new-avatar.jpg');
    
    // Verify user is authenticated
    expect(auth()->id())->toBe($user->id);
});

it('sets avatar when linking Google account to existing user', function () {
    // Create existing user without avatar
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'avatar' => null
    ]);

    // Authenticate the user
    $this->actingAs($user);

    // Mock Google user
    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn('123456789');
    $googleUser->shouldReceive('getEmail')->andReturn('test@gmail.com');
    $googleUser->shouldReceive('getName')->andReturn('Test User');
    $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/linked-avatar.jpg');
    $googleUser->token = 'access-token';
    $googleUser->refreshToken = 'refresh-token';
    $googleUser->expiresIn = 3600;

    // Mock Socialite
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock([
            'user' => $googleUser
        ]));

    // Simulate linking account callback
    $response = $this->get('/link/google/callback');

    // Verify avatar was set
    $user->refresh();
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/linked-avatar.jpg');

    // Verify social login was created
    $socialLogin = SocialLogin::where('user_id', $user->id)->first();
    expect($socialLogin)->not()->toBeNull();
    expect($socialLogin->provider)->toBe('google');
});