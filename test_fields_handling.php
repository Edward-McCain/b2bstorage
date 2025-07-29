<?php
/**
 * Тест обработки полей в товарах
 * Запуск: php test_fields_handling.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class FieldsHandlingTester {
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
    
    public function testFieldsHandling() {
        echo "🔍 ТЕСТ ОБРАБОТКИ ПОЛЕЙ В ТОВАРАХ\n";
        echo "====================================\n\n";
        
        // 1. Получаем список товаров с полями
        echo "📋 1. ПОЛУЧЕНИЕ ТОВАРОВ С ПОЛЯМИ\n";
        echo "----------------------------------\n";
        
        $productsResponse = $this->makeRequest('/products?per_page=3');
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
        
        // 2. Анализируем поля в товарах
        foreach ($products as $product) {
            echo "📦 Товар: {$product['name']} (ID: {$product['id']})\n";
            
            if (isset($product['fields']) && !empty($product['fields'])) {
                echo "  🔧 Поля: " . json_encode($product['fields'], JSON_UNESCAPED_UNICODE) . "\n";
                
                // Проверяем тип поля fields
                if (is_array($product['fields'])) {
                    echo "  ✅ Поле 'fields' является массивом\n";
                } else {
                    echo "  ❌ Поле 'fields' НЕ является массивом (тип: " . gettype($product['fields']) . ")\n";
                }
            } else {
                echo "  ⚠️  Поля отсутствуют\n";
            }
            echo "\n";
        }
        
        // 3. Получаем кастомные поля
        echo "📋 2. ПОЛУЧЕНИЕ КАСТОМНЫХ ПОЛЕЙ\n";
        echo "----------------------------------\n";
        
        $fieldsResponse = $this->makeRequest('/product-fields');
        if ($fieldsResponse['code'] === 200) {
            $fields = $fieldsResponse['data']['data'] ?? [];
            echo "Найдено кастомных полей: " . count($fields) . "\n";
            
            foreach ($fields as $field) {
                echo "  🔧 {$field['field_name']} (ID: {$field['id']})\n";
            }
        } else {
            echo "❌ Ошибка получения кастомных полей: HTTP {$fieldsResponse['code']}\n";
        }
        
        echo "\n📋 3. ТЕСТИРОВАНИЕ ОБРАБОТКИ ПОЛЕЙ\n";
        echo "--------------------------------------\n";
        
        // Проверяем, что поля правильно обрабатываются
        $testProduct = $products[0];
        if (isset($testProduct['fields']) && is_array($testProduct['fields'])) {
            echo "✅ Поле 'fields' корректно приведено к массиву\n";
            
            // Проверяем доступ к элементам массива
            $fieldKeys = array_keys($testProduct['fields']);
            if (!empty($fieldKeys)) {
                echo "  Ключи полей: " . implode(', ', $fieldKeys) . "\n";
                
                // Тестируем проверку существования ключа
                $testKey = $fieldKeys[0];
                if (array_key_exists($testKey, $testProduct['fields'])) {
                    echo "  ✅ Ключ '{$testKey}' существует в массиве\n";
                } else {
                    echo "  ❌ Ключ '{$testKey}' НЕ существует в массиве\n";
                }
            } else {
                echo "  ⚠️  Массив полей пуст\n";
            }
        } else {
            echo "❌ Поле 'fields' не является массивом\n";
        }
        
        echo "\n🎯 ТЕСТ ЗАВЕРШЕН!\n";
    }
}

// Запуск теста
$tester = new FieldsHandlingTester($baseUrl, $token);
$tester->testFieldsHandling(); 