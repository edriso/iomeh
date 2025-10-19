<?php

use App\Http\Controllers\Settings\EmailController;
use App\Http\Controllers\Settings\HabitsController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/email', [EmailController::class, 'edit'])->name('email.edit');

    Route::put('settings/email', [EmailController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('email.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/account', function () {
        return Inertia::render('settings/Account');
    })->name('account.edit');

    Route::get('settings/habits', [HabitsController::class, 'edit'])->name('habits.edit');
    Route::put('settings/habits', [HabitsController::class, 'update'])->name('habits.update');

});
