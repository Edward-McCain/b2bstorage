<?php
/**
 * Тест производительности интеграции пользовательских категорий
 * Запуск: php test_categories_performance.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class PerformanceTest {
    private $baseUrl;
    private $token;
    private $results = [];
    
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($data && in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $startTime = microtime(true);
        $response = curl_exec($ch);
        $endTime = microtime(true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true),
            'time' => ($endTime - $startTime) * 1000, // в миллисекундах
            'success' => $httpCode >= 200 && $httpCode < 300
        ];
    }
    
    private function runPerformanceTest($testName, $endpoint, $method = 'GET', $data = null, $iterations = 10) {
        echo "🧪 Тестирование производительности: {$testName}\n";
        echo "Количество итераций: {$iterations}\n";
        
        $times = [];
        $successCount = 0;
        
        for ($i = 0; $i < $iterations; $i++) {
            $result = $this->makeRequest($endpoint, $method, $data);
            $times[] = $result['time'];
            
            if ($result['success']) {
                $successCount++;
            }
            
            echo "  Итерация " . ($i + 1) . ": " . round($result['time'], 2) . "ms" . 
                 ($result['success'] ? " ✅" : " ❌") . "\n";
            
            // Небольшая пауза между запросами
            usleep(100000); // 100ms
        }
        
        $avgTime = array_sum($times) / count($times);
        $minTime = min($times);
        $maxTime = max($times);
        $successRate = ($successCount / $iterations) * 100;
        
        $this->results[] = [
            'name' => $testName,
            'endpoint' => $endpoint,
            'iterations' => $iterations,
            'avg_time' => $avgTime,
            'min_time' => $minTime,
            'max_time' => $maxTime,
            'success_rate' => $successRate,
            'times' => $times
        ];
        
        echo "📊 Результаты:\n";
        echo "  Среднее время: " . round($avgTime, 2) . "ms\n";
        echo "  Минимальное время: " . round($minTime, 2) . "ms\n";
        echo "  Максимальное время: " . round($maxTime, 2) . "ms\n";
        echo "  Процент успеха: " . round($successRate, 1) . "%\n\n";
    }
    
    public function runAllTests() {
        echo "🚀 ТЕСТИРОВАНИЕ ПРОИЗВОДИТЕЛЬНОСТИ ИНТЕГРАЦИИ КАТЕГОРИЙ\n";
        echo "========================================================\n\n";
        
        // 1. Тест получения системных категорий
        $this->runPerformanceTest(
            'Получение системных категорий',
            '/categories',
            'GET',
            null,
            5
        );
        
        // 2. Тест получения пользовательских категорий
        $this->runPerformanceTest(
            'Получение пользовательских категорий',
            '/user/categories',
            'GET',
            null,
            5
        );
        
        // 3. Тест получения подкатегорий
        $this->runPerformanceTest(
            'Получение подкатегорий',
            '/categories/1/subcategories',
            'GET',
            null,
            5
        );
        
        // 4. Тест получения товаров
        $this->runPerformanceTest(
            'Получение товаров',
            '/products',
            'GET',
            null,
            5
        );
        
        // 5. Тест получения настроек пользователя
        $this->runPerformanceTest(
            'Получение настроек пользователя',
            '/user/settings',
            'GET',
            null,
            5
        );
        
        // 6. Тест статистики категорий
        $this->runPerformanceTest(
            'Получение статистики категорий',
            '/user/categories/stats',
            'GET',
            null,
            3
        );
        
        // 7. Тест проверки проблемных категорий
        $this->runPerformanceTest(
            'Проверка проблемных категорий',
            '/user/categories/check',
            'GET',
            null,
            3
        );
        
        // 8. Тест создания товара
        $testProduct = [
            'name' => 'Тестовый товар производительности',
            'price' => 100,
            'start_count' => 1
        ];
        $this->runPerformanceTest(
            'Создание товара',
            '/products',
            'POST',
            $testProduct,
            3
        );
        
        // Вывод итоговых результатов
        $this->printSummary();
    }
    
    private function printSummary() {
        echo "📊 ИТОГОВАЯ СВОДКА ПРОИЗВОДИТЕЛЬНОСТИ\n";
        echo "========================================\n\n";
        
        $totalTests = count($this->results);
        $successfulTests = count(array_filter($this->results, function($r) {
            return $r['success_rate'] >= 80;
        }));
        
        echo "Всего тестов: {$totalTests}\n";
        echo "Успешных тестов (≥80%): {$successfulTests}\n";
        echo "Процент успешных тестов: " . round(($successfulTests / $totalTests) * 100, 1) . "%\n\n";
        
        echo "📈 ДЕТАЛЬНЫЕ РЕЗУЛЬТАТЫ:\n";
        echo "------------------------\n";
        
        foreach ($this->results as $result) {
            $status = $result['success_rate'] >= 80 ? "✅" : "❌";
            echo "{$status} {$result['name']}\n";
            echo "  Среднее время: " . round($result['avg_time'], 2) . "ms\n";
            echo "  Диапазон: " . round($result['min_time'], 2) . "ms - " . round($result['max_time'], 2) . "ms\n";
            echo "  Процент успеха: " . round($result['success_rate'], 1) . "%\n";
            echo "  Endpoint: {$result['endpoint']}\n\n";
        }
        
        // Анализ производительности
        $avgTimes = array_column($this->results, 'avg_time');
        $fastestTest = $this->results[array_search(min($avgTimes), $avgTimes)];
        $slowestTest = $this->results[array_search(max($avgTimes), $avgTimes)];
        
        echo "🏆 АНАЛИЗ ПРОИЗВОДИТЕЛЬНОСТИ:\n";
        echo "-----------------------------\n";
        echo "Самый быстрый тест: {$fastestTest['name']} (" . round($fastestTest['avg_time'], 2) . "ms)\n";
        echo "Самый медленный тест: {$slowestTest['name']} (" . round($slowestTest['avg_time'], 2) . "ms)\n";
        echo "Разница: " . round($slowestTest['avg_time'] - $fastestTest['avg_time'], 2) . "ms\n\n";
        
        // Рекомендации
        echo "🎯 РЕКОМЕНДАЦИИ:\n";
        echo "----------------\n";
        
        $slowTests = array_filter($this->results, function($r) {
            return $r['avg_time'] > 1000; // больше 1 секунды
        });
        
        if (!empty($slowTests)) {
            echo "⚠️  Медленные тесты (>1s):\n";
            foreach ($slowTests as $test) {
                echo "  - {$test['name']}: " . round($test['avg_time'], 2) . "ms\n";
            }
            echo "\n";
        }
        
        $unreliableTests = array_filter($this->results, function($r) {
            return $r['success_rate'] < 80;
        });
        
        if (!empty($unreliableTests)) {
            echo "⚠️  Ненадежные тесты (<80% успеха):\n";
            foreach ($unreliableTests as $test) {
                echo "  - {$test['name']}: " . round($test['success_rate'], 1) . "%\n";
            }
            echo "\n";
        }
        
        if (empty($slowTests) && empty($unreliableTests)) {
            echo "✅ Все тесты показывают хорошую производительность и надежность!\n";
        }
        
        echo "\n📋 БЕНЧМАРКИ:\n";
        echo "-------------\n";
        echo "Отлично: < 200ms\n";
        echo "Хорошо: 200-500ms\n";
        echo "Удовлетворительно: 500-1000ms\n";
        echo "Медленно: > 1000ms\n";
    }
}

// Запуск тестов производительности
$performanceTest = new PerformanceTest($baseUrl, $token);
$performanceTest->runAllTests(); 