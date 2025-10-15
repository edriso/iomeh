<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::confirmPasswordView(fn () => Inertia::render('auth/ConfirmPassword'));

        // Custom authentication logic to support email OR username
        Fortify::authenticateUsing(function (Request $request) {
            $loginField = $request->input('email');
            $password = $request->input('password');
            
            // Determine if the input is an email or username
            $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // Authenticate with email (case-insensitive)
                $user = User::where('email', strtolower($loginField))->first();
            } else {
                // Authenticate with username (case-insensitive)
                $user = User::where('username', strtolower($loginField))->first();
            }
            
            if ($user && Hash::check($password, $user->password)) {
                return $user;
            }
            
            return null;
        });
    }
}
