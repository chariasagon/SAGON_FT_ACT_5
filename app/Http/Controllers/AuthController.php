<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Import User model

class AuthController extends Controller
{
    // Show Login Form
    public function showLoginForm()
    {
        return view('login');
    }

    // Handle Login
    public function login(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'email' => 'required|email', // Treat 'email' as 'username' for validation
                'password' => 'required'
            ]);

                // Check if user exists in the database (modify 'email' to 'username' here)
                $user = User::where('username', $request->email)->first(); // Assuming 'username' column
                if (!$user) {
                    // If the user doesn't exist, return a message to register
                    return response()->json(['error' => 'User not found. Please register first.'], 404);
                }
    

                //ADDED NEW ACTIVITY 5 START 
            // Check if user exists in the database (modify 'email' to 'username' here)
            $user = User::where('username', $request->email)->first(); // Assuming 'username' column
            if (!$user) {
                // If the user doesn't exist, return a message to register
                return response()->json(['error' => 'Invalid username or password'], 401);
            }

            // END SA ADDED NEW ACTIVITY 5 

            // Try to authenticate the user using 'username' (which you are using for email)
            if (Auth::attempt([
                'username' => $request->email, // Use 'username' as login field
                'password' => $request->password
            ])) {
                // Log the login event
                LoginLog::create([
                    'user_id' => Auth::id(),
                    'login_method' => session('login_method', 'traditional'),
                    'is_successful' => true,
                    'ip_address' => $request->ip(),
                    'details' => 'User successfully logged in.',
                ]);

                return response()->json([
                    'message' => 'Login successful',
                    'redirect' => route('dashboard-analytics') 
                ], 200);
            }

            // Log the failed login attempt
            LoginLog::create([
                'user_id' => null,
                'login_method' => 'traditional',
                'is_successful' => false,
                'ip_address' => $request->ip(),
                'details' => 'Failed login attempt for username: ' . $request->email,
            ]);


            return response()->json(['message' => 'Invalid username or password'], 401);

        } catch (\Exception $e) {
            // Capture the exception with line number and file
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Display error with line number and file name
            return response()->json([
                'error' => 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()
            ], 500);
        }
    }

    // Logout function
    public function logout(Request $request)
    {
        if (Auth::check()) {
            // Log the logout action in the database
            LoginLog::create([
                'user_id' => Auth::id(),
                'login_method' => session('login_method', 'traditional'),  // Session value for login method
                'is_successful' => true,  // Successful logout
                'ip_address' => $request->ip(),
                'details' => 'User logged out successfully.',
            ]);
        }

        Auth::logout();  // Log the user out
        session()->flush();  // Clear session data

        // Redirect back to login page with a success message
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
