<?php

// Прямой тест API

class ApiDirectTest {
    private $baseUrl = 'http://localhost:8000/api';
    
    public function testApi() {
        echo "\n🔍 ПРЯМОЙ ТЕСТ API\n";
        echo "==================\n\n";
        
        // Тест 1: Запрос с периодом 'year'
        $this->testPeriod('year');
        
        // Тест 2: Запрос с периодом 'month'
        $this->testPeriod('month');
        
        // Тест 3: Запрос с периодом 'week'
        $this->testPeriod('week');
    }
    
    private function testPeriod($period) {
        echo "📅 ТЕСТ ПЕРИОДА: {$period}\n";
        echo "------------------------\n";
        
        $url = "{$this->baseUrl}/statistics/operations?period={$period}";
        echo "URL: {$url}\n";
        
        // Симуляция запроса с cURL
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
        
        echo "HTTP Code: {$httpCode}\n";
        echo "Response:\n";
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data) {
                echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
                if (isset($data['data'])) {
                    echo "Receipts count: " . count($data['data']['receipts']) . "\n";
                    echo "Write-offs count: " . count($data['data']['writeOffs']) . "\n";
                    echo "Transfers count: " . count($data['data']['transfers']) . "\n";
                    
                    if (!empty($data['data']['receipts'])) {
                        echo "Receipts data:\n";
                        foreach ($data['data']['receipts'] as $item) {
                            echo "  - Date: {$item['date']}, Count: {$item['count']}\n";
                        }
                    }
                    
                    if (!empty($data['data']['writeOffs'])) {
                        echo "Write-offs data:\n";
                        foreach ($data['data']['writeOffs'] as $item) {
                            echo "  - Date: {$item['date']}, Count: {$item['count']}\n";
                        }
                    }
                    
                    if (!empty($data['data']['transfers'])) {
                        echo "Transfers data:\n";
                        foreach ($data['data']['transfers'] as $item) {
                            echo "  - Date: {$item['date']}, Count: {$item['count']}\n";
                        }
                    }
                }
            } else {
                echo "Failed to decode JSON response\n";
                echo "Raw response: {$response}\n";
            }
        } else {
            echo "No response received\n";
        }
        
        echo "\n";
    }
}

// Запуск теста
$test = new ApiDirectTest();
$test->testApi();

?> 