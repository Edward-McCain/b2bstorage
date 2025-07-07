<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes - упрощенная версия без проблемных middleware
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    // Avatar upload
    Route::post('/user/avatar', [AuthController::class, 'uploadAvatar']);
    // User settings routes
    Route::get('/user/settings', [AuthController::class, 'getUserSettings']);
    Route::put('/user/personal', [AuthController::class, 'updatePersonalData']);
    Route::put('/user/company', [AuthController::class, 'updateCompanyData']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    // Product routes
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index']);
    Route::get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
    Route::delete('/products/{id}', [\App\Http\Controllers\ProductController::class, 'destroy']);
    // Product image upload
    Route::post('/products/{id}/images', [\App\Http\Controllers\ProductController::class, 'uploadImage']);
    Route::get('/products/{id}/images', [\App\Http\Controllers\ProductController::class, 'getImages']);
    Route::delete('/products/images/{id}', [\App\Http\Controllers\ProductController::class, 'deleteImage']);
    // Product draft route
    Route::post('/products/draft', [\App\Http\Controllers\ProductController::class, 'storeDraft']);
    // Product update route
    Route::put('/products/{id}', [\App\Http\Controllers\ProductController::class, 'update']);
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/subcategories', [CategoryController::class, 'subcategories']);
Route::get('/subcategories', [CategoryController::class, 'subcategories']); 