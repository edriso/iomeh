<?php

namespace App\Providers;

use App\Mail\ResetPasswordEmail;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Override the default email verification notification
        VerifyEmailNotification::toMailUsing(function ($notifiable, $url) {
            return (new VerifyEmail($notifiable, $url))
                ->to($notifiable->email);
        });

        // Override the default password reset notification
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = URL::temporarySignedRoute(
                'password.reset',
                now()->addMinutes(60),
                ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]
            );
            
            return (new ResetPasswordEmail($notifiable, $url))
                ->to($notifiable->email);
        });

    }
}
