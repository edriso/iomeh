<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OAuthWelcomeEmail;
use App\Models\SocialLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle(string $provider, string $context = 'login')
    {
        // Store the context in session to use in callback
        session(['oauth_context' => $context]);
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Prepare OAuth token data from Google user
     */
    private function getOAuthTokenData($googleUser): array
    {
        return [
            'provider' => 'google',
            'provider_id' => $googleUser->getId(),
            'token' => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'expires_at' => $googleUser->expiresIn ? now()->addSeconds($googleUser->expiresIn) : null,
        ];
    }

    /**
     * Ensure user email is verified (for OAuth users)
     */
    private function ensureEmailVerified(User $user): void
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        // Get context from session
        $context = session('oauth_context', 'login');
        
        // Clear the session data
        session()->forget('oauth_context');
        
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Validate required data from Google
            if (!$googleUser->getEmail()) {
                $errorMessage = $this->getTranslatedMessage('social.unable_retrieve_email');
                return redirect('/login')->with('error', $errorMessage);
            }
            
            // Check if user already has a social login with this provider
            $socialLogin = SocialLogin::where('provider', 'google')
                ->where('provider_id', $googleUser->getId())
                ->first();

            if ($socialLogin) {
                // User exists, update tokens only (not avatar on login)
                $socialLogin->update($this->getOAuthTokenData($googleUser));

                // Only update avatar if user doesn't have one (first time login)
                if (!$socialLogin->user->avatar) {
                    $socialLogin->user->update([
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                    // Clear user caches since avatar was updated
                    $this->clearUserCaches($socialLogin->user);
                }

                // Ensure email is verified since Google emails are verified
                $this->ensureEmailVerified($socialLogin->user);

                Auth::login($socialLogin->user);
                
                return redirect()->intended('/');
            }

            // Check if a user with this email already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // If trying to sign up with an existing email, inform them to login instead
                if ($context === 'register') {
                    $errorMessage = $this->getTranslatedMessage('social.account_exists_login');
                    return redirect('/login')->with('error', $errorMessage);
                }
                
                // Link the Google account to existing user
                $existingUser->socialLogins()->create($this->getOAuthTokenData($googleUser));

                // Only update avatar if user doesn't have one (first time linking)
                if (!$existingUser->avatar) {
                    $existingUser->update([
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                    // Clear user caches since avatar was updated
                    $this->clearUserCaches($existingUser);
                }

                // Mark email as verified since Google emails are verified
                $this->ensureEmailVerified($existingUser);

                Auth::login($existingUser);
                
                return redirect()->intended('/');
            }

            // Handle based on context
            if ($context === 'login') {
                // For login: Don't create new user, show error
                $errorMessage = $this->getTranslatedMessage('social.no_account_found');
                return redirect('/login')->with('error', $errorMessage);
            }

            // For register: Create new user
            $user = User::create([
                'username' => $this->generateUniqueUsername($googleUser->getName()),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null, // No password for OAuth users
            ]);

            // Set email as verified since Google emails are verified
            $this->ensureEmailVerified($user);

            $user->socialLogins()->create($this->getOAuthTokenData($googleUser));

            // Generate optional password reset token for backup login
            $passwordResetToken = Password::createToken($user);
            $passwordResetUrl = url("/reset-password/{$passwordResetToken}?email=" . urlencode($user->email));

            // Send OAuth welcome email with optional password setup
            Mail::to($user->email)->send(new OAuthWelcomeEmail($user, 'Google', $passwordResetUrl));

            Auth::login($user);
            
            return redirect()->intended('/');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $redirectTo = $context === 'register' ? '/register' : '/login';
            $action = $context === 'register' ? 'signing up' : 'logging in';
            $errorMessage = $this->getTranslatedMessage('social.session_expired');
            $errorMessage = str_replace(':action', $action, $errorMessage);
            return redirect($redirectTo)->with('error', $errorMessage);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorMessage = $this->getTranslatedMessage('social.auth_failed');
            return redirect('/login')->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $errorMessage = $this->getTranslatedMessage('social.unable_login');
            return redirect('/login')->with('error', $errorMessage);
        }
    }

    /**
     * Generate a unique username from the user's name
     */
    private function generateUniqueUsername(string $name): string
    {
        // Clean and prepare the base username
        $baseUsername = Str::slug(Str::lower($name));
        
        // If the slug is empty or too short, use a default
        if (empty($baseUsername) || strlen($baseUsername) < 3) {
            $baseUsername = 'user';
        }
        
        // Ensure it's not too long
        $baseUsername = Str::limit($baseUsername, 20, '');
        
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
            
            // Prevent infinite loops
            if ($counter > 9999) {
                $username = $baseUsername . Str::random(4);
                break;
            }
        }

        return $username;
    }

    /**
     * Link a social account to the currently authenticated user
     */
    public function linkAccount(Request $request, string $provider)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if ($provider !== 'google') {
            $errorMessage = $this->getTranslatedMessage('social.provider_not_supported');
            return redirect()->back()->with('error', $errorMessage);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle linking a social account callback
     */
    public function handleLinkCallback(string $provider)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if ($provider !== 'google') {
            $errorMessage = $this->getTranslatedMessage('social.provider_not_supported');
            return redirect()->back()->with('error', $errorMessage);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            /** @var User $user */
            $user = Auth::user();

            // Check if this social account is already linked to another user
            $existingSocialLogin = SocialLogin::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if ($existingSocialLogin && $existingSocialLogin->user_id !== $user->id) {
                $errorMessage = $this->getTranslatedMessage('social.account_already_linked');
                return redirect()->back()->with('error', $errorMessage);
            }

            // Check if user already has this provider linked
            $userSocialLogin = $user->socialLogins()->where('provider', $provider)->first();

            if ($userSocialLogin) {
                // Update existing social login
                $tokenData = $this->getOAuthTokenData($socialUser);
                unset($tokenData['provider']); // Don't update provider field
                $userSocialLogin->update($tokenData);
            } else {
                // Create new social login
                $user->socialLogins()->create($this->getOAuthTokenData($socialUser));
            }

            // Only update avatar if user doesn't have one (first time linking)
            if (!$user->avatar) {
                $user->update([
                    'avatar' => $socialUser->getAvatar(),
                ]);
                // Clear user caches since avatar was updated
                $this->clearUserCaches($user);
            }

            $successMessage = $this->getTranslatedMessage('success.social_linked');
            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            $errorMessage = $this->getTranslatedMessage('social.unable_link');
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Unlink a social account from the currently authenticated user
     */
    public function unlinkAccount(string $provider)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if ($provider !== 'google') {
            $errorMessage = $this->getTranslatedMessage('social.provider_not_supported');
            return redirect()->back()->with('error', $errorMessage);
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if user has a password or other social accounts before unlinking
        $socialLoginsCount = $user->socialLogins()->count();
        
        if (!$user->hasPassword() && $socialLoginsCount <= 1) {
            $errorMessage = $this->getTranslatedMessage('social.cannot_unlink_only_method');
            return redirect()->back()->with('error', $errorMessage);
        }

        $socialLogin = $user->socialLogins()->where('provider', $provider)->first();

        if (!$socialLogin) {
            $errorMessage = $this->getTranslatedMessage('social.no_linked_account');
            return redirect()->back()->with('error', $errorMessage);
        }

        $socialLogin->delete();

        $successMessage = $this->getTranslatedMessage('success.social_unlinked');
        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Clear user-related caches when profile is updated
     */
    private function clearUserCaches(User $user): void
    {
        // Clear the home page cache for this user
        $homeCacheKey = "home_data_user_{$user->id}";
        Cache::forget($homeCacheKey);
        
        // Clear the rankings cache for this user
        $rankingsCacheKey = "rankings_page_{$user->id}";
        Cache::forget($rankingsCacheKey);
    }

    /**
     * Get translated message manually since Laravel translation system is not working properly
     */
    private function getTranslatedMessage(string $key): string
    {
        $locale = app()->getLocale();
        $translations = include lang_path("{$locale}/messages.php");
        
        return $translations[$key] ?? $key;
    }
}
