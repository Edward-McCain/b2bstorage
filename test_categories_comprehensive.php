<?php
/**
 * Комплексные тесты интеграции пользовательских категорий
 * Запуск: php test_categories_comprehensive.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class CategoryIntegrationTest {
    private $baseUrl;
    private $token;
    private $testResults = [];
    
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
    
    private function addResult($testName, $result, $expected = null) {
        $success = $result['code'] >= 200 && $result['code'] < 300;
        $this->testResults[] = [
            'name' => $testName,
            'success' => $success,
            'code' => $result['code'],
            'data' => $result['data'],
            'expected' => $expected
        ];
        
        echo ($success ? "✅" : "❌") . " {$testName} (HTTP {$result['code']})\n";
        if (!$success) {
            echo "   Ошибка: " . ($result['data']['message'] ?? 'Неизвестная ошибка') . "\n";
        }
    }
    
    public function runAllTests() {
        echo "🧪 КОМПЛЕКСНОЕ ТЕСТИРОВАНИЕ ИНТЕГРАЦИИ КАТЕГОРИЙ\n";
        echo "==================================================\n\n";
        
        // 1. Тесты базовой функциональности
        $this->testBasicFunctionality();
        
        // 2. Тесты API категорий
        $this->testCategoriesAPI();
        
        // 3. Тесты пользовательских категорий
        $this->testUserCategories();
        
        // 4. Тесты валидации
        $this->testValidation();
        
        // 5. Тесты миграции данных
        $this->testDataMigration();
        
        // 6. Тесты товаров
        $this->testProducts();
        
        // 7. Вывод результатов
        $this->printResults();
    }
    
    private function testBasicFunctionality() {
        echo "📋 1. ТЕСТЫ БАЗОВОЙ ФУНКЦИОНАЛЬНОСТИ\n";
        echo "----------------------------------------\n";
        
        // Тест настроек пользователя
        $result = $this->makeRequest('/user/settings');
        $this->addResult('Получение настроек пользователя', $result);
        
        // Тест типа категорий
        $result = $this->makeRequest('/user/categories-type');
        $this->addResult('Получение типа категорий', $result);
        
        echo "\n";
    }
    
    private function testCategoriesAPI() {
        echo "📋 2. ТЕСТЫ API КАТЕГОРИЙ\n";
        echo "----------------------------\n";
        
        // Системные категории
        $result = $this->makeRequest('/categories');
        $this->addResult('Получение системных категорий', $result);
        
        if ($result['code'] === 200 && !empty($result['data']['data'])) {
            $firstCategory = $result['data']['data'][0];
            $categoryId = $firstCategory['category_id'];
            
            // Подкатегории для первой категории
            $result = $this->makeRequest("/categories/{$categoryId}/subcategories");
            $this->addResult("Получение подкатегорий для категории {$categoryId}", $result);
        }
        
        echo "\n";
    }
    
    private function testUserCategories() {
        echo "📋 3. ТЕСТЫ ПОЛЬЗОВАТЕЛЬСКИХ КАТЕГОРИЙ\n";
        echo "----------------------------------------\n";
        
        // Получение пользовательских категорий
        $result = $this->makeRequest('/user/categories');
        $this->addResult('Получение пользовательских категорий', $result);
        
        // Создание тестовой категории
        $testCategory = [
            'name' => 'Тестовая категория ' . date('Y-m-d H:i:s')
        ];
        $result = $this->makeRequest('/user/categories', 'POST', $testCategory);
        $this->addResult('Создание пользовательской категории', $result);
        
        if ($result['code'] === 201 || $result['code'] === 200) {
            $categoryId = $result['data']['data']['category_id'] ?? null;
            
            if ($categoryId) {
                // Создание подкатегории
                $testSubcategory = [
                    'name' => 'Тестовая подкатегория ' . date('Y-m-d H:i:s'),
                    'category_id' => $categoryId
                ];
                $result = $this->makeRequest('/user/subcategories', 'POST', $testSubcategory);
                $this->addResult('Создание пользовательской подкатегории', $result);
                
                // Получение подкатегорий
                $result = $this->makeRequest("/user/categories/{$categoryId}/subcategories");
                $this->addResult("Получение подкатегорий для пользовательской категории {$categoryId}", $result);
            }
        }
        
        echo "\n";
    }
    
    private function testValidation() {
        echo "📋 4. ТЕСТЫ ВАЛИДАЦИИ\n";
        echo "----------------------\n";
        
        // Тест с несуществующей категорией
        $invalidProduct = [
            'name' => 'Тестовый товар',
            'category_id' => 'non_existent_category',
            'subcategory_id' => 'non_existent_subcategory',
            'price' => 100,
            'start_count' => 1
        ];
        $result = $this->makeRequest('/products', 'POST', $invalidProduct);
        $this->addResult('Валидация несуществующей категории', $result, 'Ожидается ошибка 422');
        
        // Тест с пустыми категориями
        $validProduct = [
            'name' => 'Тестовый товар без категорий',
            'price' => 100,
            'start_count' => 1
        ];
        $result = $this->makeRequest('/products', 'POST', $validProduct);
        $this->addResult('Создание товара без категорий', $result);
        
        echo "\n";
    }
    
    private function testDataMigration() {
        echo "📋 5. ТЕСТЫ МИГРАЦИИ ДАННЫХ\n";
        echo "-------------------------------\n";
        
        // Проверка проблемных категорий
        $result = $this->makeRequest('/user/categories/check');
        $this->addResult('Проверка проблемных категорий', $result);
        
        // Статистика категорий
        $result = $this->makeRequest('/user/categories/stats');
        $this->addResult('Получение статистики категорий', $result);
        
        // Исправление категорий (если есть проблемы)
        $result = $this->makeRequest('/user/categories/fix', 'POST');
        $this->addResult('Исправление проблемных категорий', $result);
        
        echo "\n";
    }
    
    private function testProducts() {
        echo "📋 6. ТЕСТЫ ТОВАРОВ\n";
        echo "--------------------\n";
        
        // Получение товаров
        $result = $this->makeRequest('/products');
        $this->addResult('Получение списка товаров', $result);
        
        // Получение товаров с фильтрацией
        $result = $this->makeRequest('/products?limit=5');
        $this->addResult('Получение товаров с лимитом', $result);
        
        // Получение товаров с поиском
        $result = $this->makeRequest('/products?search=тест');
        $this->addResult('Поиск товаров', $result);
        
        echo "\n";
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ\n";
        echo "============================\n\n";
        
        $total = count($this->testResults);
        $success = count(array_filter($this->testResults, function($r) { return $r['success']; }));
        $failed = $total - $success;
        
        echo "Всего тестов: {$total}\n";
        echo "Успешных: {$success}\n";
        echo "Проваленных: {$failed}\n";
        echo "Процент успеха: " . round(($success / $total) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "❌ ПРОВАЛЕННЫЕ ТЕСТЫ:\n";
            echo "----------------------\n";
            foreach ($this->testResults as $result) {
                if (!$result['success']) {
                    echo "- {$result['name']} (HTTP {$result['code']})\n";
                    if (isset($result['data']['message'])) {
                        echo "  Ошибка: {$result['data']['message']}\n";
                    }
                }
            }
            echo "\n";
        }
        
        echo "✅ УСПЕШНЫЕ ТЕСТЫ:\n";
        echo "-------------------\n";
        foreach ($this->testResults as $result) {
            if ($result['success']) {
                echo "- {$result['name']} (HTTP {$result['code']})\n";
            }
        }
        
        echo "\n🎯 РЕКОМЕНДАЦИИ:\n";
        echo "-----------------\n";
        if ($failed === 0) {
            echo "✅ Все тесты прошли успешно! Система готова к использованию.\n";
        } else {
            echo "⚠️  Обнаружены проблемы. Проверьте:\n";
            echo "   - Запущен ли backend сервер\n";
            echo "   - Правильность токена авторизации\n";
            echo "   - Наличие необходимых таблиц в базе данных\n";
            echo "   - Логи сервера для детальной диагностики\n";
        }
    }
}

// Запуск тестов
$tester = new CategoryIntegrationTest($baseUrl, $token);
$tester->runAllTests(); 