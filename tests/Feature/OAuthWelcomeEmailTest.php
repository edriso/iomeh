<?php

use App\Mail\OAuthWelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

it('can create oauth welcome email with password reset url', function () {
    Mail::fake();
    
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
        'password' => null, // OAuth user
    ]);
    
    // Generate password reset token and URL
    $passwordResetToken = Password::createToken($user);
    $passwordResetUrl = url("/reset-password/{$passwordResetToken}?email=" . urlencode($user->email));
    
    // Create and send email
    $email = new OAuthWelcomeEmail($user, 'Google', $passwordResetUrl);
    Mail::to($user->email)->send($email);
    
    // Assert email was sent
    Mail::assertSent(OAuthWelcomeEmail::class, function ($mail) use ($user, $passwordResetUrl) {
        return $mail->user->email === $user->email &&
               $mail->provider === 'Google' &&
               $mail->passwordResetUrl === $passwordResetUrl;
    });
    
    // Assert the email contains the expected content
    $emailHtml = $email->render();
    expect($emailHtml)->toContain('Welcome to IOMeH');
    expect($emailHtml)->toContain('Set Up Password (Optional)');
    expect($emailHtml)->toContain($passwordResetUrl);
});

it('oauth welcome email works without password reset url', function () {
    Mail::fake();
    
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
    
    // Create email without password reset URL
    $email = new OAuthWelcomeEmail($user, 'Google');
    Mail::to($user->email)->send($email);
    
    // Assert email was sent
    Mail::assertSent(OAuthWelcomeEmail::class);
    
    // Assert the email doesn't contain backup password section
    $emailHtml = $email->render();
    expect($emailHtml)->toContain('Welcome to IOMeH');
    expect($emailHtml)->not->toContain('Set Up Password (Optional)');
});