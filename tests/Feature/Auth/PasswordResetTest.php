<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    // Get CSRF token
    $this->get(route('password.request'));
    $csrfToken = $this->app['session']->token();

    $this->post(route('password.email'), [
        'email' => $user->email,
        '_token' => $csrfToken,
    ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    // Get CSRF token
    $this->get(route('password.request'));
    $csrfToken = $this->app['session']->token();

    $this->post(route('password.email'), [
        'email' => $user->email,
        '_token' => $csrfToken,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', $notification->token));

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    // Get CSRF token
    $this->get(route('password.request'));
    $csrfToken = $this->app['session']->token();

    $this->post(route('password.email'), [
        'email' => $user->email,
        '_token' => $csrfToken,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        // Get CSRF token for password reset
        $this->get(route('password.reset', $notification->token));
        $csrfToken = $this->app['session']->token();

        $response = $this->post(route('password.store'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            '_token' => $csrfToken,
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $user = User::factory()->create();

    // Get CSRF token
    $this->get(route('password.request'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('password.store'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasErrors('email');
});