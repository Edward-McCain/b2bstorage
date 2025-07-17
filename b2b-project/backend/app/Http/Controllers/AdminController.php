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
            // Начинаем с таблицы product_balances для правильной фильтрации по складам
            $query = ProductBalance::with([
                'product.user', 
                'warehouse', 
                'product.category', 
                'product.subcategory'
            ]);
            
            // Поиск по наименованию, артикулу или ИНН компании
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('article', 'like', "%{$search}%")
                          ->orWhereHas('user', function($userQuery) use ($search) {
                              $userQuery->where('inn', 'like', "%{$search}%");
                          });
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
            
            // Сортировка
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Если сортировка по полям продукта, используем whereHas с orderBy
            if (in_array($sortBy, ['name', 'article'])) {
                $query->whereHas('product', function($productQuery) use ($sortBy, $sortOrder) {
                    $productQuery->orderBy($sortBy, $sortOrder);
                });
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
            
            // Пагинация
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);
            
            // Преобразуем данные для фронтенда
            $productsWithData = collect($products->items())->map(function ($balance) {
                $product = $balance->product;
                
                // Получаем изображение товара
                $imageUrl = null;
                if ($product->images && $product->images->count() > 0) {
                    $imageUrl = $this->transformImageUrl($product->images->first()->image_path);
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'description' => $product->description,
                    'warehouse' => $balance->warehouse,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'quantity' => $balance->quantity,
                    'price' => $balance->average_price ?? 0,
                    'image_url' => $imageUrl,
                    'user' => $product->user,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ];
            });
            
            // Получаем списки для фильтров
            $warehouses = Warehouse::select('id', 'name')->get();
            $categories = Category::select('category_id', 'name_ru as name')->orderBy('name_ru')->get();
            
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
            // Начинаем с таблицы product_balances для правильной фильтрации по складам
            $query = ProductBalance::with([
                'product.user', 
                'warehouse', 
                'product.category', 
                'product.subcategory'
            ]);
            
            // Поиск по наименованию, артикулу или ИНН компании
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('article', 'like', "%{$search}%")
                          ->orWhereHas('user', function($userQuery) use ($search) {
                              $userQuery->where('inn', 'like', "%{$search}%");
                          });
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
            
            // Сортировка - используем поля из product_balances или связанных таблиц
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Если сортировка по полям продукта, используем whereHas с orderBy
            if (in_array($sortBy, ['name', 'article'])) {
                $query->whereHas('product', function($productQuery) use ($sortBy, $sortOrder) {
                    $productQuery->orderBy($sortBy, $sortOrder);
                });
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
            
            // Пагинация
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);
            
            // Преобразуем данные для фронтенда
            $productsWithData = collect($products->items())->map(function ($balance) {
                $product = $balance->product;
                
                // Получаем изображение товара
                $imageUrl = null;
                if ($product->images && $product->images->count() > 0) {
                    $imageUrl = $this->transformImageUrl($product->images->first()->image_path);
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'article' => $product->article,
                    'description' => $product->description,
                    'warehouse' => $balance->warehouse,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'quantity' => $balance->quantity,
                    'price' => $balance->average_price ?? 0,
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
     * Получить подкатегории по ID категории
     */
    public function getSubcategories(Request $request): JsonResponse
    {
        try {
            $categoryId = $request->get('category_id');
            
            Log::info('AdminController getSubcategories called', [
                'category_id' => $categoryId,
                'request_params' => $request->all()
            ]);
            
            if (!$categoryId) {
                Log::warning('AdminController getSubcategories: category_id not provided');
                return response()->json([
                    'success' => false,
                    'message' => 'ID категории не указан'
                ], 400);
            }
            
            $subcategories = Subcategory::where('category_id', $categoryId)
                ->select('subcategory_id', 'name_ru as name')
                ->orderBy('name_ru')
                ->get();
            
            Log::info('AdminController getSubcategories result', [
                'category_id' => $categoryId,
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
            $warehouses = Warehouse::select('id', 'name')
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
    
} 