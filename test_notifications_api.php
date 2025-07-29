<?php

// Тест API уведомлений с фильтрами

class NotificationsApiTest {
    private $baseUrl = 'http://localhost:8000/api';
    
    public function runTests() {
        echo "\n🔍 ТЕСТ API УВЕДОМЛЕНИЙ С ФИЛЬТРАМИ\n";
        echo "=====================================\n\n";
        
        // Тест 1: Без фильтров
        $this->testNoFilters();
        
        // Тест 2: Фильтр по типу
        $this->testTypeFilter();
        
        // Тест 3: Фильтр по статусу прочтения
        $this->testReadStatusFilter();
        
        // Тест 4: Комбинированные фильтры
        $this->testCombinedFilters();
        
        // Вывод результатов
        $this->printResults();
    }
    
    private function testNoFilters() {
        echo "📋 ТЕСТ 1: Без фильтров\n";
        echo "------------------------\n";
        
        $url = "{$this->baseUrl}/notifications";
        echo "URL: {$url}\n";
        
        $response = $this->makeRequest($url);
        $this->printResponse($response, 'Все уведомления');
        
        echo "\n";
    }
    
    private function testTypeFilter() {
        echo "📋 ТЕСТ 2: Фильтр по типу\n";
        echo "--------------------------\n";
        
        $types = ['info', 'warning', 'recommendation', 'low_stock', 'overdue'];
        
        foreach ($types as $type) {
            $url = "{$this->baseUrl}/notifications?type={$type}";
            echo "URL: {$url}\n";
            
            $response = $this->makeRequest($url);
            $this->printResponse($response, "Тип: {$type}");
            
            echo "\n";
        }
    }
    
    private function testReadStatusFilter() {
        echo "📋 ТЕСТ 3: Фильтр по статусу прочтения\n";
        echo "--------------------------------------\n";
        
        $statuses = ['true', 'false'];
        
        foreach ($statuses as $status) {
            $url = "{$this->baseUrl}/notifications?is_read={$status}";
            echo "URL: {$url}\n";
            
            $response = $this->makeRequest($url);
            $this->printResponse($response, "Статус: {$status}");
            
            echo "\n";
        }
    }
    
    private function testCombinedFilters() {
        echo "📋 ТЕСТ 4: Комбинированные фильтры\n";
        echo "----------------------------------\n";
        
        $testCases = [
            ['type' => 'recommendation', 'is_read' => 'false'],
            ['type' => 'recommendation', 'is_read' => 'true'],
            ['type' => 'info', 'is_read' => 'false']
        ];
        
        foreach ($testCases as $testCase) {
            $params = http_build_query($testCase);
            $url = "{$this->baseUrl}/notifications?{$params}";
            echo "URL: {$url}\n";
            
            $response = $this->makeRequest($url);
            $this->printResponse($response, "Тип: {$testCase['type']}, Статус: {$testCase['is_read']}");
            
            echo "\n";
        }
    }
    
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'response' => $response
        ];
    }
    
    private function printResponse($result, $description) {
        echo "HTTP Code: {$result['http_code']}\n";
        
        if ($result['response']) {
            $data = json_decode($result['response'], true);
            if ($data) {
                echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
                if (isset($data['data'])) {
                    echo "Count: " . count($data['data']) . "\n";
                    if (isset($data['unread_count'])) {
                        echo "Unread count: " . $data['unread_count'] . "\n";
                    }
                    
                    // Показываем первые 3 уведомления
                    if (!empty($data['data'])) {
                        echo "Sample notifications:\n";
                        $sample = array_slice($data['data'], 0, 3);
                        foreach ($sample as $notification) {
                            echo "  - ID: {$notification['id']}, Type: {$notification['type']}, Read: " . ($notification['is_read'] ? 'true' : 'false') . "\n";
                        }
                    }
                }
            } else {
                echo "Failed to decode JSON\n";
                echo "Raw response: {$result['response']}\n";
            }
        } else {
            echo "No response\n";
        }
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ API\n";
        echo "==============================\n";
        
        echo "🔍 ПРОБЛЕМЫ:\n";
        echo "1. Фильтр по типу может не работать из-за неправильной передачи параметров\n";
        echo "2. Фильтр по статусу прочтения может не работать из-за проблем с типами данных\n";
        echo "3. Multiselect возвращает объекты, а не строки\n";
        echo "\n";
        
        echo "🔧 РЕШЕНИЯ:\n";
        echo "1. Исправлена передача параметров в frontend\n";
        echo "2. Добавлено логирование в контроллер\n";
        echo "3. Исправлена обработка boolean значений\n";
        echo "4. Добавлена проверка на пустые значения\n";
        echo "\n";
        
        echo "💡 РЕКОМЕНДАЦИИ:\n";
        echo "1. Проверить логи Laravel для отладки\n";
        echo "2. Проверить консоль браузера для frontend\n";
        echo "3. Убедиться, что данные передаются корректно\n";
        echo "4. Проверить типы данных в базе\n";
        echo "\n";
    }
}

// Запуск тестов
$test = new NotificationsApiTest();
$test->runTests();

?> 