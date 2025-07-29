<?php
/**
 * Диагностика проблемы с отображением категорий товаров
 * Запуск: php debug_categories_issue.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class CategoriesDebugger {
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
    
    public function debugCategoriesIssue() {
        echo "🔍 ДИАГНОСТИКА ПРОБЛЕМЫ С КАТЕГОРИЯМИ ТОВАРОВ\n";
        echo "================================================\n\n";
        
        // 1. Получаем товары пользователя
        echo "📋 1. АНАЛИЗ ТОВАРОВ ПОЛЬЗОВАТЕЛЯ\n";
        echo "----------------------------------\n";
        
        $productsResponse = $this->makeRequest('/products');
        if ($productsResponse['code'] !== 200) {
            echo "❌ Ошибка получения товаров: HTTP {$productsResponse['code']}\n";
            return;
        }
        
        $products = $productsResponse['data']['data']['data'] ?? [];
        echo "Найдено товаров: " . count($products) . "\n\n";
        
        // Анализируем каждый товар
        foreach ($products as $product) {
            echo "🛍️ Товар: {$product['name']} (ID: {$product['id']})\n";
            echo "   Категория: " . ($product['category'] ?? 'NULL') . "\n";
            echo "   Подкатегория: " . ($product['subcategory'] ?? 'NULL') . "\n";
            echo "   category_name: " . ($product['category_name'] ?? 'NULL') . "\n";
            echo "   subcategory_name: " . ($product['subcategory_name'] ?? 'NULL') . "\n";
            echo "   category_relation: " . ($product['category_relation'] ? 'Есть' : 'NULL') . "\n";
            echo "   subcategory_relation: " . ($product['subcategory_relation'] ? 'Есть' : 'NULL') . "\n";
            echo "\n";
        }
        
        // 2. Получаем системные категории
        echo "📋 2. АНАЛИЗ СИСТЕМНЫХ КАТЕГОРИЙ\n";
        echo "----------------------------------\n";
        
        $systemCategoriesResponse = $this->makeRequest('/categories');
        if ($systemCategoriesResponse['code'] === 200) {
            $systemCategories = $systemCategoriesResponse['data']['data'] ?? [];
            echo "Системных категорий: " . count($systemCategories) . "\n";
            
            // Создаем массив для быстрого поиска
            $systemCategoriesMap = [];
            foreach ($systemCategories as $cat) {
                $systemCategoriesMap[$cat['category_id']] = $cat['name'];
            }
            
            echo "Примеры системных категорий:\n";
            $count = 0;
            foreach ($systemCategoriesMap as $id => $name) {
                if ($count < 5) {
                    echo "   {$id} => {$name}\n";
                    $count++;
                }
            }
            echo "\n";
        }
        
        // 3. Получаем пользовательские категории
        echo "📋 3. АНАЛИЗ ПОЛЬЗОВАТЕЛЬСКИХ КАТЕГОРИЙ\n";
        echo "----------------------------------------\n";
        
        $userCategoriesResponse = $this->makeRequest('/user/categories');
        if ($userCategoriesResponse['code'] === 200) {
            $userCategories = $userCategoriesResponse['data']['data'] ?? [];
            echo "Пользовательских категорий: " . count($userCategories) . "\n";
            
            // Создаем массив для быстрого поиска
            $userCategoriesMap = [];
            foreach ($userCategories as $cat) {
                $userCategoriesMap[$cat['category_id']] = $cat['name'];
            }
            
            echo "Пользовательские категории:\n";
            foreach ($userCategoriesMap as $id => $name) {
                echo "   {$id} => {$name}\n";
            }
            echo "\n";
        }
        
        // 4. Анализируем соответствие
        echo "📋 4. АНАЛИЗ СООТВЕТСТВИЯ КАТЕГОРИЙ\n";
        echo "------------------------------------\n";
        
        $userSettingsResponse = $this->makeRequest('/user/settings');
        $catsType = 'system';
        if ($userSettingsResponse['code'] === 200) {
            $catsType = $userSettingsResponse['data']['data']['personal']['cats_type'] ?? 'system';
        }
        
        echo "Тип категорий пользователя: {$catsType}\n\n";
        
        $categoriesMap = ($catsType === 'user') ? $userCategoriesMap : $systemCategoriesMap;
        $categoriesSource = ($catsType === 'user') ? 'пользовательские' : 'системные';
        
        echo "Используются {$categoriesSource} категории:\n";
        
        $foundCategories = 0;
        $missingCategories = 0;
        
        foreach ($products as $product) {
            $categoryId = $product['category'];
            $subcategoryId = $product['subcategory'];
            
            if ($categoryId) {
                if (isset($categoriesMap[$categoryId])) {
                    echo "✅ Категория '{$categoryId}' найдена: {$categoriesMap[$categoryId]}\n";
                    $foundCategories++;
                } else {
                    echo "❌ Категория '{$categoryId}' НЕ найдена в {$categoriesSource} категориях\n";
                    $missingCategories++;
                }
            }
            
            if ($subcategoryId) {
                // Проверяем подкатегории
                $subcategoriesResponse = $this->makeRequest("/categories/{$categoryId}/subcategories");
                if ($subcategoriesResponse['code'] === 200) {
                    $subcategories = $subcategoriesResponse['data']['data'] ?? [];
                    $subcategoryFound = false;
                    foreach ($subcategories as $sub) {
                        if ($sub['subcategory_id'] === $subcategoryId) {
                            echo "✅ Подкатегория '{$subcategoryId}' найдена: {$sub['name']}\n";
                            $subcategoryFound = true;
                            break;
                        }
                    }
                    if (!$subcategoryFound) {
                        echo "❌ Подкатегория '{$subcategoryId}' НЕ найдена\n";
                    }
                }
            }
        }
        
        echo "\n📊 СТАТИСТИКА:\n";
        echo "Найдено категорий: {$foundCategories}\n";
        echo "Отсутствует категорий: {$missingCategories}\n";
        echo "Всего товаров с категориями: " . ($foundCategories + $missingCategories) . "\n";
        
        // 5. Проверяем проблемные товары
        echo "\n📋 5. ПРОБЛЕМНЫЕ ТОВАРЫ\n";
        echo "-------------------------\n";
        
        foreach ($products as $product) {
            $categoryId = $product['category'];
            $subcategoryId = $product['subcategory'];
            
            if ($categoryId && !isset($categoriesMap[$categoryId])) {
                echo "🚨 ПРОБЛЕМА: Товар '{$product['name']}' (ID: {$product['id']})\n";
                echo "   Категория '{$categoryId}' не найдена в {$categoriesSource} категориях\n";
                echo "   Подкатегория: " . ($subcategoryId ?? 'NULL') . "\n";
                echo "   Возможные причины:\n";
                echo "   - Категория была удалена\n";
                echo "   - Неправильный тип категорий (system/user)\n";
                echo "   - Проблема в миграции данных\n";
                echo "\n";
            }
        }
        
        // 6. Рекомендации
        echo "📋 6. РЕКОМЕНДАЦИИ\n";
        echo "-------------------\n";
        
        if ($missingCategories > 0) {
            echo "⚠️  Обнаружены проблемы с категориями!\n";
            echo "Рекомендуемые действия:\n";
            echo "1. Проверить настройки пользователя (cats_type)\n";
            echo "2. Запустить миграцию категорий: /user/categories/fix\n";
            echo "3. Проверить соответствие типов категорий\n";
            echo "4. Очистить кэш категорий на фронтенде\n";
        } else {
            echo "✅ Все категории найдены корректно!\n";
        }
        
        echo "\n🔍 Дополнительная диагностика:\n";
        echo "- Проверьте настройки пользователя: /user/settings\n";
        echo "- Проверьте статистику категорий: /user/categories/stats\n";
        echo "- Проверьте проблемные категории: /user/categories/check\n";
    }
}

// Запуск диагностики
$debugger = new CategoriesDebugger($baseUrl, $token);
$debugger->debugCategoriesIssue(); 