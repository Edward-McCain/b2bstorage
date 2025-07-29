<?php
/**
 * Тест отображения категорий в списке товаров
 * Запуск: php test_categories_display.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class CategoriesDisplayTester {
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
    
    public function testCategoriesDisplay() {
        echo "🔍 ТЕСТ ОТОБРАЖЕНИЯ КАТЕГОРИЙ В СПИСКЕ ТОВАРОВ\n";
        echo "================================================\n\n";
        
        // 1. Получаем настройки пользователя
        echo "📋 1. НАСТРОЙКИ ПОЛЬЗОВАТЕЛЯ\n";
        echo "-----------------------------\n";
        
        $settingsResponse = $this->makeRequest('/user/settings');
        if ($settingsResponse['code'] === 200) {
            $settings = $settingsResponse['data']['data'] ?? [];
            $catsType = $settings['cats_type'] ?? 'system';
            echo "Тип категорий: {$catsType}\n";
            echo "Видимость категорий: " . ($settings['product_fields_visibility']['categories'] ?? 'неизвестно') . "\n";
        } else {
            echo "❌ Ошибка получения настроек: HTTP {$settingsResponse['code']}\n";
        }
        
        // 2. Получаем список товаров
        echo "\n📋 2. СПИСОК ТОВАРОВ С КАТЕГОРИЯМИ\n";
        echo "------------------------------------\n";
        
        $productsResponse = $this->makeRequest('/products?per_page=10');
        if ($productsResponse['code'] !== 200) {
            echo "❌ Ошибка получения товаров: HTTP {$productsResponse['code']}\n";
            return;
        }
        
        $products = $productsResponse['data']['data'] ?? [];
        echo "Найдено товаров: " . count($products) . "\n\n";
        
        if (empty($products)) {
            echo "⚠️  Нет товаров для тестирования\n";
            return;
        }
        
        // 3. Анализируем категории в товарах
        $categoriesWithNames = 0;
        $categoriesWithIds = 0;
        $subcategoriesWithNames = 0;
        $subcategoriesWithIds = 0;
        
        foreach ($products as $product) {
            echo "📦 Товар: {$product['name']} (ID: {$product['id']})\n";
            echo "  Категория ID: {$product['category']}\n";
            echo "  Категория название: " . ($product['category_name'] ?? 'null') . "\n";
            echo "  Подкатегория ID: {$product['subcategory']}\n";
            echo "  Подкатегория название: " . ($product['subcategory_name'] ?? 'null') . "\n";
            
            // Подсчитываем статистику
            if ($product['category_name'] && $product['category_name'] !== $product['category']) {
                $categoriesWithNames++;
            } else {
                $categoriesWithIds++;
            }
            
            if ($product['subcategory_name'] && $product['subcategory_name'] !== $product['subcategory']) {
                $subcategoriesWithNames++;
            } else {
                $subcategoriesWithIds++;
            }
            
            echo "\n";
        }
        
        // 4. Выводим статистику
        echo "📋 3. СТАТИСТИКА КАТЕГОРИЙ\n";
        echo "---------------------------\n";
        echo "Категории с названиями: {$categoriesWithNames}\n";
        echo "Категории с ID: {$categoriesWithIds}\n";
        echo "Подкатегории с названиями: {$subcategoriesWithNames}\n";
        echo "Подкатегории с ID: {$subcategoriesWithIds}\n";
        
        // 5. Проверяем системные категории
        echo "\n📋 4. ПРОВЕРКА СИСТЕМНЫХ КАТЕГОРИЙ\n";
        echo "------------------------------------\n";
        
        $systemCategoriesResponse = $this->makeRequest('/categories');
        if ($systemCategoriesResponse['code'] === 200) {
            $systemCategories = $systemCategoriesResponse['data']['data'] ?? [];
            echo "Системных категорий: " . count($systemCategories) . "\n";
            
            // Показываем первые 5 категорий
            for ($i = 0; $i < min(5, count($systemCategories)); $i++) {
                $cat = $systemCategories[$i];
                echo "  {$cat['category_id']}: {$cat['name_ru']}\n";
            }
        } else {
            echo "❌ Ошибка получения системных категорий: HTTP {$systemCategoriesResponse['code']}\n";
        }
        
        // 6. Проверяем пользовательские категории
        echo "\n📋 5. ПРОВЕРКА ПОЛЬЗОВАТЕЛЬСКИХ КАТЕГОРИЙ\n";
        echo "-----------------------------------------\n";
        
        $userCategoriesResponse = $this->makeRequest('/user/categories');
        if ($userCategoriesResponse['code'] === 200) {
            $userCategories = $userCategoriesResponse['data']['data'] ?? [];
            echo "Пользовательских категорий: " . count($userCategories) . "\n";
            
            // Показываем первые 5 категорий
            for ($i = 0; $i < min(5, count($userCategories)); $i++) {
                $cat = $userCategories[$i];
                echo "  {$cat['category_id']}: {$cat['name']}\n";
            }
        } else {
            echo "❌ Ошибка получения пользовательских категорий: HTTP {$userCategoriesResponse['code']}\n";
        }
        
        echo "\n🎯 ТЕСТ ЗАВЕРШЕН!\n";
    }
}

// Запуск теста
$tester = new CategoriesDisplayTester($baseUrl, $token);
$tester->testCategoriesDisplay(); 