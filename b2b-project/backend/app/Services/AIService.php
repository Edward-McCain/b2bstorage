<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\ProductSklad;
use App\Models\Receipt;
use App\Models\WriteOff;
use App\Models\ProductTransfer;
use Illuminate\Support\Facades\Auth;

class AIService
{
    /**
     * Анализ остатков товаров и создание рекомендаций
     */
    public function analyzeStockLevels($userId)
    {
        try {
            // Получаем все товары с остатками
            $allProducts = ProductSklad::with(['warehouse'])->get();
            
            if ($allProducts->isEmpty()) {
                return "В системе нет товаров для анализа.";
            }

            // Анализируем разные категории товаров по start_count
            $zeroStockProducts = $allProducts->where('start_count', '=', 0);
            $lowStockProducts = $allProducts->where('start_count', '>', 0)->where('start_count', '<=', 10);
            $mediumStockProducts = $allProducts->where('start_count', '>', 10)->where('start_count', '<=', 50);
            $highStockProducts = $allProducts->where('start_count', '>', 50);
            
            $totalProducts = $allProducts->count();
            $totalQuantity = $allProducts->sum('start_count');
            $averageQuantity = $totalQuantity / $totalProducts;
            
            // Формируем детальную статистику
            $statistics = [
                'total_products' => $totalProducts,
                'total_quantity' => $totalQuantity,
                'average_quantity' => round($averageQuantity, 2),
                'zero_stock_count' => $zeroStockProducts->count(),
                'low_stock_count' => $lowStockProducts->count(),
                'medium_stock_count' => $mediumStockProducts->count(),
                'high_stock_count' => $highStockProducts->count(),
                'warehouses_count' => $allProducts->pluck('warehouse_id')->unique()->count()
            ];

            // Формируем список товаров для анализа
            $productsList = $allProducts->take(20)->map(function ($item) {
                if ($item->start_count == 0) {
                    $stockLevel = 'НУЛЕВОЙ';
                } elseif ($item->start_count <= 10) {
                    $stockLevel = 'НИЗКИЙ';
                } elseif ($item->start_count <= 50) {
                    $stockLevel = 'СРЕДНИЙ';
                } else {
                    $stockLevel = 'ВЫСОКИЙ';
                }
                return "Товар: {$item->name}, Остаток: {$item->start_count}, Склад: " . ($item->warehouse ? $item->warehouse->name : 'Не указан') . ", Уровень: {$stockLevel}";
            })->implode("\n");

            $prompt = "Проанализируй следующие данные по складским остаткам и дай детальные рекомендации:

СТАТИСТИКА:
- Всего товаров: {$statistics['total_products']}
- Общий остаток: {$statistics['total_quantity']}
- Средний остаток: {$statistics['average_quantity']}
- Товаров с нулевым остатком: {$statistics['zero_stock_count']}
- Товаров с низким остатком (1-10): {$statistics['low_stock_count']}
- Товаров со средним остатком (11-50): {$statistics['medium_stock_count']}
- Товаров с высоким остатком (>50): {$statistics['high_stock_count']}
- Количество складов: {$statistics['warehouses_count']}

ТОВАРЫ (первые 20):
{$productsList}

Дай анализ по следующим пунктам:
1. Общая оценка состояния остатков
2. Рекомендации по пополнению остатков для товаров с нулевым остатком
3. Рекомендации по закупкам для товаров с низким остатком
4. Рекомендации по оптимизации для товаров с высоким остатком
5. Общие рекомендации по управлению запасами
6. Прогноз на ближайшие 30 дней

Особое внимание удели товарам с нулевым остатком - дай конкретные рекомендации по их пополнению.

Ответ должен быть структурированным и практичным на русском языке.";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты эксперт по управлению складскими запасами. Давай детальные и практичные рекомендации на основе реальных данных. Особое внимание уделяй товарам с нулевым остатком.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1200,
                'temperature' => 0.7
            ]);

            $recommendation = $response->choices[0]->message->content;

