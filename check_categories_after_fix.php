<?php
/**
 * Проверка категорий товаров после ручного исправления
 * Запуск: php check_categories_after_fix.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class CategoriesChecker {
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl, $token) {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data && in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    public function checkCategoriesAfterFix() {
        echo "🔍 ПРОВЕРКА КАТЕГОРИЙ ПОСЛЕ ИСПРАВЛЕНИЯ\n";
        echo "==========================================\n\n";
        
        // 1. Получаем товары
        echo "📋 1. АНАЛИЗ ТОВАРОВ\n";
        echo "--------------------\n";
        
        $productsResponse = $this->makeRequest('/products');
        if ($productsResponse['code'] !== 200) {
            echo "❌ Ошибка получения товаров: HTTP {$productsResponse['code']}\n";
            return;
        }
        
        $products = $productsResponse['data']['data']['data'] ?? [];
        echo "Всего товаров: " . count($products) . "\n\n";
        
        // Статистика по категориям
        $withCategory = 0;
        $withSubcategory = 0;
        $withBoth = 0;
        $withoutCategories = 0;
        
        foreach ($products as $product) {
            $hasCategory = !empty($product['category']);
            $hasSubcategory = !empty($product['subcategory']);
            
            if ($hasCategory && $hasSubcategory) {
                $withBoth++;
            } elseif ($hasCategory) {
                $withCategory++;
            } elseif ($hasSubcategory) {
                $withSubcategory++;
            } else {
                $withoutCategories++;
            }
        }
        
        echo "📊 СТАТИСТИКА КАТЕГОРИЙ:\n";
        echo "  С категорией и подкатегорией: {$withBoth}\n";
        echo "  Только с категорией: {$withCategory}\n";
        echo "  Только с подкатегорией: {$withSubcategory}\n";
        echo "  Без категорий: {$withoutCategories}\n";
        echo "  Всего с категориями: " . ($withBoth + $withCategory + $withSubcategory) . "\n";
        echo "\n";
        
        // 2. Показываем товары с категориями
        echo "📋 2. ТОВАРЫ С КАТЕГОРИЯМИ\n";
        echo "----------------------------\n";
        
        $productsWithCategories = [];
        foreach ($products as $product) {
            if (!empty($product['category']) || !empty($product['subcategory'])) {
                $productsWithCategories[] = $product;
            }
        }
        
        echo "Найдено товаров с категориями: " . count($productsWithCategories) . "\n\n";
        
        foreach ($productsWithCategories as $product) {
            echo "🛍️ {$product['name']} (ID: {$product['id']})\n";
            echo "  Категория: " . ($product['category'] ?? 'NULL') . "\n";
            echo "  Подкатегория: " . ($product['subcategory'] ?? 'NULL') . "\n";
            echo "  Название категории: " . ($product['category_name'] ?? 'NULL') . "\n";
            echo "  Название подкатегории: " . ($product['subcategory_name'] ?? 'NULL') . "\n";
            echo "\n";
        }
        
        // 3. Показываем товары без категорий
        echo "📋 3. ТОВАРЫ БЕЗ КАТЕГОРИЙ\n";
        echo "----------------------------\n";
        
        $productsWithoutCategories = [];
        foreach ($products as $product) {
            if (empty($product['category']) && empty($product['subcategory'])) {
                $productsWithoutCategories[] = $product;
            }
        }
        
        echo "Найдено товаров без категорий: " . count($productsWithoutCategories) . "\n\n";
        
        foreach ($productsWithoutCategories as $product) {
            echo "🛍️ {$product['name']} (ID: {$product['id']})\n";
        }
        
        // 4. Проверяем настройки пользователя
        echo "\n📋 4. НАСТРОЙКИ ПОЛЬЗОВАТЕЛЯ\n";
        echo "-----------------------------\n";
        
        $settingsResponse = $this->makeRequest('/user/settings');
        if ($settingsResponse['code'] === 200) {
            $settings = $settingsResponse['data']['data']['personal'] ?? [];
            echo "Тип категорий: " . ($settings['cats_type'] ?? 'не указан') . "\n";
            
            if (isset($settings['product_fields_visibility'])) {
                $visibility = json_decode($settings['product_fields_visibility'], true);
                echo "Категории включены: " . ($visibility['categories'] ?? 'не указано') . "\n";
            }
        }
        
        // 5. Итоговая оценка
        echo "\n📋 5. ИТОГОВАЯ ОЦЕНКА\n";
        echo "----------------------\n";
        
        $totalWithCategories = $withBoth + $withCategory + $withSubcategory;
        $percentage = count($products) > 0 ? round(($totalWithCategories / count($products)) * 100, 1) : 0;
        
        echo "Процент товаров с категориями: {$percentage}%\n";
        
        if ($percentage >= 80) {
            echo "✅ Отлично! Большинство товаров имеют категории\n";
        } elseif ($percentage >= 50) {
            echo "⚠️  Хорошо, но можно улучшить\n";
        } else {
            echo "❌ Много товаров без категорий\n";
        }
        
        if ($withoutCategories > 0) {
            echo "\n💡 РЕКОМЕНДАЦИИ:\n";
            echo "- Добавьте категории для оставшихся {$withoutCategories} товаров\n";
            echo "- Проверьте, что категории соответствуют типу пользователя (system/user)\n";
        }
        
        echo "\n🎯 ГОТОВО К ПРОВЕРКЕ НА ФРОНТЕНДЕ!\n";
        echo "Теперь можно проверить отображение категорий на странице остатков.\n";
    }
}

// Запуск проверки
$checker = new CategoriesChecker($baseUrl, $token);
$checker->checkCategoriesAfterFix(); 