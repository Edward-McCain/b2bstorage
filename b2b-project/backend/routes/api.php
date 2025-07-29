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
use App\Http\Controllers\ProductTransferController;
use App\Http\Controllers\ProductBalanceController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AdminController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes - упрощенная версия без проблемных middleware
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Notification routes - временно вынесены для тестирования
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread']);
Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
Route::put('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
Route::put('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy']);

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
    // Новый маршрут массового импорта с оприходованием
    Route::post('/products/import-with-receipt', [\App\Http\Controllers\ProductController::class, 'importWithReceipt']);
    // Product operations logs
    Route::get('/product-operations', [\App\Http\Controllers\ProductController::class, 'getProductOperations']);
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
    Route::post('/inventories/calculate-balances', [InventoryController::class, 'calculateBalances']);
    // Inventory file routes
    Route::post('/inventory-files/upload', [InventoryFileController::class, 'upload']);
    Route::post('/inventory-files/upload-draft', [InventoryFileController::class, 'uploadDraft']);
    Route::post('/inventory-files/upload-item-photo', [InventoryFileController::class, 'uploadItemPhoto']);
    Route::get('/inventory-files/{id}', [InventoryFileController::class, 'show']);
    Route::delete('/inventory-files/{id}', [InventoryFileController::class, 'destroy']);
    // Warehouse routes
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
    Route::post('/warehouses', [WarehouseController::class, 'store']);
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);
    // Product transfer routes
    Route::get('/transfers', [ProductTransferController::class, 'index']);
    Route::post('/transfers/filter', [ProductTransferController::class, 'filter']);
    Route::post('/transfers/available-products', [ProductTransferController::class, 'getAvailableProducts']);
    Route::get('/transfers/all-products', [ProductTransferController::class, 'getAllProducts']);
    Route::get('/transfers/{id}', [ProductTransferController::class, 'show']);
    Route::post('/transfers', [ProductTransferController::class, 'store']);
    Route::put('/transfers/{id}', [ProductTransferController::class, 'update']);
    Route::delete('/transfers/{id}', [ProductTransferController::class, 'destroy']);
    Route::post('/transfers/{id}/confirm', [ProductTransferController::class, 'confirm']);
    Route::post('/transfers/{id}/complete', [ProductTransferController::class, 'complete']);
    Route::post('/transfers/{id}/cancel', [ProductTransferController::class, 'cancel']);
    // Product balance routes
    Route::get('/balances', [ProductBalanceController::class, 'index']);
    Route::post('/balances', [ProductBalanceController::class, 'filter']);
    Route::get('/balances/summary', [ProductBalanceController::class, 'summary']);
    Route::post('/balances/summary', [ProductBalanceController::class, 'summary']);
    Route::get('/balances/by-warehouse', [ProductBalanceController::class, 'byWarehouse']);
    Route::get('/balances/by-product', [ProductBalanceController::class, 'byProduct']);
    Route::get('/balances/low-stock', [ProductBalanceController::class, 'lowStock']);
    Route::get('/balances/out-of-stock', [ProductBalanceController::class, 'outOfStock']);
    Route::get('/balances/movements', [ProductBalanceController::class, 'movements']);
    Route::post('/balances/movements', [ProductBalanceController::class, 'movements']);
    
    // Currency routes
    Route::get('/currencies', [CurrencyController::class, 'getRates']);
    Route::get('/currencies/fetch', [CurrencyController::class, 'fetchAndSaveRates']);
    Route::get('/currencies/type/{currency_type}', [CurrencyController::class, 'getRateByType']);
    Route::post('/currencies/convert', [CurrencyController::class, 'convert']);
    Route::get('/user/currency', [CurrencyController::class, 'getUserCurrency']);
    Route::put('/user/currency', [CurrencyController::class, 'updateUserCurrency']);
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::get('/recent-users', [AdminController::class, 'getRecentUsers']);
        Route::get('/products', [AdminController::class, 'getProducts']);
        Route::post('/products/search', [AdminController::class, 'searchProducts']);
        Route::get('/users/{id}', [AdminController::class, 'getUserDetails']);
        Route::get('/subcategories', [AdminController::class, 'getSubcategories']);
        Route::get('/warehouses', [AdminController::class, 'getWarehouses']);
        // Admin receipts routes
        Route::get('/receipts', [AdminController::class, 'getReceipts']);
        Route::get('/receipts/{id}', [AdminController::class, 'getReceiptDetails']);
        // Admin write-offs routes
        Route::get('/write-offs', [AdminController::class, 'getWriteOffs']);
        Route::get('/write-offs/{id}', [AdminController::class, 'getWriteOffDetails']);
        // Admin inventories routes
        Route::get('/inventories', [AdminController::class, 'getInventories']);
        Route::get('/inventories/{id}', [AdminController::class, 'getInventoryDetails']);
        // Admin balances routes
        Route::get('/balances', [AdminController::class, 'getBalances']);
        Route::post('/balances', [AdminController::class, 'getBalances']);
        Route::post('/balances/movements', [AdminController::class, 'getBalanceMovements']);
        // Admin warehouses routes
        Route::get('/warehouses', [AdminController::class, 'getWarehouses']);
        Route::get('/warehouses/{id}', [AdminController::class, 'getWarehouseDetails']);
    });

    // Product fields (custom fields) routes
    Route::get('/product-fields', [\App\Http\Controllers\ProductFieldController::class, 'index']);
    Route::post('/product-fields', [\App\Http\Controllers\ProductFieldController::class, 'store']);
    Route::put('/product-fields/{id}', [\App\Http\Controllers\ProductFieldController::class, 'update']);
    Route::delete('/product-fields/{id}', [\App\Http\Controllers\ProductFieldController::class, 'destroy']);
    // Product fields visibility (стандартные поля)
    Route::put('/user/product-fields-visibility', [\App\Http\Controllers\AuthController::class, 'updateProductFieldsVisibility']);
    
    // AI routes
    Route::post('/ai/analyze-stock', [\App\Http\Controllers\AIController::class, 'analyzeStockLevels']);
    Route::post('/ai/analyze-documents', [\App\Http\Controllers\AIController::class, 'analyzeDocuments']);
    Route::post('/ai/smart-search', [\App\Http\Controllers\AIController::class, 'smartSearch']);
    Route::post('/ai/forecast-stock', [\App\Http\Controllers\AIController::class, 'forecastStock']);
    Route::post('/ai/generate-recommendations', [\App\Http\Controllers\AIController::class, 'generateRecommendations']);
    Route::post('/ai/comprehensive-analysis', [\App\Http\Controllers\AIController::class, 'comprehensiveAnalysis']);
    
    // Card counts routes
    Route::get('/receipts/count', [\App\Http\Controllers\CardCountsController::class, 'receiptsCount']);
    Route::get('/write-offs/count', [\App\Http\Controllers\CardCountsController::class, 'writeOffsCount']);
    Route::get('/inventories/count', [\App\Http\Controllers\CardCountsController::class, 'inventoryCount']);
    Route::get('/transfers/count', [\App\Http\Controllers\CardCountsController::class, 'transfersCount']);
    Route::get('/balances/count', [\App\Http\Controllers\CardCountsController::class, 'balancesCount']);
    Route::get('/warehouses/count', [\App\Http\Controllers\CardCountsController::class, 'warehousesCount']);
    Route::get('/card-counts/all', [\App\Http\Controllers\CardCountsController::class, 'getAllCounts']);
});

// Админские маршруты для перемещений
Route::get('/admin/transfers', [AdminController::class, 'getTransfers']);
Route::get('/admin/transfers/{id}', [AdminController::class, 'getTransferDetails']);

// Статистика операций
Route::get('/statistics/operations', [\App\Http\Controllers\StatisticsController::class, 'getOperationsStatistics']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/subcategories', [CategoryController::class, 'subcategories']);
Route::get('/subcategories', [CategoryController::class, 'subcategories']); 

// API Documentation route
Route::get('/docs_api', [\App\Http\Controllers\ApiDocumentationController::class, 'index']);