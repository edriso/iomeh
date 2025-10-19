<?php

return [
    'required' => 'This field is required.',
    'email' => 'Please enter a valid email address.',
    'email.required' => 'Email address is required.',
    'email.email' => 'Please enter a valid email address.',
    'email.unique' => 'The email has already been taken.',
    'min' => [
        'string' => 'This field must be at least :min characters.',
    ],
    'max' => [
        'string' => 'This field must not exceed :max characters.',
    ],
    'confirmed' => 'The confirmation does not match.',
    'unique' => 'This value has already been taken.',
    'url' => 'Please enter a valid URL.',
    'regex' => 'This field format is invalid.',
    'integer' => 'This field must be a number.',
    'in' => 'The selected value is invalid.',
    
    // Custom validation messages
    'username' => [
        'min' => 'Username must be at least 3 characters long.',
        'regex' => 'Username can only contain letters, numbers, and underscores.',
        'unique' => 'The username has already been taken.',
    ],
    'name' => [
        'min' => 'Name must be at least 2 characters long.',
        'max' => 'Name must not exceed 100 characters.',
    ],
    'email_custom' => [
        'required' => 'Email address is required.',
        'valid' => 'Please enter a valid email address.',
        'unique' => 'The email has already been taken.',
    ],
    'password' => [
        'required' => 'Password is required.',
        'min' => 'Password must be at least 8 characters long.',
        'confirmed' => 'Passwords do not match.',
    ],
    'password_confirmation' => [
        'required' => 'Please confirm your password.',
    ],
    'bio' => [
        'max' => 'Bio must be 255 characters or less.',
    ],
    'website_url' => [
        'url' => 'Please enter a valid URL (e.g., https://example.com).',
    ],
    'avatar' => [
        'url' => 'Please enter a valid URL for your profile picture.',
    ],
    'email_or_username' => [
        'required' => 'Email address or username is required.',
    ],
    'auth' => [
        'failed' => 'These credentials do not match our records.',
        'throttle' => 'Too many login attempts. Please try again in :minutes minutes.',
    ],
    'habit_name' => [
        'max' => 'Name must be 100 characters or less.',
    ],
    'habit_notes' => [
        'max' => 'Notes must be 500 characters or less.',
    ],
];
