<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiDocumentationController extends Controller
{
    /**
     * Обработка запроса к /api-docs
     * Возвращает информацию о доступных API endpoints
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'API Documentation',
            'description' => 'This API provides endpoints for B2B Storage management system',
            'version' => '1.0',
            'base_url' => 'https://api.b2bstorage.ru',
            'frontend_docs_url' => 'https://b2bstorage.ru/api-docs',
            'available_endpoints' => [
                'auth' => [
                    'POST /api/register' => 'User registration',
                    'POST /api/login' => 'User login',
                    'POST /api/logout' => 'User logout (requires auth)',
                    'GET /api/me' => 'Get current user info (requires auth)',
                ],
                'products' => [
                    'GET /api/products' => 'Get all products (requires auth)',
                    'GET /api/products/{id}' => 'Get product by ID (requires auth)',
                    'POST /api/products/draft' => 'Create product draft (requires auth)',
                    'PUT /api/products/{id}' => 'Update product (requires auth)',
                    'DELETE /api/products/{id}' => 'Delete product (requires auth)',
                ],
                'receipts' => [
                    'GET /api/receipts' => 'Get all receipts (requires auth)',
                    'GET /api/receipts/{id}' => 'Get receipt by ID (requires auth)',
                    'POST /api/receipts' => 'Create receipt (requires auth)',
                    'PUT /api/receipts/{id}' => 'Update receipt (requires auth)',
                    'DELETE /api/receipts/{id}' => 'Delete receipt (requires auth)',
                ],
                'write-offs' => [
                    'GET /api/write-offs' => 'Get all write-offs (requires auth)',
                    'GET /api/write-offs/{id}' => 'Get write-off by ID (requires auth)',
                    'POST /api/write-offs' => 'Create write-off (requires auth)',
                    'PUT /api/write-offs/{id}' => 'Update write-off (requires auth)',
                    'DELETE /api/write-offs/{id}' => 'Delete write-off (requires auth)',
                ],
                'inventories' => [
                    'GET /api/inventories' => 'Get all inventories (requires auth)',
                    'GET /api/inventories/{id}' => 'Get inventory by ID (requires auth)',
                    'POST /api/inventories' => 'Create inventory (requires auth)',
                    'PUT /api/inventories/{id}' => 'Update inventory (requires auth)',
                    'DELETE /api/inventories/{id}' => 'Delete inventory (requires auth)',
                ],
                'warehouses' => [
                    'GET /api/warehouses' => 'Get all warehouses (requires auth)',
                    'GET /api/warehouses/{id}' => 'Get warehouse by ID (requires auth)',
                    'POST /api/warehouses' => 'Create warehouse (requires auth)',
                    'PUT /api/warehouses/{id}' => 'Update warehouse (requires auth)',
                    'DELETE /api/warehouses/{id}' => 'Delete warehouse (requires auth)',
                ],
                'transfers' => [
                    'GET /api/transfers' => 'Get all transfers (requires auth)',
                    'GET /api/transfers/{id}' => 'Get transfer by ID (requires auth)',
                    'POST /api/transfers' => 'Create transfer (requires auth)',
                    'PUT /api/transfers/{id}' => 'Update transfer (requires auth)',
                    'DELETE /api/transfers/{id}' => 'Delete transfer (requires auth)',
                ],
                'balances' => [
                    'GET /api/balances' => 'Get product balances (requires auth)',
                    'GET /api/balances/summary' => 'Get balance summary (requires auth)',
                    'GET /api/balances/movements' => 'Get balance movements (requires auth)',
                ],
                'currencies' => [
                    'GET /api/currencies' => 'Get currency rates',
                    'GET /api/currencies/fetch' => 'Fetch latest currency rates',
                    'POST /api/currencies/convert' => 'Convert currency (requires auth)',
                ],
                'categories' => [
                    'GET /api/categories' => 'Get all categories',
                    'GET /api/categories/{id}/subcategories' => 'Get subcategories for category',
                    'GET /api/subcategories' => 'Get all subcategories',
                ],
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'note' => 'Most endpoints require authentication. Get token via /api/login'
            ],
            'status' => 'active'
        ], 200);
    }
} 