<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptFileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WriteOffController;
use App\Http\Controllers\WriteOffFileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryFileController;

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
    Route::get('/user', [AuthController::class, 'me']); // Добавляем альтернативный маршрут
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
    // Receipt routes
    Route::get('/receipts', [ReceiptController::class, 'index']);
    Route::get('/receipts/{id}', [ReceiptController::class, 'show']);
    Route::post('/receipts', [ReceiptController::class, 'store']);
    Route::put('/receipts/{id}', [ReceiptController::class, 'update']);
    Route::delete('/receipts/{id}', [ReceiptController::class, 'destroy']);
    Route::post('/receipt-files', [ReceiptFileController::class, 'store']);
    Route::get('/receipt-files/{receiptId}', [ReceiptFileController::class, 'getFiles']);
    Route::delete('/receipt-files/{id}', [ReceiptFileController::class, 'destroy']);
    Route::post('/receipt-files/draft', [ReceiptFileController::class, 'storeDraft']);
    // Write-off routes
    Route::get('/write-offs', [WriteOffController::class, 'index']);
    Route::get('/write-offs/{id}', [WriteOffController::class, 'show']);
    Route::post('/write-offs', [WriteOffController::class, 'store']);
    Route::put('/write-offs/{id}', [WriteOffController::class, 'update']);
    Route::delete('/write-offs/{id}', [WriteOffController::class, 'destroy']);
    Route::post('/write-off-files', [WriteOffFileController::class, 'store']);
    Route::get('/write-off-files/{writeOffId}', [WriteOffFileController::class, 'getFiles']);
    Route::delete('/write-off-files/{id}', [WriteOffFileController::class, 'destroy']);
    Route::post('/write-off-files/draft', [WriteOffFileController::class, 'storeDraft']);
    // Inventory routes
    Route::get('/inventories', [InventoryController::class, 'index']);
    Route::get('/inventories/{id}', [InventoryController::class, 'show']);
    Route::post('/inventories', [InventoryController::class, 'store']);
    Route::put('/inventories/{id}', [InventoryController::class, 'update']);
    Route::delete('/inventories/{id}', [InventoryController::class, 'destroy']);
    Route::get('/inventories/{id}/export', [InventoryController::class, 'export']);
    // Inventory file routes
    Route::post('/inventory-files/upload', [InventoryFileController::class, 'upload']);
    Route::post('/inventory-files/upload-draft', [InventoryFileController::class, 'uploadDraft']);
    Route::get('/inventory-files/{id}', [InventoryFileController::class, 'show']);
    Route::delete('/inventory-files/{id}', [InventoryFileController::class, 'destroy']);
    // Warehouse routes
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
    Route::post('/warehouses', [WarehouseController::class, 'store']);
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/subcategories', [CategoryController::class, 'subcategories']);
Route::get('/subcategories', [CategoryController::class, 'subcategories']); 