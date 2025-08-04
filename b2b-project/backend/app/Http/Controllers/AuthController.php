<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
                'currency' => 'UZS',
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

        if ($user->isBanned()) {
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
            'data' => $user
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

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|string', // base64 image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            
            // Decode base64 image
            $imageData = $request->avatar;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace('data:image/gif;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            
            $imageData = base64_decode($imageData);
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            // Generate unique filename
            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            $path = 'uploads/avatars/' . $filename;
            
            // Save to storage
            Storage::disk('public')->put($path, $imageData);
            
            // Get public URL - используем относительный путь для универсальности
            $avatarUrl = '/storage/uploads/avatars/' . $filename;
            
            // Update user avatar in database
            $user->update([
                'avatar_url' => $avatarUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_url' => $avatarUrl
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update personal data
     */
    public function updatePersonalData(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'firstName' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'country' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'timezone' => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|max:10|in:AUD,CAD,CHF,CNY,EUR,GBP,HKD,JPY,NZD,RUB,USD,UZS',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        
        if ($request->has('firstName')) {
            $updateData['first_name'] = $request->firstName;
            // $updateData['user_name'] = trim($request->firstName . ' ' . $user->last_name);
        }
        
        if ($request->has('position')) {
            $updateData['position'] = $request->position;
        }
        
        if ($request->has('phone')) {
            $updateData['phone_number'] = $request->phone;
        }
        
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        
        if ($request->has('country')) {
            $updateData['country'] = $request->country;
        }
        
        if ($request->has('city')) {
            $updateData['city'] = $request->city;
        }
        
        if ($request->has('timezone')) {
            $updateData['timezone'] = $request->timezone;
        }
        
        if ($request->has('currency')) {
            $updateData['currency'] = $request->currency;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Personal data updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update company data
     */
    public function updateCompanyData(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'inn' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        
        if ($request->has('name')) {
            $updateData['company_name'] = $request->name;
        }
        
        if ($request->has('inn')) {
            $updateData['inn'] = $request->inn;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Company data updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|string|same:newPassword',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if old password is correct
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Get user settings data
     */
    public function getUserSettings(Request $request)
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
                'personal' => [
                    'firstName' => $user->first_name,
                    'position' => $user->position,
                    'phone' => $user->phone_number,
                    'email' => $user->email,
                    'country' => $user->country,
                    'city' => $user->city,
                    'timezone' => $user->timezone,
                    'currency' => $user->currency,
                    'avatar_url' => $user->avatar_url,
                    'product_fields_visibility' => $user->product_fields_visibility,
                    'cats_type' => $user->cats_type ?? 'system',
                ],
                'company' => [
                    'name' => $user->company_name,
                    'inn' => $user->inn,
                ]
            ]
        ]);
    }

    /**
     * Обновить видимость стандартных полей товаров
     */
    public function updateProductFieldsVisibility(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'product_fields_visibility' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $user->product_fields_visibility = json_encode($request->product_fields_visibility);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Product fields visibility updated',
            'data' => [
                'product_fields_visibility' => $user->product_fields_visibility
            ]
        ]);
    }

    /**
     * Update user language
     */
    public function updateLanguage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:ru,en,uz,china'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->language = $request->language;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Language updated successfully',
                'data' => [
                    'language' => $user->language
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating language: ' . $e->getMessage()
            ], 500);
        }
    }
}
