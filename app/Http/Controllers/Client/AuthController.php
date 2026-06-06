<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            // Update last_login timestamp
            $user = Auth::user();

            if ($request->session()->has('client_locale') && empty($user->client_locale)) {
                $sessionLocale = $request->session()->get('client_locale');
                if (in_array($sessionLocale, ['ms', 'en'], true)) {
                    $user->forceFill(['client_locale' => $sessionLocale])->save();
                }
                $request->session()->forget('client_locale');
            }

            $user->update(['last_login' => now()]);
            
            // Redirect based on user role
            $redirectRoute = $this->getRedirectRouteForUser($user);
            return redirect()->intended($redirectRoute)
                ->with('success', __('client_messages.msg_fcf4bd49c1bc'));
        }

        return back()->with('error', __('client_messages.msg_5cf89aad9e8e'))->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => __('client_messages.msg_aa4ec1316875'),
            'password.confirmed' => __('client_messages.msg_e48eb8564ec7'),
            'password.min' => __('client_messages.msg_a022d2d5d04d'),
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('email')) {
                return back()->with('error', __('client_messages.msg_aa4ec1316875'))->withInput($request->except('password', 'password_confirmation'));
            }

            if ($validator->errors()->has('password')) {
                return back()->with('error', $validator->errors()->first('password'))->withInput($request->except('password', 'password_confirmation'));
            }

            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
            'trust_score' => 0.0,
            'is_verified' => false,
        ]);

        // Don't automatically log in the user
        // Auth::login($user);

        return redirect(route('login'))
            ->with('success', __('client_messages.msg_a77a22bd6874'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'))
            ->with('success', __('client_messages.msg_5ddb757ef2d2'));
    }

    public function redirectToGoogle()
    {
        try {
            // Set registration flag if coming from registration page
            if (url()->previous() === route('register')) {
                session(['google_registration' => true]);
            } else {
                session()->forget('google_registration');
            }

            $guzzleConfig = [
                'verify' => base_path('cacert.pem'),
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => true
                ]
            ];

            return Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client($guzzleConfig))
                ->with([
                    'prompt' => 'select_account',
                    'access_type' => 'offline',
                    'response_type' => 'code'
                ])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', __('client_messages.msg_4c96a0273344'));
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $guzzleConfig = [
                'verify' => base_path('cacert.pem'),
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => true
                ]
            ];

            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client($guzzleConfig))
                ->user();
            
            Log::info('Google User Data:', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar
            ]);

            // Check if we're in registration flow
            $isRegistering = session('google_registration', false);
            
            // Find user by email or google_id
            $user = User::where('email', $googleUser->email)
                       ->orWhere('google_id', $googleUser->id)
                       ->first();

            if ($isRegistering) {
                // Registration Flow
                if ($user) {
                    return redirect()->route('login')
                        ->with('error', __('client_messages.msg_691034536cc5'));
                }

                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'profile_image' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                    'trust_score' => 0.0,
                    'is_verified' => true,
                ]);

                // Don't automatically log in the user (same as manual registration)
                // Auth::login($user, true);
                session()->forget('google_registration');
                return redirect()->route('login')
                    ->with('success', __('client_messages.msg_a77a22bd6874'));
            }
            
            // Login Flow
            if (!$user) {
                return redirect()->route('register')
                    ->with('error', __('client_messages.msg_ccd0b6e21e95'));
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'profile_image' => $googleUser->avatar
                ]);
            }
            
            Auth::login($user, true);
            // Update last_login timestamp
            $user->update(['last_login' => now()]);
            
            // Redirect based on user role
            $redirectRoute = $this->getRedirectRouteForUser($user);
            return redirect($redirectRoute)->with('success', __('client_messages.msg_fcf4bd49c1bc'));

        } catch (\Exception $e) {
            Log::error('Google Callback Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Tidak dapat log masuk dengan Google. ';
            if (app()->environment('local')) {
                $errorMessage .= $e->getMessage();
            } else {
                $errorMessage .= __('flash.login_try_again');
            }

            return redirect()->route('login')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show the form to request a password reset link.
     */
    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success', __('client_messages.msg_0b9c42f58db5'))
                    : back()->with('error', __('client_messages.msg_96d671090228'));
    }

    /**
     * Show the form to reset password.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('client.auth.reset-password', ['token' => $token, 'email' => $request->email]);
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
                    ? redirect()->route('login')->with('success', __('client_messages.msg_1984b302e1b8'))
                    : back()->with('error', __('client_messages.msg_742ff73ddbd2'));
    }

    /**
     * Get the appropriate redirect route based on user role.
     * Writers are redirected to admin articles.
     *
     * @param User $user
     * @return string
     */
    private function getRedirectRouteForUser(User $user)
    {
        // If user has role 'writer', redirect to admin articles
        if ($user->role === 'writer') {
            return route('admin.articles.index');
        }
        
        // Default redirect to home page
        return route('home');
    }
} 