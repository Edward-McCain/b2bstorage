<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function getOperationsStatistics(Request $request)
    {
        try {
            // Временно используем тестового пользователя для отладки
            $userId = Auth::id() ?: 52; // Если пользователь не аутентифицирован, используем ID 52
            
            $period = $request->get('period', 'month'); // week, month, year
            
            Log::info("Statistics request", [
                'user_id' => $userId,
                'period' => $period,
                'auth_user_id' => Auth::id()
            ]);
            
            // Получаем данные по операциям - всегда последние доступные данные
            $receipts = $this->getLatestReceiptsData($userId, $period);
            $writeOffs = $this->getLatestWriteOffsData($userId, $period);
            $transfers = $this->getLatestTransfersData($userId, $period);
            
            Log::info("Data retrieved", [
                'receipts_count' => count($receipts),
                'write_offs_count' => count($writeOffs),
                'transfers_count' => count($transfers)
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'receipts' => $receipts,
                    'writeOffs' => $writeOffs,
                    'transfers' => $transfers,
                    'period' => $period,
                    'startDate' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'endDate' => Carbon::now()->format('Y-m-d')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("Statistics error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статистики',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'year':
            default:
                return Carbon::now()->subYear();
        }
    }
    
    private function getReceiptsData($userId, $startDate, $endDate, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        $results = DB::table('receipts')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', date) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        Log::info("Receipts query result", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        return $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
    }
    
    private function getWriteOffsData($userId, $startDate, $endDate, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        $results = DB::table('write_offs')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', date) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        Log::info("Write-offs query result", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        return $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
    }
    
    private function getTransfersData($userId, $startDate, $endDate, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        $results = DB::table('product_transfers')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', created_at) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_by', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        Log::info("Transfers query result", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        return $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
    }
    
    // Методы для получения последних доступных данных
    private function getLatestReceiptsData($userId, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        Log::info("getLatestReceiptsData called", [
            'user_id' => $userId,
            'period' => $period,
            'group_by' => $groupBy
        ]);
        
        $results = DB::table('receipts')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', date) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $userId)
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12)
            ->get();
        
        Log::info("getLatestReceiptsData results", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        $mappedResults = $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
        
        Log::info("getLatestReceiptsData mapped results", [
            'mapped_count' => $mappedResults->count(),
            'mapped_data' => $mappedResults->toArray()
        ]);
        
        return $mappedResults;
    }
    
    private function getLatestWriteOffsData($userId, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        Log::info("getLatestWriteOffsData called", [
            'user_id' => $userId,
            'period' => $period,
            'group_by' => $groupBy
        ]);
        
        $results = DB::table('write_offs')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', date) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $userId)
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12)
            ->get();
        
        Log::info("getLatestWriteOffsData results", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        $mappedResults = $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
        
        Log::info("getLatestWriteOffsData mapped results", [
            'mapped_count' => $mappedResults->count(),
            'mapped_data' => $mappedResults->toArray()
        ]);
        
        return $mappedResults;
    }
    
    private function getLatestTransfersData($userId, $period)
    {
        $groupBy = $this->getGroupBy($period);
        
        Log::info("getLatestTransfersData called", [
            'user_id' => $userId,
            'period' => $period,
            'group_by' => $groupBy
        ]);
        
        $results = DB::table('product_transfers')
            ->select(
                DB::raw("DATE_TRUNC('{$groupBy}', created_at) as period"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_by', $userId)
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12)
            ->get();
        
        Log::info("getLatestTransfersData results", [
            'count' => $results->count(),
            'data' => $results->toArray()
        ]);
        
        $mappedResults = $results->map(function ($item) use ($period) {
            return [
                'date' => $this->formatDate($item->period, $period),
                'count' => $item->count
            ];
        });
        
        Log::info("getLatestTransfersData mapped results", [
            'mapped_count' => $mappedResults->count(),
            'mapped_data' => $mappedResults->toArray()
        ]);
        
        return $mappedResults;
    }
    
    private function getGroupBy($period)
    {
        switch ($period) {
            case 'week':
                return 'day';
            case 'month':
                return 'week';
            case 'year':
            default:
                return 'month';
        }
    }
    
    private function formatDate($date, $period)
    {
        $carbon = Carbon::parse($date);
        
        switch ($period) {
            case 'week':
                return $carbon->format('d.m');
            case 'month':
                return $carbon->format('d.m');
            case 'year':
            default:
                return $carbon->format('M');
        }
    }
} 