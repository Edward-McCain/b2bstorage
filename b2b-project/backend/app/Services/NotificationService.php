<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ProductSklad;
use App\Models\Receipt;
use App\Models\WriteOff;
use App\Models\ProductTransfer;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Проверка низких остатков и создание уведомлений
     */
    public function checkLowStock($userId)
    {
        try {
            $lowStockProducts = ProductSklad::where('quantity', '<=', 10)
                ->where('quantity', '>', 0)
                ->with(['product', 'warehouse'])
                ->get();

            foreach ($lowStockProducts as $product) {
                $message = "Товар '{$product->product->name}' на складе '{$product->warehouse->name}' имеет низкий остаток: {$product->quantity} шт. Рекомендуем пополнить запасы.";

                Notification::create([
                    'user_id' => $userId,
                    'type' => Notification::TYPE_LOW_STOCK,
                    'message' => $message
                ]);
            }

            return count($lowStockProducts);

        } catch (\Exception $e) {
            Log::error('Ошибка при проверке низких остатков: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Проверка просроченных документов
     */
    public function checkOverdueDocuments($userId)
    {
        try {
            $notifications = [];

            // Проверяем просроченные оприходования
            $overdueReceipts = Receipt::where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(7))
                ->count();

            if ($overdueReceipts > 0) {
                $notifications[] = "У вас {$overdueReceipts} необработанных оприходований старше 7 дней.";
            }

            // Проверяем просроченные списания
            $overdueWriteOffs = WriteOff::where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(7))
                ->count();

            if ($overdueWriteOffs > 0) {
                $notifications[] = "У вас {$overdueWriteOffs} необработанных списаний старше 7 дней.";
            }

            // Проверяем незавершенные перемещения
            $overdueTransfers = ProductTransfer::where('status', 'in_progress')
                ->where('created_at', '<=', now()->subDays(3))
                ->count();

            if ($overdueTransfers > 0) {
                $notifications[] = "У вас {$overdueTransfers} незавершенных перемещений старше 3 дней.";
            }

            // Создаем уведомления
            foreach ($notifications as $message) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => Notification::TYPE_OVERDUE,
                    'message' => $message
                ]);
            }

            return count($notifications);

        } catch (\Exception $e) {
            Log::error('Ошибка при проверке просроченных документов: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Создание рекомендаций по закупкам
     */
    public function createPurchaseRecommendations($userId)
    {
        try {
            // Получаем товары с нулевыми остатками
            $outOfStockProducts = ProductSklad::where('quantity', 0)
                ->with(['product', 'warehouse'])
                ->get();

            if ($outOfStockProducts->isEmpty()) {
                return 0;
            }

            $productList = $outOfStockProducts->map(function ($product) {
                return "• {$product->product->name} (склад: {$product->warehouse->name})";
            })->implode("\n");

            $message = "Рекомендации по закупкам:\n\nТовары с нулевыми остатками:\n{$productList}\n\nРекомендуем пополнить эти товары в ближайшее время.";

            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_RECOMMENDATION,
                'message' => $message
            ]);

            return 1;

        } catch (\Exception $e) {
            Log::error('Ошибка при создании рекомендаций по закупкам: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Анализ продаж и создание уведомлений
     */
    public function analyzeSales($userId)
    {
        try {
            // Получаем товары с высокими остатками (возможно, медленно продающиеся)
            $highStockProducts = ProductSklad::where('quantity', '>=', 100)
                ->with(['product', 'warehouse'])
                ->get();

            if ($highStockProducts->isEmpty()) {
                return 0;
            }

            $productList = $highStockProducts->take(5)->map(function ($product) {
                return "• {$product->product->name}: {$product->quantity} шт. (склад: {$product->warehouse->name})";
            })->implode("\n");

            $message = "Анализ продаж:\n\nТовары с высокими остатками:\n{$productList}\n\nРекомендуем проанализировать спрос на эти товары.";

            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_RECOMMENDATION,
                'message' => $message
            ]);

            return 1;

        } catch (\Exception $e) {
            Log::error('Ошибка при анализе продаж: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Создание информационных уведомлений
     */
    public function createInfoNotifications($userId)
    {
        try {
            $notifications = [];

            // Статистика по товарам
            $totalProducts = ProductSklad::count();
            $lowStockCount = ProductSklad::where('quantity', '<=', 10)->count();
            $outOfStockCount = ProductSklad::where('quantity', 0)->count();

            if ($totalProducts > 0) {
                $notifications[] = "Статистика склада: всего товаров - {$totalProducts}, с низкими остатками - {$lowStockCount}, отсутствует - {$outOfStockCount}.";
            }

            // Создаем уведомления
            foreach ($notifications as $message) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => Notification::TYPE_INFO,
                    'message' => $message
                ]);
            }

            return count($notifications);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании информационных уведомлений: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Комплексная проверка и создание уведомлений
     */
    public function comprehensiveCheck($userId)
    {
        try {
            $totalNotifications = 0;

            // Проверяем низкие остатки
            $totalNotifications += $this->checkLowStock($userId);

            // Проверяем просроченные документы
            $totalNotifications += $this->checkOverdueDocuments($userId);

            // Создаем рекомендации по закупкам
            $totalNotifications += $this->createPurchaseRecommendations($userId);

            // Анализируем продажи
            $totalNotifications += $this->analyzeSales($userId);

            // Создаем информационные уведомления
            $totalNotifications += $this->createInfoNotifications($userId);

            Log::info("Создано {$totalNotifications} уведомлений для пользователя {$userId}");

            return $totalNotifications;

        } catch (\Exception $e) {
            Log::error('Ошибка при комплексной проверке уведомлений: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Создание уведомления о новом документе
     */
    public function createDocumentNotification($userId, $documentType, $documentId, $message)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_INFO,
                'message' => $message
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Ошибка при создании уведомления о документе: ' . $e->getMessage());
            return false;
        }
    }
} 