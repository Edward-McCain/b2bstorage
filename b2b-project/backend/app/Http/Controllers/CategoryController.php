<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Получить категории в зависимости от настроек пользователя
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Получаем тип категорий пользователя
            $catsType = $user->cats_type ?? 'system';
            
            if ($catsType === 'user') {
                // Возвращаем пользовательские категории
                $categories = DB::table('user_categories')
                    ->select('id', 'category_id', 'name')
                    ->where('user_id', $user->id)
                    ->orderBy('name')
                    ->get();
            } else {
                // Возвращаем системные категории
                $categories = DB::table('categories')
                    ->select('id', 'category_id', 'name_ru as name')
                    ->orderBy('name_ru')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения категорий: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить подкатегории в зависимости от настроек пользователя
     */
    public function subcategories(Request $request, $id = null)
    {
        try {
            $user = Auth::user();
            $category_id = $id ?: $request->query('category_id');
            
            if (!$category_id) {
                return response()->json(['error' => 'ID категории не указан'], 400);
            }
            
            // Получаем тип категорий пользователя
            $catsType = $user->cats_type ?? 'system';
            
            if ($catsType === 'user') {
                // Проверяем, что категория принадлежит пользователю
                $category = DB::table('user_categories')
                    ->where('category_id', $category_id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Категория не найдена'
                    ], 404);
                }
                
                // Возвращаем пользовательские подкатегории
                $subcategories = DB::table('user_subcategories')
                    ->select('id', 'subcategory_id', 'name')
                    ->where('category_id', $category_id)
                    ->where('user_id', $user->id)
                    ->orderBy('name')
                    ->get();
            } else {
                // Возвращаем системные подкатегории
                $subcategories = DB::table('subcategories')
                    ->select('id', 'subcategory_id', 'name_ru as name')
                    ->where('category_id', $category_id)
                    ->orderBy('name_ru')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $subcategories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения подкатегорий: ' . $e->getMessage()
            ], 500);
        }
    }
} 