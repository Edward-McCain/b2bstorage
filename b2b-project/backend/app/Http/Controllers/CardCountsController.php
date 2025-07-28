<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Receipt;
use App\Models\WriteOff;
use App\Models\Inventory;
use App\Models\ProductTransfer;
use App\Models\ProductBalance;
use App\Models\Warehouse;

class CardCountsController extends Controller
{
    /**
     * Получить количество оприходований пользователя
     */
    public function receiptsCount()
    {
        try {
            $userId = Auth::id();
            $count = Receipt::where('user_id', $userId)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете оприходований',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить количество списаний пользователя
     */
    public function writeOffsCount()
    {
        try {
            $userId = Auth::id();
            $count = WriteOff::where('user_id', $userId)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете списаний',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить количество инвентаризаций пользователя
     */
    public function inventoryCount()
    {
        try {
            $userId = Auth::id();
            $count = Inventory::where('created_by', $userId)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете инвентаризаций',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить количество перемещений пользователя
     */
    public function transfersCount()
    {
        try {
            $userId = Auth::id();
            $count = ProductTransfer::where('created_by', $userId)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете перемещений',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить количество остатков пользователя
     */
    public function balancesCount()
    {
        try {
            $userId = Auth::id();
            $count = ProductBalance::whereHas('product', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете остатков',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить количество складов пользователя
     */
    public function warehousesCount()
    {
        try {
            $userId = Auth::id();
            $count = Warehouse::where('user_id', $userId)->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подсчете складов',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить все счетчики одним запросом
     */
    public function getAllCounts()
    {
        try {
            $userId = Auth::id();
            $receipts = \App\Models\Receipt::where('user_id', $userId)->count();
            $writeOffs = \App\Models\WriteOff::where('user_id', $userId)->count();
            $inventory = \App\Models\Inventory::where('created_by', $userId)->count();
            $transfers = \App\Models\ProductTransfer::where('created_by', $userId)->count();
            $balances = \App\Models\ProductBalance::whereHas('product', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count();
            $warehouses = \App\Models\Warehouse::where('user_id', $userId)->count();
            return response()->json([
                'success' => true,
                'counts' => [
                    'receipts' => $receipts,
                    'writeOffs' => $writeOffs,
                    'inventory' => $inventory,
                    'transfers' => $transfers,
                    'balances' => $balances,
                    'warehouses' => $warehouses,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении счетчиков',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 