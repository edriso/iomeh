<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class EmailController extends Controller
{
    /**
     * Show the user's email settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Email');
    }

    /**
     * Update the user's email.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'email_confirmation' => ['required', 'same:email'],
        ], [
            'current_password.required' => __('validation.password.required'),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.valid'),
            'email.unique' => __('validation.email.unique'),
            'email_confirmation.required' => __('validation.email.required'),
            'email_confirmation.same' => __('validation.confirmed'),
        ]);

        $user = $request->user();
        $user->email = $validated['email'];
        $user->email_verified_at = null; // Reset verification status
        $user->save();

        return back();
    }
}
