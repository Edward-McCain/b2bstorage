<?php
/**
 * Тест удаления товаров
 * Запуск: php test_product_delete.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class ProductDeleteTester {
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
    
    public function testProductDelete() {
        echo "🔍 ТЕСТ УДАЛЕНИЯ ТОВАРОВ\n";
        echo "=========================\n\n";
        
        // 1. Получаем список товаров
        echo "📋 1. ПОЛУЧЕНИЕ СПИСКА ТОВАРОВ\n";
        echo "--------------------------------\n";
        
        $productsResponse = $this->makeRequest('/products?per_page=5');
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
        
        // 2. Показываем товары
        foreach ($products as $product) {
            echo "📦 Товар: {$product['name']} (ID: {$product['id']})\n";
        }
        
        // 3. Тестируем удаление товара
        echo "\n📋 2. ТЕСТ УДАЛЕНИЯ ТОВАРА\n";
        echo "-----------------------------\n";
        
        $testProduct = $products[0];
        echo "Тестируем удаление товара: {$testProduct['name']} (ID: {$testProduct['id']})\n\n";
        
        $deleteResponse = $this->makeRequest("/products/{$testProduct['id']}", 'DELETE');
        
        if ($deleteResponse['code'] === 200) {
            echo "✅ Товар успешно удален!\n";
            echo "Ответ: " . json_encode($deleteResponse['data'], JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "❌ Ошибка удаления товара: HTTP {$deleteResponse['code']}\n";
            if (isset($deleteResponse['data']['error'])) {
                echo "Ошибка: {$deleteResponse['data']['error']}\n";
            }
            if (isset($deleteResponse['data']['details'])) {
                echo "Детали: {$deleteResponse['data']['details']}\n";
            }
        }
        
        // 4. Проверяем, что товар действительно удален
        echo "\n📋 3. ПРОВЕРКА УДАЛЕНИЯ\n";
        echo "-------------------------\n";
        
        $productAfterResponse = $this->makeRequest("/products/{$testProduct['id']}");
        if ($productAfterResponse['code'] === 404) {
            echo "✅ Товар успешно удален из базы данных\n";
        } else {
            echo "❌ Товар все еще существует в базе данных\n";
        }
        
        // 5. Проверяем связанные записи
        echo "\n📋 4. ПРОВЕРКА СВЯЗАННЫХ ЗАПИСЕЙ\n";
        echo "------------------------------------\n";
        
        $relatedTables = [
            'write_off_positions' => 'Позиции списаний',
            'receipt_positions' => 'Позиции оприходований',
            'inventory_items' => 'Позиции инвентаризации',
            'product_transfer_positions' => 'Позиции перемещений',
            'product_operations' => 'Операции',
            'product_balances' => 'Остатки',
            'product_images' => 'Изображения'
        ];
        
        foreach ($relatedTables as $table => $description) {
            $checkResponse = $this->makeRequest("/check-related-records?table={$table}&product_id={$testProduct['id']}");
            if ($checkResponse['code'] === 200) {
                $count = $checkResponse['data']['count'] ?? 0;
                if ($count === 0) {
                    echo "✅ {$description}: удалены\n";
                } else {
                    echo "❌ {$description}: осталось {$count} записей\n";
                }
            } else {
                echo "⚠️  {$description}: не удалось проверить\n";
            }
        }
        
        echo "\n🎯 ТЕСТ ЗАВЕРШЕН!\n";
    }
}

// Запуск теста
$tester = new ProductDeleteTester($baseUrl, $token);
$tester->testProductDelete(); 