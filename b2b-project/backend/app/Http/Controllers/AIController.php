<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Анализ остатков и создание рекомендаций
     */
    public function analyzeStockLevels(): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->aiService->analyzeStockLevels($user->id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Анализ остатков выполнен',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Нет товаров с низкими остатками для анализа'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Ошибка при анализе остатков: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при анализе остатков'
            ], 500);
        }
    }

    /**
     * Анализ документов
     */
    public function analyzeDocuments(): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->aiService->analyzeDocuments($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Анализ документов выполнен',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при анализе документов: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при анализе документов'
            ], 500);
        }
    }

    /**
     * Умный поиск
     */
    public function smartSearch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            $query = $request->input('query');
            $result = $this->aiService->smartSearch($query, $user->id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось выполнить умный поиск'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Ошибка при умном поиске: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при умном поиске'
            ], 500);
        }
    }

    /**
     * Прогнозирование остатков
     */
    public function forecastStock(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products_sklad,id'
            ]);

            $user = Auth::user();
            $productId = $request->input('product_id');
            $result = $this->aiService->forecastStock($productId, $user->id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось создать прогноз'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Ошибка при прогнозировании остатков: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при прогнозировании остатков'
            ], 500);
        }
    }

    /**
     * Генерация общих рекомендаций
     */
    public function generateRecommendations(): JsonResponse
    {
        try {
            Log::info('AIController: Начинаем генерацию рекомендаций');
            
            $user = Auth::user();
            Log::info('AIController: Пользователь: ' . $user->id);
            
            $result = $this->aiService->generateGeneralRecommendations($user->id);
            
            Log::info('AIController: Рекомендации сгенерированы успешно');

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                Log::warning('AIController: Результат пустой');
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось сгенерировать рекомендации'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('AIController: Ошибка при генерации рекомендаций: ' . $e->getMessage());
            Log::error('AIController: Stack trace: ' . $e->getTraceAsString());
            
            // Проверяем, является ли это специальным сообщением об ошибке
            if ($e->getMessage() === 'Попробуйте позднее.') {
                return response()->json([
                    'success' => false,
                    'message' => 'Попробуйте позднее.'
                ], 503); // 503 Service Unavailable
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось сгенерировать рекомендации: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Комплексный анализ системы
     */
    public function comprehensiveAnalysis(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Выполняем все виды анализа
            $stockAnalysis = $this->aiService->analyzeStockLevels($user->id);
            $documentAnalysis = $this->aiService->analyzeDocuments($user->id);
            $generalRecommendations = $this->aiService->generateGeneralRecommendations($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Комплексный анализ выполнен',
                'data' => [
                    'stock_analysis' => $stockAnalysis,
                    'document_analysis' => $documentAnalysis,
                    'general_recommendations' => $generalRecommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при комплексном анализе: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при комплексном анализе'
            ], 500);
        }
    }
} 