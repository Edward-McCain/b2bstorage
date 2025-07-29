<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserCategoriesTypeController extends Controller
{
    /**
     * Обновить тип категорий пользователя
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'cats_type' => 'required|in:system,user'
            ]);

            // Обновляем тип категорий в таблице users
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'cats_type' => $request->cats_type
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Тип категорий успешно обновлен',
                'data' => [
                    'cats_type' => $request->cats_type
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка обновления типа категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления типа категорий'
            ], 500);
        }
    }

    /**
     * Получить текущий тип категорий пользователя
     */
    public function show()
    {
        try {
            $user = Auth::user();
            
            $catsType = DB::table('users')
                ->where('id', $user->id)
                ->value('cats_type');

            return response()->json([
                'success' => true,
                'data' => [
                    'cats_type' => $catsType ?? 'system'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка получения типа категорий: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения типа категорий'
            ], 500);
        }
    }
} 