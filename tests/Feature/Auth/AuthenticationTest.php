<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    // Get CSRF token from login page
    $loginPage = $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('users can authenticate using username', function () {
    $user = User::factory()->create([
        'username' => 'testuser123',
        'email' => 'test@example.com',
    ]);

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('login.store'), [
        'email' => $user->username,
        'password' => 'password',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('users can authenticate using email regardless of case', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('login.store'), [
        'email' => 'TEST@EXAMPLE.COM',
        'password' => 'password',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('users can authenticate using username regardless of case', function () {
    $user = User::factory()->create([
        'username' => 'testuser123',
    ]);

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('login.store'), [
        'email' => 'TESTUSER123',
        'password' => 'password',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
        '_token' => $csrfToken,
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->actingAs($user)->post(route('logout'), [
        '_token' => $csrfToken,
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('home'));
});

test('users are rate limited', function () {
    $user = User::factory()->create();

    RateLimiter::increment(implode('|', [$user->email, '127.0.0.1']), amount: 10);

    // Get CSRF token from login page
    $this->get(route('login'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasErrors('email');

    $errors = session('errors');

    $this->assertStringContainsString('Too many login attempts', $errors->first('email'));
});