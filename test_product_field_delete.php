<?php
/**
 * Тест удаления кастомных полей товаров
 * Запуск: php test_product_field_delete.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class ProductFieldDeleteTester {
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
    
    public function testProductFieldDelete() {
        echo "🔍 ТЕСТ УДАЛЕНИЯ КАСТОМНЫХ ПОЛЕЙ\n";
        echo "=====================================\n\n";
        
        // 1. Получаем список кастомных полей
        echo "📋 1. ПОЛУЧЕНИЕ КАСТОМНЫХ ПОЛЕЙ\n";
        echo "---------------------------------\n";
        
        $fieldsResponse = $this->makeRequest('/product-fields');
        if ($fieldsResponse['code'] !== 200) {
            echo "❌ Ошибка получения полей: HTTP {$fieldsResponse['code']}\n";
            return;
        }
        
        $fields = $fieldsResponse['data']['data'] ?? [];
        echo "Найдено кастомных полей: " . count($fields) . "\n\n";
        
        if (empty($fields)) {
            echo "⚠️  Нет кастомных полей для тестирования\n";
            return;
        }
        
        // 2. Показываем поля
        foreach ($fields as $field) {
            echo "🔧 Поле: {$field['field_name']} (ID: {$field['id']})\n";
        }
        
        // 3. Тестируем удаление поля
        echo "\n📋 2. ТЕСТ УДАЛЕНИЯ ПОЛЯ\n";
        echo "---------------------------\n";
        
        $testField = $fields[0];
        echo "Тестируем удаление поля: {$testField['field_name']} (ID: {$testField['id']})\n\n";
        
        $deleteResponse = $this->makeRequest("/product-fields/{$testField['id']}", 'DELETE');
        
        if ($deleteResponse['code'] === 200) {
            echo "✅ Поле успешно удалено!\n";
            echo "Ответ: " . json_encode($deleteResponse['data'], JSON_UNESCAPED_UNICODE) . "\n";
        } elseif ($deleteResponse['code'] === 400) {
            echo "⚠️  Поле не удалено - используется в товарах\n";
            echo "Сообщение: " . ($deleteResponse['data']['message'] ?? 'Неизвестная ошибка') . "\n";
        } else {
            echo "❌ Ошибка удаления поля: HTTP {$deleteResponse['code']}\n";
            if (isset($deleteResponse['data']['message'])) {
                echo "Сообщение: {$deleteResponse['data']['message']}\n";
            }
            if (isset($deleteResponse['data']['exception'])) {
                echo "Исключение: {$deleteResponse['data']['exception']}\n";
            }
        }
        
        // 4. Проверяем, что поле действительно удалено
        echo "\n📋 3. ПРОВЕРКА УДАЛЕНИЯ\n";
        echo "-------------------------\n";
        
        $fieldsAfterResponse = $this->makeRequest('/product-fields');
        if ($fieldsAfterResponse['code'] === 200) {
            $fieldsAfter = $fieldsAfterResponse['data']['data'] ?? [];
            $fieldStillExists = false;
            
            foreach ($fieldsAfter as $field) {
                if ($field['id'] == $testField['id']) {
                    $fieldStillExists = true;
                    break;
                }
            }
            
            if (!$fieldStillExists) {
                echo "✅ Поле успешно удалено из базы данных\n";
            } else {
                echo "❌ Поле все еще существует в базе данных\n";
            }
        }
        
        echo "\n🎯 ТЕСТ ЗАВЕРШЕН!\n";
    }
}

// Запуск теста
$tester = new ProductFieldDeleteTester($baseUrl, $token);
$tester->testProductFieldDelete(); 