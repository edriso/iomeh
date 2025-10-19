<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => 'required|string|max:255|min:3|regex:/^[a-zA-Z0-9_]+$/',
            'avatar' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
        ], [
            'name.min' => __('validation.name.min'),
            'name.max' => __('validation.name.max'),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.valid'),
            'email.unique' => __('validation.email.unique'),
            'password.required' => __('validation.password.required'),
            'password.confirmed' => __('validation.password.confirmed'),
            'username.required' => __('validation.username.min'),
            'username.min' => __('validation.username.min'),
            'username.regex' => __('validation.username.regex'),
            'bio.max' => __('validation.bio.max'),
            'website_url.url' => __('validation.website_url.url'),
            'avatar.url' => __('validation.avatar.url'),
        ]);

        // Check for case-insensitive username uniqueness
        $existingUser = User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->first();
        if ($existingUser) {
            return back()->withErrors(['username' => __('validation.username.unique')]);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => strtolower($request->email), // Convert to lowercase
                'password' => Hash::make($request->password),
                'avatar' => $request->avatar,
                'bio' => $request->bio,
                'website_url' => $request->website_url,
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Handle unique constraint violations
            if (str_contains($e->getMessage(), 'users.email')) {
                return back()->withErrors(['email' => __('validation.email.unique')]);
            }
            if (str_contains($e->getMessage(), 'users.username')) {
                return back()->withErrors(['username' => __('validation.username.unique')]);
            }
            throw $e;
        }

        // Send custom welcome email with verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        
        Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user, $verificationUrl));

        Auth::login($user);

        return to_route('home');
    }
}
