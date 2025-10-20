<?php

use App\Models\User;
use App\Models\SocialLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery\MockInterface;

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

it('preserves existing avatar during Google OAuth login', function () {
    // Create existing user with avatar
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

    // Verify avatar was NOT updated (preserved)
    $user->refresh();
    expect($user->avatar)->toBe('https://old-avatar.com/image.jpg');
    
    // Verify user is authenticated
    $this->assertAuthenticated();
});

it('sets avatar from Google OAuth during login when user has no avatar', function () {
    // Create existing user without avatar
    $user = User::factory()->create([
        'email' => 'test@gmail.com',
        'avatar' => null
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

    // Verify avatar was set (user had no avatar)
    $user->refresh();
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/new-avatar.jpg');
    
    // Verify user is authenticated
    $this->assertAuthenticated();
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

it('does not clear user caches when avatar is not updated via OAuth login', function () {
    // Create existing user with avatar
    $user = User::factory()->create([
        'email' => 'test@gmail.com',
        'avatar' => 'https://old-avatar.com/image.jpg'
    ]);

    // Pre-populate caches to simulate existing cached data
    $homeCacheKey = "home_data_user_{$user->id}";
    $rankingsCacheKey = "rankings_page_{$user->id}";
    
    Cache::put($homeCacheKey, ['cached' => 'data'], 300);
    Cache::put($rankingsCacheKey, ['cached' => 'rankings'], 300);
    
    // Verify caches exist
    expect(Cache::has($homeCacheKey))->toBeTrue();
    expect(Cache::has($rankingsCacheKey))->toBeTrue();

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

    // Verify avatar was NOT updated (preserved)
    $user->refresh();
    expect($user->avatar)->toBe('https://old-avatar.com/image.jpg');
    
    // Verify caches are NOT cleared (since avatar wasn't updated)
    expect(Cache::has($homeCacheKey))->toBeTrue();
    expect(Cache::has($rankingsCacheKey))->toBeTrue();
    
    // Verify user is authenticated
    $this->assertAuthenticated();
});

it('clears user caches when avatar is updated via account linking', function () {
    // Create existing user without avatar
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'avatar' => null
    ]);

    // Pre-populate caches
    $homeCacheKey = "home_data_user_{$user->id}";
    $rankingsCacheKey = "rankings_page_{$user->id}";
    
    Cache::put($homeCacheKey, ['cached' => 'data'], 300);
    Cache::put($rankingsCacheKey, ['cached' => 'rankings'], 300);
    
    // Verify caches exist
    expect(Cache::has($homeCacheKey))->toBeTrue();
    expect(Cache::has($rankingsCacheKey))->toBeTrue();

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

    // Verify caches are cleared
    expect(Cache::has($homeCacheKey))->toBeFalse();
    expect(Cache::has($rankingsCacheKey))->toBeFalse();

    // Verify social login was created
    $socialLogin = SocialLogin::where('user_id', $user->id)->first();
    expect($socialLogin)->not()->toBeNull();
    expect($socialLogin->provider)->toBe('google');
});