<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'username' => 'testuser',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('new users cannot upload profile picture without enough points', function () {
    $response = $this->post(route('register.store'), [
        'username' => 'testuser',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'avatar' => 'https://example.com/profile.jpg',
    ]);

    $response->assertSessionHasErrors(['avatar']);
    $response->assertSessionHasErrors(['avatar' => 'You need at least 50 points to upload a profile picture.']);
});

test('username must be unique case-insensitively', function () {
    // Create first user with username 'TestUser'
    $firstResponse = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'TestUser',
    ]);

    // Verify first user was created successfully
    $firstResponse->assertRedirect(route('home', absolute: false));
    $this->assertDatabaseHas('users', ['username' => 'TestUser']);

    // Logout the first user
    auth()->logout();

    // Try to create second user with username 'testuser' (lowercase)
    $response = $this->post(route('register.store'), [
        'name' => 'Test User 2',
        'email' => 'test2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'testuser',
    ]);

    $response->assertSessionHasErrors(['username']);
});

test('username must be unique case-insensitively (uppercase)', function () {
    // Create first user with username 'testuser'
    $firstResponse = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'testuser',
    ]);

    // Verify first user was created successfully
    $firstResponse->assertRedirect(route('home', absolute: false));
    $this->assertDatabaseHas('users', ['username' => 'testuser']);

    // Logout the first user
    auth()->logout();

    // Try to create second user with username 'TESTUSER' (uppercase)
    $response = $this->post(route('register.store'), [
        'name' => 'Test User 2',
        'email' => 'test2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'TESTUSER',
    ]);

    $response->assertSessionHasErrors(['username']);
});