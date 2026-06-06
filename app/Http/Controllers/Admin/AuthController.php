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

            // One-time: migrate legacy session locale to user preference
            if ($request->session()->has('admin_locale') && empty($user->admin_locale)) {
                $sessionLocale = $request->session()->get('admin_locale');
                if (in_array($sessionLocale, ['ms', 'en'], true)) {
                    $user->forceFill(['admin_locale' => $sessionLocale])->save();
                }
                $request->session()->forget('admin_locale');
            }

            $user->update(['last_login' => now()]);
            
            // Check if user has admin privileges
            if (!in_array($user->role, ['admin', 'super_admin'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('flash.auth_no_admin_privileges'),
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => __('flash.auth_credentials_invalid'),
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

        return redirect()->route('dashboard')->with('success', __('flash.auth_admin_request_submitted'));
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $isWriter = $user && $user->role === 'writer';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $isWriter
            ? redirect()->route('home')
            : redirect()->route('admin.login');
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
            return back()->with('error', __('flash.auth_email_not_found'));
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success', __('flash.auth_reset_link_sent'))
                    : back()->with('error', __('flash.auth_email_not_found'));
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
            return back()->with('error', __('flash.auth_email_not_found'));
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
                    ? redirect()->route('admin.login')->with('success', __('flash.auth_password_reset_success'))
                    : back()->with('error', __('flash.auth_reset_token_invalid'));
    }
} 