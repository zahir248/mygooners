<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            // Update last_login timestamp
            $user->update(['last_login' => now()]);
            
            // Check if user has admin privileges
            if (!in_array($user->role, ['admin', 'super_admin'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the admin registration form.
     */
    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    /**
     * Handle admin registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'position' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'terms' => ['required', 'accepted'],
        ]);

        // Create user with 'user' role (pending admin approval)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Start as regular user
            'admin_request_data' => json_encode([
                'position' => $request->position,
                'reason' => $request->reason,
                'requested_at' => now(),
                'status' => 'pending'
            ])
        ]);

        // Log them in as a regular user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 
            'Your admin access request has been submitted and is pending review. You\'ve been given regular user access in the meantime.'
        );
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show the form to request a password reset link.
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the user exists and has admin privileges
        $user = User::where('email', $request->email)->first();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Kami tidak dapat mencari admin dengan alamat emel tersebut.');
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success', 'Pautan reset kata laluan telah dihantar ke alamat emel anda.')
                    : back()->with('error', 'Kami tidak dapat mencari admin dengan alamat emel tersebut.');
    }

    /**
     * Show the form to reset password.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if the user exists and has admin privileges
        $user = User::where('email', $request->email)->first();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Kami tidak dapat mencari admin dengan alamat emel tersebut.');
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('admin.login')->with('success', 'Kata laluan anda telah berjaya ditetapkan semula. Sila log masuk dengan kata laluan baharu.')
                    : back()->with('error', 'Token reset kata laluan tidak sah atau telah tamat tempoh.');
    }
} 