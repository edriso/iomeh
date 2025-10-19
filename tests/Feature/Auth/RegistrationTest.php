<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Get CSRF token
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('register.store'), [
        'username' => 'testuser',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('new users can register with profile picture', function () {
    // Get CSRF token
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    $response = $this->post(route('register.store'), [
        'username' => 'testuser',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'avatar' => 'https://example.com/profile.jpg',
        '_token' => $csrfToken,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
    
    $this->assertDatabaseHas('users', [
        'username' => 'testuser',
        'avatar' => 'https://example.com/profile.jpg',
    ]);
});

test('username must be unique case-insensitively', function () {
    // Get CSRF token for first user
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    // Create first user with username 'TestUser'
    $firstResponse = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'TestUser',
        '_token' => $csrfToken,
    ]);

    // Verify first user was created successfully
    $firstResponse->assertRedirect(route('home', absolute: false));
    $this->assertDatabaseHas('users', ['username' => 'TestUser']);

    // Logout the first user
    auth()->logout();

    // Get CSRF token for second user
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    // Try to create second user with username 'testuser' (lowercase)
    $response = $this->post(route('register.store'), [
        'name' => 'Test User 2',
        'email' => 'test2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'testuser',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasErrors(['username']);
});

test('username must be unique case-insensitively (uppercase)', function () {
    // Get CSRF token for first user
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    // Create first user with username 'testuser'
    $firstResponse = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'testuser',
        '_token' => $csrfToken,
    ]);

    // Verify first user was created successfully
    $firstResponse->assertRedirect(route('home', absolute: false));
    $this->assertDatabaseHas('users', ['username' => 'testuser']);

    // Logout the first user
    auth()->logout();

    // Get CSRF token for second user
    $this->get(route('register'));
    $csrfToken = $this->app['session']->token();

    // Try to create second user with username 'TESTUSER' (uppercase)
    $response = $this->post(route('register.store'), [
        'name' => 'Test User 2',
        'email' => 'test2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'TESTUSER',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasErrors(['username']);
});