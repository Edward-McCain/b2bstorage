<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryMigrationController extends Controller
{
    /**
     * Проверить и исправить категории товаров пользователя
     */
    public function checkAndFixCategories(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Получаем тип категорий пользователя
            $catsType = $user->cats_type ?? 'system';
            
            // Получаем все товары пользователя с проблемными категориями
            $products = DB::table('products_sklad')
                ->where('user_id', $user->id)
                ->whereNotNull('category')
                ->get();
            
            $issues = [];
            $fixed = 0;
            
            foreach ($products as $product) {
                $categoryExists = false;
                $subcategoryExists = false;
                
                if ($catsType === 'user') {
                    // Проверяем существование в пользовательских категориях
                    $categoryExists = DB::table('user_categories')
                        ->where('category_id', $product->category)
                        ->where('user_id', $user->id)
                        ->exists();
                        
                    if ($product->subcategory) {
                        $subcategoryExists = DB::table('user_subcategories')
                            ->where('subcategory_id', $product->subcategory)
                            ->where('user_id', $user->id)
                            ->exists();
                    }
                } else {
                    // Проверяем существование в системных категориях
                    $categoryExists = DB::table('categories')
                        ->where('category_id', $product->category)
                        ->exists();
                        
                    if ($product->subcategory) {
                        $subcategoryExists = DB::table('subcategories')
                            ->where('subcategory_id', $product->subcategory)
                            ->exists();
                    }
                }
                
                if (!$categoryExists || !$subcategoryExists) {
                    $issues[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'category' => $product->category,
                        'subcategory' => $product->subcategory,
                        'category_exists' => $categoryExists,
                        'subcategory_exists' => $subcategoryExists
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'issues' => $issues,
                    'total_products' => $products->count(),
                    'issues_count' => count($issues),
                    'category_type' => $catsType
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка проверки категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка проверки категорий: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Исправить категории товаров (очистить несуществующие)
     */
    public function fixCategories(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Получаем тип категорий пользователя
            $catsType = $user->cats_type ?? 'system';
            
            // Получаем все товары пользователя
            $products = DB::table('products_sklad')
                ->where('user_id', $user->id)
                ->whereNotNull('category')
                ->get();
            
            $fixed = 0;
            $errors = [];
            
            foreach ($products as $product) {
                $categoryExists = false;
                $subcategoryExists = false;
                
                if ($catsType === 'user') {
                    // Проверяем существование в пользовательских категориях
                    $categoryExists = DB::table('user_categories')
                        ->where('category_id', $product->category)
                        ->where('user_id', $user->id)
                        ->exists();
                        
                    if ($product->subcategory) {
                        $subcategoryExists = DB::table('user_subcategories')
                            ->where('subcategory_id', $product->subcategory)
                            ->where('user_id', $user->id)
                            ->exists();
                    }
                } else {
                    // Проверяем существование в системных категориях
                    $categoryExists = DB::table('categories')
                        ->where('category_id', $product->category)
                        ->exists();
                        
                    if ($product->subcategory) {
                        $subcategoryExists = DB::table('subcategories')
                            ->where('subcategory_id', $product->subcategory)
                            ->exists();
                    }
                }
                
                // Если категория или подкатегория не существует, очищаем их
                if (!$categoryExists || !$subcategoryExists) {
                    $updateData = [];
                    
                    if (!$categoryExists) {
                        $updateData['category'] = null;
                    }
                    
                    if (!$subcategoryExists) {
                        $updateData['subcategory'] = null;
                    }
                    
                    DB::table('products_sklad')
                        ->where('id', $product->id)
                        ->update($updateData);
                    
                    $fixed++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Исправлено товаров: {$fixed}",
                'data' => [
                    'fixed_count' => $fixed,
                    'category_type' => $catsType
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка исправления категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка исправления категорий: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить статистику по категориям пользователя
     */
    public function getCategoryStats(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Получаем тип категорий пользователя
            $catsType = $user->cats_type ?? 'system';
            
            if ($catsType === 'user') {
                // Статистика пользовательских категорий
                $categories = DB::table('user_categories')
                    ->leftJoin('products_sklad', 'products_sklad.category', '=', 'user_categories.category_id')
                    ->select([
                        'user_categories.category_id',
                        'user_categories.name',
                        DB::raw('COUNT(products_sklad.id) as products_count')
                    ])
                    ->where('user_categories.user_id', $user->id)
                    ->groupBy('user_categories.category_id', 'user_categories.name')
                    ->orderBy('user_categories.name')
                    ->get();
                    
                $subcategories = DB::table('user_subcategories')
                    ->leftJoin('products_sklad', 'products_sklad.subcategory', '=', 'user_subcategories.subcategory_id')
                    ->select([
                        'user_subcategories.subcategory_id',
                        'user_subcategories.name',
                        'user_subcategories.category_id',
                        DB::raw('COUNT(products_sklad.id) as products_count')
                    ])
                    ->where('user_subcategories.user_id', $user->id)
                    ->groupBy('user_subcategories.subcategory_id', 'user_subcategories.name', 'user_subcategories.category_id')
                    ->orderBy('user_subcategories.name')
                    ->get();
            } else {
                // Статистика системных категорий
                $categories = DB::table('categories')
                    ->leftJoin('products_sklad', 'products_sklad.category', '=', 'categories.category_id')
                    ->select([
                        'categories.category_id',
                        'categories.name_ru as name',
                        DB::raw('COUNT(products_sklad.id) as products_count')
                    ])
                    ->groupBy('categories.category_id', 'categories.name_ru')
                    ->orderBy('categories.name_ru')
                    ->get();
                    
                $subcategories = DB::table('subcategories')
                    ->leftJoin('products_sklad', 'products_sklad.subcategory', '=', 'subcategories.subcategory_id')
                    ->select([
                        'subcategories.subcategory_id',
                        'subcategories.name_ru as name',
                        'subcategories.category_id',
                        DB::raw('COUNT(products_sklad.id) as products_count')
                    ])
                    ->groupBy('subcategories.subcategory_id', 'subcategories.name_ru', 'subcategories.category_id')
                    ->orderBy('subcategories.name_ru')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'subcategories' => $subcategories,
                    'category_type' => $catsType
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения статистики категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения статистики категорий: ' . $e->getMessage()
            ], 500);
        }
    }
} 