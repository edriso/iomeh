<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'username' => 'testuser',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->username)->toBe('testuser');
});

test('profile update does not affect email verification status', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'username' => 'testuser',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    // Email verification status should remain unchanged
    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

test('user can upload profile picture regardless of points', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'username' => 'testuser',
            'avatar' => 'https://example.com/profile.jpg',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->avatar)->toBe('https://example.com/profile.jpg');
});

test('profile update clears user caches', function () {
    $user = User::factory()->create();
    
    // Pre-populate caches to simulate existing cached data
    $homeCacheKey = "home_data_user_{$user->id}";
    $rankingsCacheKey = "rankings_page_{$user->id}";
    
    Cache::put($homeCacheKey, ['cached' => 'data'], 300);
    Cache::put($rankingsCacheKey, ['cached' => 'rankings'], 300);
    
    // Verify caches exist
    expect(Cache::has($homeCacheKey))->toBeTrue();
    expect(Cache::has($rankingsCacheKey))->toBeTrue();
    
    // Update profile with new avatar
    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'avatar' => 'https://example.com/new-avatar.jpg',
        ]);
    
    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));
    
    // Verify caches are cleared
    expect(Cache::has($homeCacheKey))->toBeFalse();
    expect(Cache::has($rankingsCacheKey))->toBeFalse();
    
    // Verify profile was updated
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->username)->toBe('updateduser');
    expect($user->avatar)->toBe('https://example.com/new-avatar.jpg');
});

test('profile update without avatar also clears caches', function () {
    $user = User::factory()->create();
    
    // Pre-populate caches
    $homeCacheKey = "home_data_user_{$user->id}";
    $rankingsCacheKey = "rankings_page_{$user->id}";
    
    Cache::put($homeCacheKey, ['cached' => 'data'], 300);
    Cache::put($rankingsCacheKey, ['cached' => 'rankings'], 300);
    
    // Update profile without avatar
    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name Only',
            'username' => 'nameonly',
        ]);
    
    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));
    
    // Verify caches are cleared even without avatar update
    expect(Cache::has($homeCacheKey))->toBeFalse();
    expect(Cache::has($rankingsCacheKey))->toBeFalse();
});