<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'user_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'first_name' => $request->user_name,
                'last_name' => '',
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone_number' => '',
                'verified_email' => false,
                'phone_ok' => false,
                'password' => Hash::make($request->password),
                'fcm_token' => '',
                'fcm_token_android' => '',
                'position' => 'Пользователь',
                'is_online' => false,
                'country' => 'Россия',
                'city' => 'Москва',
                'avatar_url' => '',
                'banned' => false,
                'glink' => Str::random(10),
                'acc_type' => 'individual',
                'status_subscription' => 0,
                'timezone' => 'UTC',
                'last_logged_in' => now()->toISOString(),
                'language' => 'ru',
                'messages_language' => 'ru',
                'currency' => 'RUB',
                'balance' => 0,
                'ref_balance' => 0,
                'demo_balance' => 0,
                'bonus_balance' => 0,
                'inn' => '',
                'comp_pinfl' => 0,
                'comp_state' => false,
                'company_type' => '',
                'company_name' => '',
                'company_description' => '',
                'company_rating' => 0,
                'com_address' => '',
                'com_leader' => '',
                'comp_logo_url' => '',
                'comp_phone' => '',
                'comp_mail' => '',
                'comp_website_url' => '',
                'company_link' => '',
                'company_statuses' => '',
                'comp_verified' => 0,
                'comp_tariff' => 0,
                'deal_seen' => false,
                'notification_email' => true,
                'notification_email_deal' => true,
                'notification_email_system' => true,
                'notification_email_chat' => true,
                'notification_email_subscription' => true,
                'notification_sms_chat' => false,
                'notification_sms_custom' => false,
                'notification_sms_system' => false,
                'is_active' => true,
                'catch' => '',
                'moderated' => false,
                'referer' => '',
                'invite_link' => '',
                'deleted' => false,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->banned) {
            return response()->json([
                'success' => false,
                'message' => 'Account is banned or inactive'
            ], 403);
        }

        // Update last login time
        $user->update([
            'last_logged_in' => now()->toISOString(),
            'is_online' => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            $user->update(['is_online' => false]);
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user data
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'company_name' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'avatar_url' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'first_name', 'last_name', 'phone_number', 'position',
            'company_name', 'country', 'city', 'avatar_url'
        ]));

        if ($request->has('first_name') || $request->has('last_name')) {
            $user->update([
                'user_name' => trim($user->first_name . ' ' . $user->last_name)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
