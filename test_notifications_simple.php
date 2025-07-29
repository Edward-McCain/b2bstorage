<?php

// Простой тест фильтров уведомлений

class NotificationsSimpleTest {
    private $baseUrl = 'http://localhost:8000/api';
    
    public function runTests() {
        echo "\n🔍 ПРОСТОЙ ТЕСТ ФИЛЬТРОВ УВЕДОМЛЕНИЙ\n";
        echo "========================================\n\n";
        
        // Тест 1: Без фильтров
        $this->testNoFilters();
        
        // Тест 2: Фильтр по типу
        $this->testTypeFilter();
        
        // Тест 3: Фильтр по статусу
        $this->testReadStatusFilter();
        
        echo "\n";
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
        
        $types = ['info', 'warning', 'recommendation'];
        
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
    
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error
        ];
    }
    
    private function printResponse($response, $testName) {
        echo "HTTP Code: {$response['http_code']}\n";
        
        if ($response['error']) {
            echo "CURL Error: {$response['error']}\n";
            return;
        }
        
        if ($response['http_code'] === 200) {
            $data = json_decode($response['response'], true);
            
            if ($data && isset($data['success']) && $data['success']) {
                $count = count($data['data'] ?? []);
                $unreadCount = $data['unread_count'] ?? 0;
                
                echo "✅ Успешно\n";
                echo "Количество уведомлений: {$count}\n";
                echo "Непрочитанных: {$unreadCount}\n";
                
                if ($count > 0) {
                    echo "Примеры уведомлений:\n";
                    foreach (array_slice($data['data'], 0, 2) as $notification) {
                        echo "  - ID: {$notification['id']}, Тип: {$notification['type']}, Прочитано: " . ($notification['is_read'] ? 'Да' : 'Нет') . "\n";
                    }
                }
            } else {
                echo "❌ API вернул ошибку\n";
                echo "Ответ: " . $response['response'] . "\n";
            }
        } else {
            echo "❌ HTTP ошибка: {$response['http_code']}\n";
            echo "Ответ: " . $response['response'] . "\n";
        }
    }
}

// Запуск тестов
$test = new NotificationsSimpleTest();
$test->runTests();

?> 