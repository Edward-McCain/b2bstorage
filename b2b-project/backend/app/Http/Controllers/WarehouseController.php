<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Получить все склады пользователя
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $warehouses = Warehouse::where('user_id', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $warehouses
        ]);
    }

    /**
     * Получить один склад
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $warehouse = Warehouse::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$warehouse) {
            return response()->json(['error' => 'Склад не найден'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $warehouse
        ]);
    }

    /**
     * Создать новый склад
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        $warehouse = Warehouse::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Склад успешно создан',
            'data' => $warehouse
        ], 201);
    }

    /**
     * Обновить склад
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $warehouse = Warehouse::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$warehouse) {
            return response()->json(['error' => 'Склад не найден'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        $warehouse->update([
            'name' => $request->name,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Склад успешно обновлен',
            'data' => $warehouse
        ]);
    }

    /**
     * Удалить склад
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $warehouse = Warehouse::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$warehouse) {
            return response()->json(['error' => 'Склад не найден'], 404);
        }

        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Склад успешно удален'
        ]);
    }
}
