<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserSubcategoryController extends Controller
{
    /**
     * Получить подкатегории для конкретной категории
     */
    public function index($categoryId)
    {
        try {
            $user = Auth::user();
            
            // Проверяем, что категория принадлежит пользователю
            $category = DB::table('user_categories')
                ->where('category_id', $categoryId)
                ->where('user_id', $user->id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            $subcategories = DB::table('user_subcategories as usc')
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
                ->where('usc.category_id', $categoryId)
                ->groupBy('usc.id', 'usc.subcategory_id', 'usc.name', 'usc.category_id', 'usc.created_at', 'usc.updated_at')
                ->orderBy('usc.name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subcategories
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка получения пользовательских подкатегорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения подкатегорий'
            ], 500);
        }
    }

    /**
     * Создать новую пользовательскую подкатегорию
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|string'
            ]);

            // Проверяем, что категория принадлежит пользователю
            $category = DB::table('user_categories')
                ->where('category_id', $request->category_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            $subcategoryId = 'user_subcat_' . Str::uuid();
            
            DB::table('user_subcategories')->insert([
                'user_id' => $user->id,
                'subcategory_id' => $subcategoryId,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Подкатегория успешно создана',
                'data' => [
                    'subcategory_id' => $subcategoryId,
                    'name' => $request->name,
                    'category_id' => $request->category_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка создания пользовательской подкатегории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания подкатегории'
            ], 500);
        }
    }

    /**
     * Обновить пользовательскую подкатегорию
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $subcategory = DB::table('user_subcategories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$subcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Подкатегория не найдена'
                ], 404);
            }

            // Проверяем, есть ли товары в этой подкатегории
            $productsCount = DB::table('products_sklad')
                ->where('subcategory', $subcategory->subcategory_id)
                ->count();

            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя изменить подкатегорию, в которой есть товары'
                ], 400);
            }

            DB::table('user_subcategories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->update([
                    'name' => $request->name,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Подкатегория успешно обновлена'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка обновления пользовательской подкатегории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления подкатегории'
            ], 500);
        }
    }

    /**
     * Удалить пользовательскую подкатегорию
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $subcategory = DB::table('user_subcategories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$subcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Подкатегория не найдена'
                ], 404);
            }

            // Проверяем, есть ли товары в этой подкатегории
            $productsCount = DB::table('products_sklad')
                ->where('subcategory', $subcategory->subcategory_id)
                ->count();

            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить подкатегорию, в которой есть товары'
                ], 400);
            }

            DB::table('user_subcategories')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Подкатегория успешно удалена'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка удаления пользовательской подкатегории: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления подкатегории'
            ], 500);
        }
    }
} 