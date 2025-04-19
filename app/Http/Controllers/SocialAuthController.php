<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    // Redirect to social provider (Google or Facebook)
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Handle callback from social provider
    public function callback($provider)
    {
        try {
            // Use stateless for local development or avoiding state mismatch errors
            // Without stateless, for default OAuth flow with sessions
            $socialUser = Socialite::driver($provider)->user();

            // Log raw user data for debugging
            Log::info('Social User Data:', (array) $socialUser);

            // Split full name into first, middle, and last names
            $nameParts = explode(' ', trim($socialUser->getName()));
            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
            $middleName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;

            // Update or create user based on email (used as username)
            $user = User::updateOrCreate(
                ['username' => $socialUser->getEmail()],
                [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => bcrypt(str()->random(16)), // Generate a random password
                ]
            );

            // Log the user in
            Auth::login($user);

            // Store login method in session
            session(['login_method' => $provider]);

            // Log successful login event
            LoginLog::create([
                'user_id' => $user->id,
                'login_method' => $provider,
                'is_successful' => true,
                'ip_address' => request()->ip(),
                'details' => "User logged in successfully via {$provider}.",
            ]);

            Log::info("User logged in via {$provider}", ['user' => $user->email]);

            // Redirect to the dashboard
            return redirect()->route('dashboard-analytics');
        } catch (\Exception $e) {
            // Log error details
            Log::error('Social login error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Log failed login attempt
            LoginLog::create([
                'user_id' => null,
                'login_method' => $provider,
                'is_successful' => false,
                'ip_address' => request()->ip(),
                'details' => "Failed login attempt via {$provider}. Error: " . $e->getMessage(),
            ]);

            // Redirect to login page with an error message
            return redirect()->route('login')->with('error', 'Login via ' . ucfirst($provider) . ' failed. Please try again.');
        }
    }

    // Social logout handler
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $loginMethod = session('login_method', 'unknown');

            LoginLog::create([
                'user_id' => $user->id,
                'login_method' => $loginMethod,
                'is_successful' => true,
                'ip_address' => $request->ip(),
                'details' => "User logged out successfully via {$loginMethod}.",
            ]);

            Log::info("User logged out via {$loginMethod}", ['user' => $user->email]);
        }

        Auth::logout();
        session()->flush();

        // Redirect to login page
        return redirect()->route('login');
    }
}
