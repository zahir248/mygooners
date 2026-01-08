<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Mobile API: Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $user->update(['last_login' => now()]);
            
            // Create token for mobile app
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Log masuk berjaya',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Maklumat log masuk yang diberikan tidak sepadan dengan rekod kami.',
        ], 401);
    }

    /**
     * Mobile API: Register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal. Sila semak maklumat yang dimasukkan.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'trust_score' => 0.0,
            'is_verified' => false,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Akaun berjaya dicipta!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }

    /**
     * Mobile API: Login with Google
     */
    public function loginWithGoogle(Request $request)
    {
        try {
            $request->validate([
                'google_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string',
                'photo_url' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak sah: ' . implode(', ', $e->errors()),
            ], 422);
        }

        try {
            // Find user by google_id first, then by email
            $user = User::where('google_id', $request->google_id)->first();
            
            if (!$user) {
                // If not found by google_id, try by email
                $user = User::where('email', $request->email)->first();
            }

            if ($user) {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $request->google_id]);
                }
                
                // Update profile image if available and not set
                if ($request->photo_url && !$user->profile_image) {
                    $user->update(['profile_image' => $request->photo_url]);
                }
                
                // Update name if changed
                if ($user->name !== $request->name) {
                    $user->update(['name' => $request->name]);
                }
                
                // Update last login
                $user->update(['last_login' => now()]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'profile_image' => $request->photo_url,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                    'trust_score' => 0.0,
                    'is_verified' => true,
                ]);
            }

            // Create token for mobile app
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Log masuk Google berjaya',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Google Login Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ralat berlaku semasa log masuk dengan Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mobile API: Forgot Password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Pautan reset kata laluan telah dihantar ke alamat emel anda.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kami tidak dapat mencari pengguna dengan alamat emel tersebut.',
        ], 404);
    }

    /**
     * Mobile API: Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anda telah berjaya log keluar.',
        ], 200);
    }
}

