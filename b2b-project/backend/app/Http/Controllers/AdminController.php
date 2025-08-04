<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\ProductSklad;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductBalance; // Added this import
use App\Helpers\CategoryHelper;

class AdminController extends Controller
{
    /**
     * Получить список пользователей с поиском
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $query = User::query();
            
            // Поиск по имени, email, названию компании, ИНН
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('inn', 'like', "%{$search}%");
                });
            }
            
            // Фильтр по роли
            if ($request->has('role') && $request->role !== '') {
                $query->where('role', $request->role);
            }
            
            // Фильтр по статусу
            if ($request->has('status')) {
                switch ($request->status) {
                    case 'active':
                        $query->where('is_active', true);
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                    case 'banned':
                        $query->where('banned', true);
                        break;
                }
            }
            
            // Сортировка
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Пагинация
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);
            
            // Статистика
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'admin_users' => User::where('role', 1)->count(),
                'banned_users' => User::where('banned', true)->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                    ],
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка пользователей: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить общую статистику для админ-панели
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'online_users' => User::where('is_online', true)->count(),
                'total_products' => ProductSklad::count(),
                'total_warehouses' => Warehouse::count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статистики: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить последние регистрации пользователей
     */
    public function getRecentUsers(): JsonResponse
    {
        try {
            $recentUsers = User::orderBy('created_at', 'desc')
                ->limit(3)
                ->get(['id', 'user_name', 'first_name', 'email', 'company_name', 'created_at', 'avatar_url']);
            
            return response()->json([
                'success' => true,
                'data' => $recentUsers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении последних пользователей: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список всех товаров для админ-панели
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            // Максимально упрощаем запрос
            $query = ProductBalance::with(['product.user', 'product.images', 'warehouse']);
            
            // Поиск по наименованию, артикулу или ИНН компании
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('article', 'like', "%{$search}%");
                    });
                });
            }
            
            // Фильтр по складу
            if ($request->has('warehouse_id') && $request->warehouse_id !== '') {
                $query->where('warehouse_id', $request->warehouse_id);
            }
            
            // Фильтр по категории
            if ($request->has('category_id') && $request->category_id !== '') {
                $query->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('category', $request->category_id);
                });
            }
            
            // Фильтр по подкатегории
            if ($request->has('subcategory_id') && $request->subcategory_id !== '') {
                $query->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('subcategory', $request->subcategory_id);
                });
            }
            
            // Пагинация
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);
            
            // Преобразуем данные для фронтенда
            $productsWithData = collect($products->items())->map(function ($balance) {
                $product = $balance->product;
                
                // Получаем цену товара из самого товара
                $price = (float) ($product->price ?? 0);
                
                // Получаем изображение товара
                $imageUrl = null;
                if ($product->images && $product->images->count() > 0) {
                    $imageUrl = $this->transformImageUrl($product->images->first()->image_path);
                }
                
                // Получаем категорию и подкатегорию через связи
                $category = null;
                $subcategory = null;
                
                // Поскольку category и subcategory хранятся как строки (slug), получаем данные из таблиц
                if ($product->category) {
                    $category = DB::table('categories')
                        ->where('name', $product->category)
                        ->orWhere('name_ru', $product->category)
                        ->orWhere('name_en', $product->category)
                        ->orWhere('name_uz', $product->category)
                        ->orWhere('name_china', $product->category)
                        ->select('id as category_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                        ->first();
                    
                    if ($category) {
                        $category->name = CategoryHelper::getCategoryName($category, 'ru'); // Админка всегда на русском
                    }
                }
                
                if ($product->subcategory) {
                    $subcategory = DB::table('subcategories')
                        ->where('name', $product->subcategory)
                        ->orWhere('name_ru', $product->subcategory)
                        ->orWhere('name_en', $product->subcategory)
                        ->orWhere('name_uz', $product->subcategory)
                        ->orWhere('name_china', $product->subcategory)
                        ->select('id as subcategory_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                        ->first();
                    
                    if ($subcategory) {
                        $subcategory->name = CategoryHelper::getSubcategoryName($subcategory, 'ru'); // Админка всегда на русском
                    }
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'description' => $product->description,
                    'warehouse' => $balance->warehouse,
                    'category' => $category,
                    'subcategory' => $subcategory,
                    'quantity' => $balance->quantity,
                    'price' => $price,
                    'image_url' => $imageUrl,
                    'user' => $product->user,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ];
            });
            
            // Получаем списки для фильтров
            $warehouses = Warehouse::select('id', 'name')->get();
            $categories = Category::select('category_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')->orderBy('name_ru')->get();
            
            // Применяем правильные названия для админки (всегда русский)
            foreach ($categories as $category) {
                $category->name = CategoryHelper::getCategoryName($category, 'ru');
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $productsWithData,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                    ],
                    'filters' => [
                        'warehouses' => $warehouses,
                        'categories' => $categories
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('AdminController getProducts error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка товаров: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список всех товаров для админ-панели (POST)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        try {
            // Максимально упрощаем запрос
            $query = ProductBalance::with(['product.user', 'product.images', 'warehouse']);
            
            // Поиск по наименованию, артикулу или ИНН компании
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('article', 'like', "%{$search}%");
                    });
                });
            }
            
            // Фильтр по складу
            if ($request->has('warehouse_id') && $request->warehouse_id !== '') {
                $query->where('warehouse_id', $request->warehouse_id);
            }
            
            // Фильтр по категории
            if ($request->has('category_id') && $request->category_id !== '') {
                $query->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('category', $request->category_id);
                });
            }
            
            // Фильтр по подкатегории
            if ($request->has('subcategory_id') && $request->subcategory_id !== '') {
                $query->whereHas('product', function($productQuery) use ($request) {
                    $productQuery->where('subcategory', $request->subcategory_id);
                });
            }
            
            // Пагинация
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);
            
            // Преобразуем данные для фронтенда
            $productsWithData = collect($products->items())->map(function ($balance) {
                $product = $balance->product;
                
                // Получаем цену товара из самого товара
                $price = (float) ($product->price ?? 0);
                
                // Получаем изображение товара
                $imageUrl = null;
                if ($product->images && $product->images->count() > 0) {
                    $imageUrl = $this->transformImageUrl($product->images->first()->image_path);
                }
                
                // Получаем категорию и подкатегорию через связи
                $category = null;
                $subcategory = null;
                
                // Получаем данные из таблицы products_sklad по ID товара
                $productSklad = DB::table('products_sklad')
                    ->where('id', $balance->product_id)
                    ->select('category', 'subcategory')
                    ->first();
                
                // Получаем название категории по category_id из таблицы categories
                if ($productSklad && $productSklad->category) {
                    $category = DB::table('categories')
                        ->where('category_id', $productSklad->category)
                        ->select('category_id', 'name')
                        ->first();
                }
                
                // Получаем название подкатегории по subcategory_id из таблицы subcategories
                if ($productSklad && $productSklad->subcategory) {
                    $subcategory = DB::table('subcategories')
                        ->where('subcategory_id', $productSklad->subcategory)
                        ->select('subcategory_id', 'name')
                        ->first();
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'description' => $product->description,
                    'warehouse' => $balance->warehouse,
                    'category' => $category,
                    'subcategory' => $subcategory,
                    'quantity' => $balance->quantity,
                    'price' => $price,
                    'image_url' => $imageUrl,
                    'user' => $product->user,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $productsWithData,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('AdminController searchProducts error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске товаров: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Преобразовать URL изображения
     */
    private function transformImageUrl($imageUrl)
    {
        $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
        return $baseUrl . $imageUrl;
    }

    /**
     * Получить детальную информацию о пользователе
     */
    public function getUserDetails($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных пользователя: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить подкатегории по ID категории (для админки)
     */
    public function getSubcategories(Request $request): JsonResponse
    {
        try {
            $categoryId = $request->get('category_id');
            $categoryType = $request->get('category_type', 'system'); // system или user
            
            Log::info('AdminController getSubcategories called', [
                'category_id' => $categoryId,
                'category_type' => $categoryType,
                'request_params' => $request->all()
            ]);
            
            if (!$categoryId) {
                Log::warning('AdminController getSubcategories: category_id not provided');
                return response()->json([
                    'success' => false,
                    'message' => 'ID категории не указан'
                ], 400);
            }
            
            if ($categoryType === 'user') {
                // Получаем пользовательские подкатегории
                $subcategories = DB::table('user_subcategories')
                    ->select('subcategory_id', 'name')
                    ->where('category_id', $categoryId)
                    ->orderBy('name')
                    ->get();
            } else {
                // Получаем системные подкатегории с поддержкой многоязычности
                $subcategories = Subcategory::where('category_id', $categoryId)
                    ->select('subcategory_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                    ->orderBy('name_ru')
                    ->get();
                
                // Применяем правильные названия для админки (всегда русский)
                foreach ($subcategories as $subcategory) {
                    $subcategory->name = CategoryHelper::getSubcategoryName($subcategory, 'ru');
                }
            }
            
            Log::info('AdminController getSubcategories result', [
                'category_id' => $categoryId,
                'category_type' => $categoryType,
                'subcategories_count' => $subcategories->count(),
                'subcategories' => $subcategories->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $subcategories
            ]);
            
        } catch (\Exception $e) {
            Log::error('AdminController getSubcategories error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении подкатегорий: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить все склады для админ-панели
     */
    public function getWarehouses(): JsonResponse
    {
        try {
            $warehouses = Warehouse::with('user')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $warehouses
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении складов: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить детали склада для админ-панели
     */
    public function getWarehouseDetails($id): JsonResponse
    {
        try {
            $warehouse = Warehouse::with('user')->find($id);
            
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Склад не найден'
                ], 404);
            }

            // Получаем товары на складе
            $products = DB::table('product_balances as pb')
                ->leftJoin('products_sklad as p', 'pb.product_id', '=', 'p.id')
                ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
                ->select(
                    'pb.product_id',
                    'pb.quantity',
                    'p.name as product_name',
                    'p.article as product_article',
                    'p.description as product_description',
                    'u.user_name',
                    'u.first_name',
                    'u.company_name'
                )
                ->where('pb.warehouse_id', $id)
                ->where('pb.quantity', '>', 0)
                ->orderBy('pb.quantity', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'warehouse' => [
                        'id' => $warehouse->id,
                        'name' => $warehouse->name,
                        'address' => $warehouse->address,
                        'user' => [
                            'id' => $warehouse->user->id,
                            'user_name' => $warehouse->user->user_name,
                            'first_name' => $warehouse->user->first_name,
                            'company_name' => $warehouse->user->company_name
                        ]
                    ],
                    'products' => $products
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении склада: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список всех оприходований для админ-панели
     */
    public function getReceipts(Request $request): JsonResponse
    {
        try {
            $query = DB::table('receipts as r')
                ->leftJoin('warehouses as w', 'r.warehouse', '=', 'w.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->select(
                    'r.id', 'r.number', 'r.date', 'r.organization',
                    'r.warehouse as warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'r.status', 'r.total', 'r.created_by', 'r.user_id', 'r.is_posted',
                    'r.comment', 'r.overhead_costs', 'r.project', 'r.created_at', 'r.updated_at',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                );

            // Применяем фильтры
            if ($request->has('number') && !empty($request->number)) {
                $query->where('r.number', 'like', '%' . $request->number . '%');
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('r.date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('r.date', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->has('warehouse') && !empty($request->warehouse)) {
                $query->where('r.warehouse', $request->warehouse);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('r.status', $request->status);
            }

            // Поиск по пользователю
            if ($request->has('user_search') && !empty($request->user_search)) {
                $search = $request->user_search;
                $query->where(function($q) use ($search) {
                    $q->where('u.user_name', 'like', "%{$search}%")
                      ->orWhere('u.first_name', 'like', "%{$search}%")
                      ->orWhere('u.last_name', 'like', "%{$search}%")
                      ->orWhere('u.email', 'like', "%{$search}%")
                      ->orWhere('u.company_name', 'like', "%{$search}%")
                      ->orWhere('u.inn', 'like', "%{$search}%");
                });
            }

            $receipts = $query->orderBy('r.created_at', 'desc')->paginate(20);

            $data = collect($receipts->items())->map(function($receipt) {
                return [
                    'id' => $receipt->id,
                    'number' => $receipt->number,
                    'date' => $receipt->date,
                    'organization' => $receipt->organization,
                    'warehouse_id' => $receipt->warehouse_id,
                    'warehouse_name' => $receipt->warehouse_name ?? '',
                    'warehouse_address' => $receipt->warehouse_address ?? '',
                    'status' => $receipt->status,
                    'total' => $receipt->total,
                    'created_by' => $receipt->user_full_name ?? $receipt->created_by,
                    'user_id' => $receipt->user_id,
                    'is_posted' => $receipt->is_posted,
                    'comment' => $receipt->comment,
                    'overhead_costs' => $receipt->overhead_costs,
                    'project' => $receipt->project,
                    'created_at' => $receipt->created_at,
                    'updated_at' => $receipt->updated_at,
                    'user' => [
                        'id' => $receipt->user_id,
                        'user_name' => $receipt->user_name,
                        'first_name' => $receipt->first_name,
                        'last_name' => $receipt->last_name,
                        'email' => $receipt->email,
                        'phone_number' => $receipt->phone_number,
                        'company_name' => $receipt->company_name,
                        'inn' => $receipt->inn,
                        'full_name' => $receipt->user_full_name
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $receipts->currentPage(),
                    'last_page' => $receipts->lastPage(),
                    'per_page' => $receipts->perPage(),
                    'total' => $receipts->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getReceipts error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка оприходований: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить детали оприходования для админ-панели
     */
    public function getReceiptDetails($id): JsonResponse
    {
        try {
            // Получаем основную информацию об оприходовании с JOIN'ами
            $receipt = DB::table('receipts as r')
                ->leftJoin('warehouses as w', 'r.warehouse', '=', 'w.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->select(
                    'r.id', 'r.number', 'r.date', 'r.organization',
                    'r.warehouse as warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'r.status', 'r.total', 'r.created_by', 'r.user_id', 'r.is_posted',
                    'r.comment', 'r.overhead_costs', 'r.project', 'r.created_at', 'r.updated_at',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    'u.avatar_url',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                )
                ->where('r.id', $id)
                ->first();

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оприходование не найдено'
                ], 404);
            }

            // Получаем позиции
            $positions = DB::table('receipt_positions')
                ->where('receipt_id', $id)
                ->get();

            // Получаем файлы
            $files = DB::table('receipt_files')
                ->where('receipt_id', $id)
                ->get();

            // Формируем данные с информацией о пользователе и складе
            $data = [
                'id' => $receipt->id,
                'number' => $receipt->number,
                'date' => $receipt->date,
                'organization' => $receipt->organization,
                'project' => $receipt->project,
                'warehouse' => $receipt->warehouse_id,
                'warehouse_name' => $receipt->warehouse_name ?? '',
                'warehouse_address' => $receipt->warehouse_address ?? '',
                'status' => $receipt->status,
                'is_posted' => $receipt->is_posted,
                'comment' => $receipt->comment,
                'total' => $receipt->total,
                'overhead_costs' => $receipt->overhead_costs,
                'created_by' => $receipt->user_full_name ?? $receipt->created_by,
                'user_id' => $receipt->user_id,
                'created_at' => $receipt->created_at,
                'updated_at' => $receipt->updated_at,
                'user' => [
                    'id' => $receipt->user_id,
                    'user_name' => $receipt->user_name,
                    'first_name' => $receipt->first_name,
                    'last_name' => $receipt->last_name,
                    'email' => $receipt->email,
                    'phone_number' => $receipt->phone_number,
                    'company_name' => $receipt->company_name,
                    'inn' => $receipt->inn,
                    'avatar_url' => $receipt->avatar_url,
                    'full_name' => $receipt->user_full_name
                ],
                'positions' => $positions,
                'files' => $files
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getReceiptDetails error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении оприходования: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список всех списаний для админ-панели
     */
    public function getWriteOffs(Request $request): JsonResponse
    {
        try {
            $query = DB::table('write_offs as w')
                ->leftJoin('warehouses as wh', 'w.warehouse', '=', 'wh.id')
                ->leftJoin('users as u', 'w.user_id', '=', 'u.id')
                ->select(
                    'w.id', 'w.number', 'w.date', 'w.organization',
                    'w.warehouse as warehouse_id',
                    'wh.name as warehouse_name',
                    'wh.address as warehouse_address',
                    'w.status', 'w.total', 'w.created_by', 'w.user_id',
                    'w.comment', 'w.overhead_costs', 'w.project', 'w.created_at', 'w.updated_at',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                );

            // Применяем фильтры
            if ($request->has('number') && !empty($request->number)) {
                $query->where('w.number', 'like', '%' . $request->number . '%');
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('w.date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('w.date', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->has('warehouse') && !empty($request->warehouse)) {
                $query->where('w.warehouse', $request->warehouse);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('w.status', $request->status);
            }

            // Поиск по пользователю
            if ($request->has('user_search') && !empty($request->user_search)) {
                $search = $request->user_search;
                $query->where(function($q) use ($search) {
                    $q->where('u.user_name', 'like', "%{$search}%")
                      ->orWhere('u.first_name', 'like', "%{$search}%")
                      ->orWhere('u.last_name', 'like', "%{$search}%")
                      ->orWhere('u.email', 'like', "%{$search}%")
                      ->orWhere('u.company_name', 'like', "%{$search}%")
                      ->orWhere('u.inn', 'like', "%{$search}%");
                });
            }

            $writeOffs = $query->orderBy('w.created_at', 'desc')->paginate(20);

            $data = collect($writeOffs->items())->map(function($writeOff) {
                return [
                    'id' => $writeOff->id,
                    'number' => $writeOff->number,
                    'date' => $writeOff->date,
                    'organization' => $writeOff->organization,
                    'warehouse_id' => $writeOff->warehouse_id,
                    'warehouse_name' => $writeOff->warehouse_name ?? '',
                    'warehouse_address' => $writeOff->warehouse_address ?? '',
                    'status' => $writeOff->status,
                    'total' => $writeOff->total,
                    'created_by' => $writeOff->user_full_name ?? $writeOff->created_by,
                    'user_id' => $writeOff->user_id,
                    'comment' => $writeOff->comment,
                    'overhead_costs' => $writeOff->overhead_costs,
                    'project' => $writeOff->project,
                    'created_at' => $writeOff->created_at,
                    'updated_at' => $writeOff->updated_at,
                    'user' => [
                        'id' => $writeOff->user_id,
                        'user_name' => $writeOff->user_name,
                        'first_name' => $writeOff->first_name,
                        'last_name' => $writeOff->last_name,
                        'email' => $writeOff->email,
                        'phone_number' => $writeOff->phone_number,
                        'company_name' => $writeOff->company_name,
                        'inn' => $writeOff->inn,
                        'full_name' => $writeOff->user_full_name
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $writeOffs->currentPage(),
                    'last_page' => $writeOffs->lastPage(),
                    'per_page' => $writeOffs->perPage(),
                    'total' => $writeOffs->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getWriteOffs error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка списаний: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить детали списания для админ-панели
     */
    public function getWriteOffDetails($id): JsonResponse
    {
        try {
            // Получаем основную информацию о списании с JOIN'ами
            $writeOff = DB::table('write_offs as w')
                ->leftJoin('warehouses as wh', 'w.warehouse', '=', 'wh.id')
                ->leftJoin('users as u', 'w.user_id', '=', 'u.id')
                ->select(
                    'w.id', 'w.number', 'w.date', 'w.organization',
                    'w.warehouse as warehouse_id',
                    'wh.name as warehouse_name',
                    'wh.address as warehouse_address',
                    'w.status', 'w.total', 'w.created_by', 'w.user_id',
                    'w.comment', 'w.overhead_costs', 'w.project', 'w.created_at', 'w.updated_at',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    'u.avatar_url',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                )
                ->where('w.id', $id)
                ->first();

            if (!$writeOff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Списание не найдено'
                ], 404);
            }

            // Получаем позиции
            $positions = DB::table('write_off_positions')
                ->where('write_off_id', $id)
                ->get();

            // Получаем файлы
            $files = DB::table('write_off_files')
                ->where('write_off_id', $id)
                ->get();

            // Формируем данные с информацией о пользователе и складе
            $data = [
                'id' => $writeOff->id,
                'number' => $writeOff->number,
                'date' => $writeOff->date,
                'organization' => $writeOff->organization,
                'project' => $writeOff->project,
                'warehouse' => $writeOff->warehouse_id,
                'warehouse_name' => $writeOff->warehouse_name ?? '',
                'warehouse_address' => $writeOff->warehouse_address ?? '',
                'status' => $writeOff->status,
                'comment' => $writeOff->comment,
                'total' => $writeOff->total,
                'overhead_costs' => $writeOff->overhead_costs,
                'created_by' => $writeOff->user_full_name ?? $writeOff->created_by,
                'user_id' => $writeOff->user_id,
                'created_at' => $writeOff->created_at,
                'updated_at' => $writeOff->updated_at,
                'user' => [
                    'id' => $writeOff->user_id,
                    'user_name' => $writeOff->user_name,
                    'first_name' => $writeOff->first_name,
                    'last_name' => $writeOff->last_name,
                    'email' => $writeOff->email,
                    'phone_number' => $writeOff->phone_number,
                    'company_name' => $writeOff->company_name,
                    'inn' => $writeOff->inn,
                    'avatar_url' => $writeOff->avatar_url,
                    'full_name' => $writeOff->user_full_name
                ],
                'positions' => $positions,
                'files' => $files
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getWriteOffDetails error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списания: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список всех инвентаризаций для админ-панели
     */
    public function getInventories(Request $request): JsonResponse
    {
        try {
            $query = DB::table('inventories as i')
                ->leftJoin('warehouses as w', 'i.warehouse_id', '=', 'w.id')
                ->leftJoin('users as u', 'i.created_by', '=', 'u.id')
                ->select(
                    'i.id', 'i.name', 'i.description', 'i.warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'i.status', 'i.created_by', 'i.created_at', 'i.updated_at',
                    'i.completed_at', 'i.notes',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                );

            // Применяем фильтры
            if ($request->has('name') && !empty($request->name)) {
                $query->where('i.name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('i.created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('i.created_at', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->has('warehouse') && !empty($request->warehouse)) {
                $query->where('i.warehouse_id', $request->warehouse);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('i.status', $request->status);
            }

            // Поиск по пользователю
            if ($request->has('user_search') && !empty($request->user_search)) {
                $search = $request->user_search;
                $query->where(function($q) use ($search) {
                    $q->where('u.user_name', 'like', "%{$search}%")
                      ->orWhere('u.first_name', 'like', "%{$search}%")
                      ->orWhere('u.last_name', 'like', "%{$search}%")
                      ->orWhere('u.email', 'like', "%{$search}%")
                      ->orWhere('u.company_name', 'like', "%{$search}%")
                      ->orWhere('u.inn', 'like', "%{$search}%");
                });
            }

            $inventories = $query->orderBy('i.created_at', 'desc')->paginate(20);

            // Получаем дополнительную информацию для каждой инвентаризации
            $data = collect($inventories->items())->map(function($inventory) {
                // Получаем количество товаров
                $itemsCount = DB::table('inventory_items')
                    ->where('inventory_id', $inventory->id)
                    ->count();

                // Получаем количество расхождений
                $discrepanciesCount = DB::table('inventory_items')
                    ->where('inventory_id', $inventory->id)
                    ->whereRaw('(actual_quantity - calculated_quantity) != 0')
                    ->count();

                return [
                    'id' => $inventory->id,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'warehouse_id' => $inventory->warehouse_id,
                    'warehouse_name' => $inventory->warehouse_name ?? '',
                    'warehouse_address' => $inventory->warehouse_address ?? '',
                    'status' => $inventory->status,
                    'created_by' => $inventory->user_full_name ?? $inventory->first_name,
                    'user_id' => $inventory->user_id,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                    'completed_at' => $inventory->completed_at,
                    'notes' => $inventory->notes,
                    'items_count' => $itemsCount,
                    'discrepancies_count' => $discrepanciesCount,
                    'user' => [
                        'id' => $inventory->user_id,
                        'user_name' => $inventory->user_name,
                        'first_name' => $inventory->first_name,
                        'last_name' => $inventory->last_name,
                        'email' => $inventory->email,
                        'phone_number' => $inventory->phone_number,
                        'company_name' => $inventory->company_name,
                        'inn' => $inventory->inn,
                        'full_name' => $inventory->user_full_name
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $inventories->currentPage(),
                    'last_page' => $inventories->lastPage(),
                    'per_page' => $inventories->perPage(),
                    'total' => $inventories->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getInventories error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка инвентаризаций: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить детали инвентаризации для админ-панели
     */
    public function getInventoryDetails($id): JsonResponse
    {
        try {
            // Получаем основную информацию об инвентаризации с JOIN'ами
            $inventory = DB::table('inventories as i')
                ->leftJoin('warehouses as w', 'i.warehouse_id', '=', 'w.id')
                ->leftJoin('users as u', 'i.created_by', '=', 'u.id')
                ->select(
                    'i.id', 'i.name', 'i.description', 'i.warehouse_id',
                    'w.name as warehouse_name',
                    'w.address as warehouse_address',
                    'i.status', 'i.created_by', 'i.created_at', 'i.updated_at',
                    'i.completed_at', 'i.notes',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    'u.avatar_url',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name")
                )
                ->where('i.id', $id)
                ->first();

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Инвентаризация не найдена'
                ], 404);
            }

            // Получаем товары инвентаризации
            $items = DB::table('inventory_items as ii')
                ->leftJoin('products_sklad as p', 'ii.product_id', '=', 'p.id')
                ->select(
                    'ii.id', 'ii.product_id', 'ii.calculated_quantity', 'ii.actual_quantity',
                    'ii.notes', 'ii.created_at', 'ii.updated_at',
                    'p.name as product_name',
                    'p.article as product_sku',
                    DB::raw('(ii.actual_quantity - ii.calculated_quantity) as difference_quantity')
                )
                ->where('ii.inventory_id', $id)
                ->get();

            // Добавляем статус избытка/недостачи для каждого товара
            $items->each(function($item) {
                if ($item->difference_quantity > 0) {
                    $item->excess_shortage = 'excess';
                } elseif ($item->difference_quantity < 0) {
                    $item->excess_shortage = 'shortage';
                } else {
                    $item->excess_shortage = 'normal';
                }
            });

            // Получаем файлы
            $files = DB::table('inventory_files')
                ->where('inventory_id', $id)
                ->get();

            // Формируем данные с информацией о пользователе и складе
            $data = [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'description' => $inventory->description,
                'warehouse_id' => $inventory->warehouse_id,
                'warehouse_name' => $inventory->warehouse_name ?? '',
                'warehouse_address' => $inventory->warehouse_address ?? '',
                'status' => $inventory->status,
                'created_by' => $inventory->user_full_name ?? $inventory->first_name,
                'user_id' => $inventory->user_id,
                'created_at' => $inventory->created_at,
                'updated_at' => $inventory->updated_at,
                'completed_at' => $inventory->completed_at,
                'notes' => $inventory->notes,
                'user' => [
                    'id' => $inventory->user_id,
                    'user_name' => $inventory->user_name,
                    'first_name' => $inventory->first_name,
                    'last_name' => $inventory->last_name,
                    'email' => $inventory->email,
                    'phone_number' => $inventory->phone_number,
                    'company_name' => $inventory->company_name,
                    'inn' => $inventory->inn,
                    'avatar_url' => $inventory->avatar_url,
                    'full_name' => $inventory->user_full_name
                ],
                'items' => $items,
                'files' => $files
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getInventoryDetails error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении инвентаризации: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить список всех перемещений для админ-панели
     */
    public function getTransfers(Request $request): JsonResponse
    {
        try {
            $query = DB::table('product_transfers as pt')
                ->leftJoin('warehouses as fw', 'pt.from_warehouse_id', '=', 'fw.id')
                ->leftJoin('warehouses as tw', 'pt.to_warehouse_id', '=', 'tw.id')
                ->leftJoin('users as u', 'pt.created_by', '=', 'u.id')
                ->leftJoin('users as cu', 'pt.completed_by', '=', 'cu.id')
                ->select(
                    'pt.id', 'pt.from_warehouse_id', 'pt.to_warehouse_id', 'pt.transfer_date',
                    'pt.status', 'pt.notes', 'pt.created_by', 'pt.completed_by', 'pt.created_at',
                    'pt.updated_at', 'pt.completed_at',
                    'fw.name as from_warehouse_name',
                    'fw.address as from_warehouse_address',
                    'tw.name as to_warehouse_name',
                    'tw.address as to_warehouse_address',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    'cu.first_name as completed_by_first_name',
                    'cu.last_name as completed_by_last_name',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name"),
                    DB::raw("CONCAT(cu.first_name, ' ', cu.last_name) as completed_by_full_name")
                );

            // Применяем фильтры
            if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
                $query->where(function($q) use ($request) {
                    $q->where('pt.from_warehouse_id', $request->warehouse_id)
                      ->orWhere('pt.to_warehouse_id', $request->warehouse_id);
                });
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('pt.transfer_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('pt.transfer_date', '<=', $request->date_to);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('pt.status', $request->status);
            }

            // Поиск по пользователю
            if ($request->has('user_search') && !empty($request->user_search)) {
                $search = $request->user_search;
                $query->where(function($q) use ($search) {
                    $q->where('u.user_name', 'like', "%{$search}%")
                      ->orWhere('u.first_name', 'like', "%{$search}%")
                      ->orWhere('u.last_name', 'like', "%{$search}%")
                      ->orWhere('u.email', 'like', "%{$search}%")
                      ->orWhere('u.company_name', 'like', "%{$search}%")
                      ->orWhere('u.inn', 'like', "%{$search}%");
                });
            }

            $transfers = $query->orderBy('pt.created_at', 'desc')->paginate(20);

            $data = collect($transfers->items())->map(function($transfer) {
                // Получаем количество товаров
                $totalItems = DB::table('product_transfer_positions')
                    ->where('transfer_id', $transfer->id)
                    ->sum('quantity');

                $actualTotalItems = DB::table('product_transfer_positions')
                    ->where('transfer_id', $transfer->id)
                    ->sum('actual_quantity');

                return [
                    'id' => $transfer->id,
                    'from_warehouse_id' => $transfer->from_warehouse_id,
                    'from_warehouse_name' => $transfer->from_warehouse_name ?? '',
                    'from_warehouse_address' => $transfer->from_warehouse_address ?? '',
                    'to_warehouse_id' => $transfer->to_warehouse_id,
                    'to_warehouse_name' => $transfer->to_warehouse_name ?? '',
                    'to_warehouse_address' => $transfer->to_warehouse_address ?? '',
                    'transfer_date' => $transfer->transfer_date,
                    'status' => $transfer->status,
                    'status_text' => $this->getTransferStatusText($transfer->status),
                    'notes' => $transfer->notes,
                    'created_by' => $transfer->user_full_name ?? $transfer->first_name,
                    'completed_by' => $transfer->completed_by_full_name ?? '',
                    'created_at' => $transfer->created_at,
                    'updated_at' => $transfer->updated_at,
                    'completed_at' => $transfer->completed_at,
                    'total_items' => $totalItems,
                    'actual_total_items' => $actualTotalItems,
                    'user' => [
                        'id' => $transfer->user_id,
                        'user_name' => $transfer->user_name,
                        'first_name' => $transfer->first_name,
                        'last_name' => $transfer->last_name,
                        'email' => $transfer->email,
                        'phone_number' => $transfer->phone_number,
                        'company_name' => $transfer->company_name,
                        'inn' => $transfer->inn,
                        'full_name' => $transfer->user_full_name
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $transfers->currentPage(),
                    'last_page' => $transfers->lastPage(),
                    'per_page' => $transfers->perPage(),
                    'total' => $transfers->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getTransfers error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка перемещений: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить детали перемещения для админ-панели
     */
    public function getTransferDetails($id): JsonResponse
    {
        try {
            // Получаем основную информацию о перемещении с JOIN'ами
            $transfer = DB::table('product_transfers as pt')
                ->leftJoin('warehouses as fw', 'pt.from_warehouse_id', '=', 'fw.id')
                ->leftJoin('warehouses as tw', 'pt.to_warehouse_id', '=', 'tw.id')
                ->leftJoin('users as u', 'pt.created_by', '=', 'u.id')
                ->leftJoin('users as cu', 'pt.completed_by', '=', 'cu.id')
                ->select(
                    'pt.id', 'pt.from_warehouse_id', 'pt.to_warehouse_id', 'pt.transfer_date',
                    'pt.status', 'pt.notes', 'pt.created_by', 'pt.completed_by', 'pt.created_at',
                    'pt.updated_at', 'pt.completed_at',
                    'fw.name as from_warehouse_name',
                    'fw.address as from_warehouse_address',
                    'tw.name as to_warehouse_name',
                    'tw.address as to_warehouse_address',
                    'u.id as user_id',
                    'u.user_name',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.phone_number',
                    'u.company_name',
                    'u.inn',
                    'cu.first_name as completed_by_first_name',
                    'cu.last_name as completed_by_last_name',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_full_name"),
                    DB::raw("CONCAT(cu.first_name, ' ', cu.last_name) as completed_by_full_name")
                )
                ->where('pt.id', $id)
                ->first();

            if (!$transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Перемещение не найдено'
                ], 404);
            }

            // Получаем позиции перемещения
            $positions = DB::table('product_transfer_positions as ptp')
                ->leftJoin('products_sklad as p', 'ptp.product_id', '=', 'p.id')
                ->select(
                    'ptp.id', 'ptp.product_id', 'ptp.quantity', 'ptp.actual_quantity',
                    'ptp.notes', 'ptp.created_at', 'ptp.updated_at',
                    'p.name as product_name',
                    'p.article as product_sku'
                )
                ->where('ptp.transfer_id', $id)
                ->get();

            // Формируем данные с информацией о пользователе и складах
            $data = [
                'id' => $transfer->id,
                'from_warehouse_id' => $transfer->from_warehouse_id,
                'from_warehouse_name' => $transfer->from_warehouse_name ?? '',
                'from_warehouse_address' => $transfer->from_warehouse_address ?? '',
                'to_warehouse_id' => $transfer->to_warehouse_id,
                'to_warehouse_name' => $transfer->to_warehouse_name ?? '',
                'to_warehouse_address' => $transfer->to_warehouse_address ?? '',
                'transfer_date' => $transfer->transfer_date,
                'status' => $transfer->status,
                'status_text' => $this->getTransferStatusText($transfer->status),
                'notes' => $transfer->notes,
                'created_by' => $transfer->user_full_name ?? $transfer->first_name,
                'completed_by' => $transfer->completed_by_full_name ?? '',
                'created_at' => $transfer->created_at,
                'updated_at' => $transfer->updated_at,
                'completed_at' => $transfer->completed_at,
                'user' => [
                    'id' => $transfer->user_id,
                    'user_name' => $transfer->user_name,
                    'first_name' => $transfer->first_name,
                    'last_name' => $transfer->last_name,
                    'email' => $transfer->email,
                    'phone_number' => $transfer->phone_number,
                    'company_name' => $transfer->company_name,
                    'inn' => $transfer->inn,
                    'full_name' => $transfer->user_full_name
                ],
                'positions' => $positions
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('AdminController getTransferDetails error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении перемещения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить текст статуса перемещения
     */
    private function getTransferStatusText($status): string
    {
        $statuses = [
            'draft' => 'Черновик',
            'confirmed' => 'Подтвержден',
            'completed' => 'Выполнен',
            'cancelled' => 'Отменен'
        ];
        
        return $statuses[$status] ?? $status;
    }

    /**
     * Получить остатки товаров для админ-панели
     */
    public function getBalances(Request $request): JsonResponse
    {
        try {
            $query = ProductBalance::with(['product.user', 'product.images', 'warehouse']);

            // Фильтр по складу
            if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            // Фильтр по пользователю
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->whereHas('warehouse', function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                });
            }

            // Поиск по названию товара
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('article', 'like', "%{$search}%");
                });
            }

            // Фильтр по минимальному количеству
            if ($request->has('min_quantity') && !empty($request->min_quantity)) {
                $query->where('quantity', '>=', $request->min_quantity);
            }

            // Фильтр по максимальному количеству
            if ($request->has('max_quantity') && !empty($request->max_quantity)) {
                $query->where('quantity', '<=', $request->max_quantity);
            }

            // Пагинация
            $perPage = $request->get('per_page', 15);
            $balances = $query->orderBy('quantity', 'desc')->paginate($perPage);

            // Получаем цены товаров
            $balances->getCollection()->transform(function ($balance) {
                // Сначала пытаемся получить цену из самого товара
                if ($balance->product && $balance->product->price > 0) {
                    $balance->product->price = (float) $balance->product->price;
                } else {
                    // Если цены нет в товаре, проверяем последнее оприходование (для совместимости)
                    $lastReceiptPosition = DB::table('receipt_positions')
                        ->where('product_id', $balance->product_id)
                        ->whereNotNull('price')
                        ->where('price', '>', 0)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $balance->product->price = $lastReceiptPosition ? (float) $lastReceiptPosition->price : 0;
                }

                // Преобразуем URL изображений
                if ($balance->product && $balance->product->images) {
                    $baseUrl = request()->getSchemeAndHttpHost() . '/storage/';
                    $balance->product->images->each(function ($image) use ($baseUrl) {
                        $image->image_url = $baseUrl . $image->image_url;
                    });
                }

                return $balance;
            });

            return response()->json($balances);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении остатков: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить движение товаров для админ-панели
     */
    public function getBalanceMovements(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products_sklad,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date'
            ]);

            // Получаем информацию о товаре с категориями
            $product = \App\Models\ProductSklad::with(['user', 'categoryRelation', 'subcategoryRelation'])->find($request->product_id);
            
            $query = DB::table('product_operations as po')
                ->leftJoin('products_sklad as p', 'po.product_id', '=', 'p.id')
                ->leftJoin('warehouses as w', 'po.warehouse_id', '=', 'w.id')
                ->leftJoin('users as u', 'w.user_id', '=', 'u.id')
                ->select([
                    'po.*',
                    'p.name as product_name',
                    'p.article as product_article',
                    'w.name as warehouse_name',
                    'u.user_name',
                    'u.first_name',
                    'u.company_name'
                ])
                ->where('po.product_id', $request->product_id);

            if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
                $query->where('po.warehouse_id', $request->warehouse_id);
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('po.created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('po.created_at', '<=', $request->date_to . ' 23:59:59');
            }

            $page = (int) $request->get('page', 1);
            $movements = $query->orderBy('po.created_at', 'desc')
                ->paginate(50, ['*'], 'page', $page);

            // Получаем последнюю цену товара
            $productPrice = 0;
            
            // Сначала проверяем цену в самом товаре
            if ($product && $product->price > 0) {
                $productPrice = (float) $product->price;
            } else {
                // Если цены нет в товаре, проверяем последнее оприходование
                $lastReceiptPosition = DB::table('receipt_positions')
                    ->where('product_id', $request->product_id)
                    ->whereNotNull('price')
                    ->where('price', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $productPrice = $lastReceiptPosition ? (float) $lastReceiptPosition->price : 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'movements' => $movements,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'article' => $product->article,
                        'category' => $product->category,
                        'subcategory' => $product->subcategory,
                        'category_name' => $product->category_name,
                        'subcategory_name' => $product->subcategory_name,
                        'user' => [
                            'user_name' => $product->user->user_name,
                            'first_name' => $product->user->first_name,
                            'company_name' => $product->user->company_name
                        ]
                    ],
                    'product_price' => $productPrice
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении движения товаров: ' . $e->getMessage()
            ], 500);
        }
    }
} 