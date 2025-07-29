<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserCategoryController extends Controller
{
    /**
     * Получить список пользовательских категорий с подсчетом товаров
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Получаем категории пользователя с подсчетом товаров
            $categories = DB::table('user_categories as uc')
                ->leftJoin('user_subcategories as usc', 'uc.category_id', '=', 'usc.category_id')
                ->leftJoin('products_sklad as ps', function($join) {
                    $join->on('ps.category', '=', 'uc.category_id')
                         ->orOn('ps.subcategory', '=', 'usc.subcategory_id');
                })
                ->select([
                    'uc.id',
                    'uc.category_id',
                    'uc.name',
                    'uc.created_at',
                    'uc.updated_at',
                    DB::raw('COUNT(DISTINCT ps.id) as products_count')
                ])
                ->where('uc.user_id', $user->id)
                ->groupBy('uc.id', 'uc.category_id', 'uc.name', 'uc.created_at', 'uc.updated_at')
                ->orderBy('uc.name')
                ->get();

            // Получаем подкатегории для каждой категории
            foreach ($categories as $category) {
                $category->subcategories = DB::table('user_subcategories as usc')
                    ->leftJoin('products_sklad as ps', 'ps.subcategory', '=', 'usc.subcategory_id')
                    ->select([
                        'usc.id',
                        'usc.subcategory_id',
                        'usc.name',
                        'usc.category_id',
                        'usc.created_at',
                        'usc.updated_at',
                        DB::raw('COUNT(DISTINCT ps.id) as products_count')
                    ])
                    ->where('usc.user_id', $user->id)
                    ->where('usc.category_id', $category->category_id)
                    ->groupBy('usc.id', 'usc.subcategory_id', 'usc.name', 'usc.category_id', 'usc.created_at', 'usc.updated_at')
                    ->orderBy('usc.name')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка получения пользовательских категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения категорий'
            ], 500);
        }
    }

    /**
     * Создать новую пользовательскую категорию
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $categoryId = 'user_cat_' . Str::uuid();
            
            $category = DB::table('user_categories')->insert([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'name' => $request->name,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно создана',
                'data' => [
                    'category_id' => $categoryId,
                    'name' => $request->name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка создания пользовательской категории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания категории'
            ], 500);
        }
    }

    /**
     * Обновить пользовательскую категорию
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category = DB::table('user_categories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            // Проверяем, есть ли товары в этой категории
            $productsCount = DB::table('products_sklad')
                ->where('category', $category->category_id)
                ->count();

            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя изменить категорию, в которой есть товары'
                ], 400);
            }

            DB::table('user_categories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->update([
                    'name' => $request->name,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно обновлена'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка обновления пользовательской категории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления категории'
            ], 500);
        }
    }

    /**
     * Удалить пользовательскую категорию
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $category = DB::table('user_categories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            // Проверяем, есть ли товары в этой категории
            $productsCount = DB::table('products_sklad')
                ->where('category', $category->category_id)
                ->count();

            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить категорию, в которой есть товары'
                ], 400);
            }

            // Удаляем категорию (подкатегории удалятся каскадно)
            DB::table('user_categories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно удалена'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка удаления пользовательской категории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления категории'
            ], 500);
        }
    }
} 