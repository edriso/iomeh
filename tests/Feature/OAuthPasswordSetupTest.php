<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('oauth user can set up backup password while authenticated', function () {
    // Create OAuth user (no password)
    $user = User::factory()->create([
        'email' => 'oauth@example.com',
        'password' => null,
        'name' => 'OAuth User',
    ]);
    
    // Log in the OAuth user (simulating OAuth login)
    Auth::login($user);
    
    // Generate password reset token
    $token = Password::createToken($user);
    
    // Visit the password reset page (should work for authenticated users now)
    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('auth/ResetPassword')
            ->has('email')
            ->has('token')
    );
    
    // Set up new password
    $newPassword = 'new-secure-password';
    $response = $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);
    
    // Should redirect to profile (not login) since user is authenticated
    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'Password has been set up successfully! You can now use email/password login as a backup method.');
    
    // Verify password was actually set
    $user->refresh();
    expect(Hash::check($newPassword, $user->password))->toBeTrue();
    
    // User should still be authenticated
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

it('guest user password reset flow still works normally', function () {
    // Create user with existing password
    $user = User::factory()->create([
        'email' => 'regular@example.com',
        'password' => Hash::make('old-password'),
    ]);
    
    // Generate password reset token
    $token = Password::createToken($user);
    
    // Visit password reset page as guest
    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));
    
    $response->assertStatus(200);
    
    // Reset password
    $newPassword = 'new-password';
    $response = $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);
    
    // Should redirect to login (normal flow)
    $response->assertRedirect(route('login'));
    
    // Verify password was changed
    $user->refresh();
    expect(Hash::check($newPassword, $user->password))->toBeTrue();
});