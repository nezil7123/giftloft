<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Send the user off to Google's consent screen.
     */
    public function redirectToProvider()
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')
                ->with('error', 'Google sign-in is not available right now. Please sign in with your email and password.');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the return trip from Google.
     */
    public function handleProviderCallback(Request $request)
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')
                ->with('error', 'Google sign-in is not available right now. Please sign in with your email and password.');
        }

        // The user can decline consent, and the OAuth state can expire if the
        // consent screen sat open too long. Neither should be a 500 page.
        try {
            // Deliberately NOT stateless(): Socialite then verifies the `state`
            // parameter it set during the redirect, which is what stops an
            // attacker replaying a callback to log someone into their account.
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', 'We could not complete your Google sign-in. Please try again.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')
                ->with('error', 'Your Google account did not share an email address, so we could not sign you in.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $byEmail = User::where('email', $email)->first();

            if ($byEmail) {
                // Linking an existing account is only safe when Google vouches
                // for the address; otherwise anyone able to put an arbitrary
                // email on a Google profile could seize someone else's account.
                if (! $this->emailIsVerifiedByGoogle($googleUser)) {
                    return redirect()->route('login')->with(
                        'error',
                        'An account already uses this email address. Please sign in with your password to continue.'
                    );
                }

                $user = $byEmail;
            }
        }

        if ($user) {
            // email_verified_at and avatar are not mass-assignable on User,
            // so they have to be set explicitly rather than via update().
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar_url' => $user->avatar_url ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        } else {
            $user = new User();
            $user->forceFill([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $email,
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar_url' => $googleUser->getAvatar(),
                // Placeholder so the column is never null; the user signs in
                // with Google, and can set a real password via "forgot password".
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                // Set explicitly: the column default is not populated on the
                // in-memory model after an insert, and the is_active check below
                // would otherwise reject every brand-new account.
                'is_active' => true,
                'profile_visible' => true,
            ])->save();
        }

        if (! $user->is_active) {
            return redirect()->route('login')
                ->with('error', 'This account has been deactivated. Please contact support.');
        }

        Auth::login($user, true);

        // Same protection the password login gets: a fresh session ID so a
        // pre-login session cannot be fixated onto the authenticated user.
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Google sign-in only works once OAuth credentials are configured.
     */
    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }

    /**
     * Google returns `email_verified` alongside the profile; treat a missing
     * flag as unverified rather than assuming the address is trustworthy.
     */
    private function emailIsVerifiedByGoogle(object $googleUser): bool
    {
        return (bool) data_get($googleUser, 'user.email_verified', false);
    }
}
