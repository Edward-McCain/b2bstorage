<?php
/**
 * Тест отображения категорий в модалке движений товаров
 * Запуск: php test_movements_categories.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class MovementsCategoriesTester {
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
    
    public function testMovementsCategories() {
        echo "🔍 ТЕСТ КАТЕГОРИЙ В МОДАЛКЕ ДВИЖЕНИЙ\n";
        echo "=======================================\n\n";
        
        // 1. Получаем товары с категориями
        echo "📋 1. ПОЛУЧЕНИЕ ТОВАРОВ С КАТЕГОРИЯМИ\n";
        echo "----------------------------------------\n";
        
        $productsResponse = $this->makeRequest('/products');
        if ($productsResponse['code'] !== 200) {
            echo "❌ Ошибка получения товаров: HTTP {$productsResponse['code']}\n";
            return;
        }
        
        $products = $productsResponse['data']['data']['data'] ?? [];
        echo "Всего товаров: " . count($products) . "\n\n";
        
        // Находим товары с категориями
        $productsWithCategories = [];
        foreach ($products as $product) {
            if (!empty($product['category']) || !empty($product['subcategory'])) {
                $productsWithCategories[] = $product;
            }
        }
        
        echo "Товаров с категориями: " . count($productsWithCategories) . "\n\n";
        
        if (empty($productsWithCategories)) {
            echo "⚠️  Нет товаров с категориями для тестирования\n";
            return;
        }
        
        // 2. Тестируем API движений для товара с категориями
        echo "📋 2. ТЕСТ API ДВИЖЕНИЙ\n";
        echo "------------------------\n";
        
        $testProduct = $productsWithCategories[0];
        echo "Тестируем товар: {$testProduct['name']} (ID: {$testProduct['id']})\n";
        echo "Категория: " . ($testProduct['category'] ?? 'NULL') . "\n";
        echo "Подкатегория: " . ($testProduct['subcategory'] ?? 'NULL') . "\n";
        echo "Название категории: " . ($testProduct['category_name'] ?? 'NULL') . "\n";
        echo "Название подкатегории: " . ($testProduct['subcategory_name'] ?? 'NULL') . "\n\n";
        
        // Тестируем API движений
        $movementsData = [
            'product_id' => $testProduct['id'],
            'page' => 1
        ];
        
        $movementsResponse = $this->makeRequest('/balances/movements', 'POST', $movementsData);
        
        if ($movementsResponse['code'] === 200) {
            $movements = $movementsResponse['data'];
            echo "✅ API движений работает\n";
            echo "Количество движений: " . count($movements['movements']['data'] ?? []) . "\n";
            
            if (isset($movements['product'])) {
                $product = $movements['product'];
                echo "\n📦 ДАННЫЕ ТОВАРА В ОТВЕТЕ:\n";
                echo "ID: {$product['id']}\n";
                echo "Название: {$product['name']}\n";
                echo "Артикул: " . ($product['article'] ?? 'NULL') . "\n";
                echo "Категория ID: " . ($product['category'] ?? 'NULL') . "\n";
                echo "Подкатегория ID: " . ($product['subcategory'] ?? 'NULL') . "\n";
                echo "Название категории: " . ($product['category_name'] ?? 'NULL') . "\n";
                echo "Название подкатегории: " . ($product['subcategory_name'] ?? 'NULL') . "\n";
                
                if (!empty($product['category_name']) || !empty($product['subcategory_name'])) {
                    echo "\n✅ КАТЕГОРИИ ОТОБРАЖАЮТСЯ КОРРЕКТНО!\n";
                } else {
                    echo "\n❌ КАТЕГОРИИ НЕ ОТОБРАЖАЮТСЯ\n";
                }
            }
        } else {
            echo "❌ Ошибка API движений: HTTP {$movementsResponse['code']}\n";
            if (isset($movementsResponse['data']['message'])) {
                echo "Сообщение: {$movementsResponse['data']['message']}\n";
            }
        }
        
        // 3. Тестируем админский API движений
        echo "\n📋 3. ТЕСТ АДМИНСКОГО API ДВИЖЕНИЙ\n";
        echo "-----------------------------------\n";
        
        $adminMovementsResponse = $this->makeRequest('/admin/balances/movements', 'POST', $movementsData);
        
        if ($adminMovementsResponse['code'] === 200) {
            $adminMovements = $adminMovementsResponse['data'];
            echo "✅ Админский API движений работает\n";
            
            if (isset($adminMovements['data']['product'])) {
                $adminProduct = $adminMovements['data']['product'];
                echo "\n📦 ДАННЫЕ ТОВАРА В АДМИНСКОМ ОТВЕТЕ:\n";
                echo "ID: {$adminProduct['id']}\n";
                echo "Название: {$adminProduct['name']}\n";
                echo "Артикул: " . ($adminProduct['article'] ?? 'NULL') . "\n";
                echo "Категория ID: " . ($adminProduct['category'] ?? 'NULL') . "\n";
                echo "Подкатегория ID: " . ($adminProduct['subcategory'] ?? 'NULL') . "\n";
                echo "Название категории: " . ($adminProduct['category_name'] ?? 'NULL') . "\n";
                echo "Название подкатегории: " . ($adminProduct['subcategory_name'] ?? 'NULL') . "\n";
                
                if (!empty($adminProduct['category_name']) || !empty($adminProduct['subcategory_name'])) {
                    echo "\n✅ КАТЕГОРИИ В АДМИНСКОЙ ПАНЕЛИ ОТОБРАЖАЮТСЯ КОРРЕКТНО!\n";
                } else {
                    echo "\n❌ КАТЕГОРИИ В АДМИНСКОЙ ПАНЕЛИ НЕ ОТОБРАЖАЮТСЯ\n";
                }
            }
        } else {
            echo "❌ Ошибка админского API движений: HTTP {$adminMovementsResponse['code']}\n";
            if (isset($adminMovementsResponse['data']['message'])) {
                echo "Сообщение: {$adminMovementsResponse['data']['message']}\n";
            }
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
        
        echo "\n🎯 ТЕСТ ЗАВЕРШЕН!\n";
        echo "Теперь можно проверить отображение категорий в модалке движений на фронтенде.\n";
    }
}

// Запуск теста
$tester = new MovementsCategoriesTester($baseUrl, $token);
$tester->testMovementsCategories(); 