            // Создаем уведомление
            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_RECOMMENDATION,
                'message' => "Анализ остатков выполнен:\n\n{$recommendation}"
            ]);

            return [
                'statistics' => $statistics,
                'recommendations' => $recommendation
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка при анализе остатков через AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Анализ документов и создание уведомлений
     */
    public function analyzeDocuments($userId)
    {
        try {
            // Получаем все документы за последние 30 дней
            $thirtyDaysAgo = now()->subDays(30);
            
            $recentReceipts = Receipt::where('created_at', '>=', $thirtyDaysAgo)->get();
            $recentWriteOffs = WriteOff::where('created_at', '>=', $thirtyDaysAgo)->get();
            $recentTransfers = ProductTransfer::where('created_at', '>=', $thirtyDaysAgo)->get();
            
            // Анализируем просроченные документы
            $overdueReceipts = Receipt::where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(7))
                ->get();

            $overdueWriteOffs = WriteOff::where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(7))
                ->get();

            $overdueTransfers = ProductTransfer::where('status', 'in_progress')
                ->where('created_at', '<=', now()->subDays(3))
                ->get();

            // Статистика документов
            $documentsStats = [
                'total_receipts' => $recentReceipts->count(),
                'total_write_offs' => $recentWriteOffs->count(),
                'total_transfers' => $recentTransfers->count(),
                'overdue_receipts' => $overdueReceipts->count(),
                'overdue_write_offs' => $overdueWriteOffs->count(),
                'overdue_transfers' => $overdueTransfers->count(),
                'completed_receipts' => $recentReceipts->where('status', 'completed')->count(),
                'completed_write_offs' => $recentWriteOffs->where('status', 'completed')->count(),
                'completed_transfers' => $recentTransfers->where('status', 'completed')->count()
            ];

            // Формируем детальную информацию о документах
            $documentsInfo = "СТАТИСТИКА ДОКУМЕНТОВ (за 30 дней):\n";
            $documentsInfo .= "- Оприходования: {$documentsStats['total_receipts']} (завершено: {$documentsStats['completed_receipts']})\n";
            $documentsInfo .= "- Списания: {$documentsStats['total_write_offs']} (завершено: {$documentsStats['completed_write_offs']})\n";
            $documentsInfo .= "- Перемещения: {$documentsStats['total_transfers']} (завершено: {$documentsStats['completed_transfers']})\n\n";
            
            $documentsInfo .= "ПРОСРОЧЕННЫЕ ДОКУМЕНТЫ:\n";
            $documentsInfo .= "- Просроченных оприходований: {$documentsStats['overdue_receipts']}\n";
            $documentsInfo .= "- Просроченных списаний: {$documentsStats['overdue_write_offs']}\n";
            $documentsInfo .= "- Просроченных перемещений: {$documentsStats['overdue_transfers']}\n";

            $prompt = "Проанализируй следующие данные по документам и дай рекомендации:

{$documentsInfo}

Дай анализ по следующим пунктам:
1. Общая оценка работы с документами
2. Рекомендации по обработке просроченных документов
3. Рекомендации по оптимизации процессов документооборота
4. Прогноз по загрузке на ближайшие 30 дней
5. Общие рекомендации по улучшению работы с документами

Ответ должен быть структурированным и практичным на русском языке.";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты эксперт по документообороту и управлению складскими процессами. Давай детальные и практичные рекомендации.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 800,
                'temperature' => 0.7
            ]);

            $analysis = $response->choices[0]->message->content;

            // Создаем уведомления для просроченных документов
            $notifications = [];
            
            if ($documentsStats['overdue_receipts'] > 0) {
                $notifications[] = "У вас {$documentsStats['overdue_receipts']} необработанных оприходований старше 7 дней. Рекомендуем обработать их в ближайшее время.";
            }

            if ($documentsStats['overdue_write_offs'] > 0) {
                $notifications[] = "У вас {$documentsStats['overdue_write_offs']} необработанных списаний старше 7 дней. Рекомендуем обработать их в ближайшее время.";
            }

            if ($documentsStats['overdue_transfers'] > 0) {
                $notifications[] = "У вас {$documentsStats['overdue_transfers']} незавершенных перемещений старше 3 дней. Рекомендуем завершить их в ближайшее время.";
            }

            // Создаем уведомления
            foreach ($notifications as $message) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => Notification::TYPE_WARNING,
                    'message' => $message
                ]);
            }

            return [
                'statistics' => $documentsStats,
                'analysis' => $analysis,
                'notifications' => $notifications
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка при анализе документов через AI: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Умный поиск товаров
     */
    public function smartSearch($query, $userId)
    {
        try {
            // Получаем статистику по товарам для контекста
            $totalProducts = ProductSklad::count();
            $categories = \App\Models\ProductSklad::with('product.category')
                ->get()
                ->pluck('product.category.name')
                ->unique()
                ->filter()
                ->take(10)
                ->implode(', ');

            $prompt = "Пользователь ищет товары по запросу: '{$query}'

КОНТЕКСТ:
- Всего товаров в системе: {$totalProducts}
- Основные категории: {$categories}

Предложи:
1. Возможные варианты поиска и синонимы
2. Связанные категории товаров
3. Рекомендации по улучшению поиска
4. Популярные товары в этой категории

Ответ должен быть кратким и на русском языке.";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты помощник по поиску товаров. Предлагай варианты поиска и синонимы на основе реальных данных системы.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 400,
                'temperature' => 0.5
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            Log::error('Ошибка при умном поиске через AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Прогнозирование остатков
     */
    public function forecastStock($productId, $userId)
    {
        try {
            // Получаем историю движения товара
            $product = ProductSklad::with(['warehouse'])->find($productId);
            
            if (!$product) {
                return null;
            }

            // Получаем историю операций с этим товаром
            $operations = \App\Models\ProductOperation::where('product_id', $productId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $operationsHistory = $operations->map(function ($op) {
                return "{$op->created_at->format('d.m.Y')}: {$op->operation_type} - {$op->quantity} шт.";
            })->implode("\n");

            $prompt = "ТОВАР: {$product->name}
                ТЕКУЩИЙ ОСТАТОК: {$product->start_count}
                СКЛАД: " . ($product->warehouse ? $product->warehouse->name : 'Не указан') . "

                ИСТОРИЯ ОПЕРАЦИЙ (последние 10):
                {$operationsHistory}

                На основе этих данных дай детальный прогноз по остаткам на ближайшие 30 дней.
                Учти:
                - Сезонность спроса
                - Тренды в операциях
                - Возможные колебания
                - Рекомендации по закупкам

                Ответ должен быть структурированным и практичным.";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты эксперт по прогнозированию складских остатков. Давай детальные и практичные прогнозы на основе реальных данных.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 600,
                'temperature' => 0.6
            ]);

            $forecast = $response->choices[0]->message->content;

            // Создаем уведомление с прогнозом
            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_INFO,
                'message' => "Прогноз по товару {$product->name}:\n\n{$forecast}"
            ]);

            return $forecast;

        } catch (\Exception $e) {
            Log::error('Ошибка при прогнозировании остатков через AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Генерация общих рекомендаций
     */
    public function generateGeneralRecommendations($userId)
    {
        try {
            Log::info('AIService: Начинаем генерацию общих рекомендаций для пользователя ' . $userId);
            
            // Получаем все товары
            $allProducts = ProductSklad::with(['warehouse'])->get();
            Log::info('AIService: Получено товаров: ' . $allProducts->count());
            
            if ($allProducts->isEmpty()) {
                Log::info('AIService: Нет товаров для анализа');
                return "В системе нет товаров для анализа.";
            }

            // Анализируем остатки
            $totalProducts = $allProducts->count();
            $totalQuantity = $allProducts->sum('start_count');
            $lowStockCount = $allProducts->where('start_count', '>', 0)->where('start_count', '<=', 10)->count();
            $warehousesCount = $allProducts->pluck('warehouse_id')->unique()->count();
            
            // Получаем последние документы
            $recentDocuments = collect();
            $recentReceipts = Receipt::where('created_at', '>=', now()->subDays(30))->count();
            $recentWriteOffs = WriteOff::where('created_at', '>=', now()->subDays(30))->count();
            $recentTransfers = ProductTransfer::where('created_at', '>=', now()->subDays(30))->count();
            
            Log::info('AIService: Статистика - товаров: ' . $totalProducts . ', остаток: ' . $totalQuantity . ', низкий остаток: ' . $lowStockCount);
            
            // Формируем список товаров для анализа
            $productsList = $allProducts->take(10)->map(function ($item) {
                return "Товар: {$item->name}, Остаток: {$item->start_count}, Склад: " . ($item->warehouse ? $item->warehouse->name : 'Не указан');
            })->implode("\n");

            $prompt = "Проанализируй следующие данные и дай общие рекомендации по управлению запасами:

СТАТИСТИКА:
- Всего товаров: {$totalProducts}
- Общий остаток: {$totalQuantity}
- Товаров с низким остатком (1-10): {$lowStockCount}
- Количество складов: {$warehousesCount}
- Документов за 30 дней: оприходований {$recentReceipts}, списаний {$recentWriteOffs}, перемещений {$recentTransfers}

ТОВАРЫ (первые 10):
{$productsList}

Дай общие рекомендации по управлению запасами на русском языке. Включи:
1. Анализ и оптимизацию запасов
2. Управление поставками
3. Оптимизацию складских процессов
4. Мониторинг и контроль
5. Обучение персонала
6. Анализ эффективности
7. Сотрудничество с отделом продаж

Ответ должен быть структурированным и практичным.";

            Log::info('AIService: Отправляем запрос к OpenAI');
            
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты эксперт по управлению складскими запасами. Давай детальные и практичные рекомендации на основе реальных данных.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 800,
                'temperature' => 0.7
            ]);
            
            Log::info('AIService: Получен ответ от OpenAI');
            
            $recommendations = $response->choices[0]->message->content;
            
            // Создаем уведомление с рекомендациями
            Notification::create([
                'user_id' => $userId,
                'type' => 'recommendation',
                'message' => $recommendations,
                'is_read' => false
            ]);
            
            Log::info('AIService: Уведомление создано успешно');
            
            return [
                'recommendations' => $recommendations,
                'statistics' => [
                    'total_products' => $totalProducts,
                    'total_quantity' => $totalQuantity,
                    'low_stock_count' => $lowStockCount,
                    'warehouses_count' => $warehousesCount,
                    'recent_documents' => [
                        'receipts' => $recentReceipts,
                        'write_offs' => $recentWriteOffs,
                        'transfers' => $recentTransfers
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('AIService: Ошибка при генерации рекомендаций: ' . $e->getMessage());
            Log::error('AIService: Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
} 