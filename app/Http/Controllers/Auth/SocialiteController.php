<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        if ($provider === 'github') {
            return Socialite::driver($provider)->scopes(['user:email'])->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        try {
            // Retrieve user details from provider
            $socialUser = Socialite::driver($provider)->user();

            if (!$socialUser->getEmail()) {
                return redirect('/login')->with('error', "Failed to retrieve email address from your {$provider} account.");
            }

            // Find existing user by provider ID
            $user = User::where("{$provider}_id", $socialUser->getId())->first();

            if (!$user) {
                // Check if user already exists with the same email
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // Link provider to existing email account
                    $user->update([
                        "{$provider}_id" => $socialUser->getId(),
                        'avatar' => $user->avatar ?: $socialUser->getAvatar(),
                    ]);
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? ucfirst($provider) . ' User',
                        'email' => $socialUser->getEmail(),
                        "{$provider}_id" => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'password' => null, // OAuth user password is not required
                    ]);
                }
            } else {
                // Update avatar if it has changed
                if ($socialUser->getAvatar() && $user->avatar !== $socialUser->getAvatar()) {
                    $user->update([
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                }
            }

            // Log the user in
            Auth::login($user, true);

            // Redirect to dashboard
            return redirect('/dashboard');

        } catch (Exception $e) {
            Log::error("Socialite Login Error [{$provider}]: " . $e->getMessage(), [
                'exception' => $e
            ]);

            return redirect('/login')->with('error', "Authentication failed: Please try again or use another login method.");
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
