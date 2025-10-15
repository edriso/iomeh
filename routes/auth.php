<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Social Authentication Routes
    Route::get('auth/{provider}/{context?}', [SocialAuthController::class, 'redirectToGoogle'])
        ->where('provider', 'google')
        ->where('context', 'login|register')
        ->name('social.redirect');

    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleGoogleCallback'])
        ->where('provider', 'google')
        ->name('social.callback');
});

// Password reset routes (available to both guest and authenticated users)
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Social Account Management Routes
    Route::get('link/{provider}', [SocialAuthController::class, 'linkAccount'])
        ->where('provider', 'google')
        ->name('social.link');

    Route::get('link/{provider}/callback', [SocialAuthController::class, 'handleLinkCallback'])
        ->where('provider', 'google')
        ->name('social.link.callback');

    Route::delete('unlink/{provider}', [SocialAuthController::class, 'unlinkAccount'])
        ->where('provider', 'google')
        ->name('social.unlink');
});
