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
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat kembali! Anda telah berjaya log masuk.');
        }

        return back()->with('error', 'Maklumat log masuk yang diberikan tidak sepadan dengan rekod kami.')->onlyInput('email');
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
        ]);

        if ($validator->fails()) {
            // Check if it's an email uniqueness error
            if ($validator->errors()->has('email') && $validator->errors()->first('email') === 'The email has already been taken.') {
                return back()->with('error', 'Alamat emel ini telah digunakan.')->withInput($request->except('password', 'password_confirmation'));
            }
            
            // Check if it's a password confirmation error
            if ($validator->errors()->has('password') && $validator->errors()->first('password') === 'The password field confirmation does not match.') {
                return back()->with('error', 'Kata laluan dan pengesahan kata laluan tidak sepadan.')->withInput($request->except('password', 'password_confirmation'));
            }
            
            // Check if it's a password length error
            if ($validator->errors()->has('password') && $validator->errors()->first('password') === 'The password field must be at least 8 characters.') {
                return back()->with('error', 'Kata laluan mesti sekurang-kurangnya 8 aksara.')->withInput($request->except('password', 'password_confirmation'));
            }
            
            // For other validation errors, still use field errors
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
            ->with('success', 'Akaun berjaya dicipta! Sila log masuk untuk meneruskan.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'))
            ->with('success', 'Anda telah berjaya log keluar.');
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
                ->with('error', 'Tidak dapat menyambung ke Google. Sila cuba lagi.');
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
                        ->with('error', 'Alamat emel ini telah didaftarkan. Sila log masuk.');
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
                    ->with('success', 'Akaun berjaya dicipta! Sila log masuk untuk meneruskan.');
            }
            
            // Login Flow
            if (!$user) {
                return redirect()->route('register')
                    ->with('error', 'Akaun tidak dijumpai. Sila daftar terlebih dahulu.');
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'profile_image' => $googleUser->avatar
                ]);
            }
            
            Auth::login($user, true);
            return redirect()->route('dashboard')->with('success', 'Selamat kembali! Anda telah berjaya log masuk.');

        } catch (\Exception $e) {
            Log::error('Google Callback Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Tidak dapat log masuk dengan Google. ';
            if (app()->environment('local')) {
                $errorMessage .= $e->getMessage();
            } else {
                $errorMessage .= 'Sila cuba lagi.';
            }

            return redirect()->route('login')
                ->with('error', $errorMessage);
        }
    }
} 