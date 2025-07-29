<?php
/**
 * Тест интеграции пользовательских категорий
 * Запуск: php test_categories_integration.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3'; // Замените на ваш токен

function makeRequest($endpoint, $method = 'GET', $data = null) {
    global $baseUrl, $token;
    
    $url = $baseUrl . $endpoint;
    $headers = [
        'Authorization: Bearer ' . $token,
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

function printResult($testName, $result) {
    echo "\n=== {$testName} ===\n";
    echo "HTTP Code: {$result['code']}\n";
    echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

echo "🧪 ТЕСТИРОВАНИЕ ИНТЕГРАЦИИ КАТЕГОРИЙ\n";
echo "=====================================\n";

// Тест 1: Получение настроек пользователя
echo "\n1️⃣ Тест получения настроек пользователя";
$result = makeRequest('/user/settings');
printResult('Настройки пользователя', $result);

// Тест 2: Получение системных категорий
echo "\n2️⃣ Тест получения системных категорий";
$result = makeRequest('/categories');
printResult('Системные категории', $result);

// Тест 3: Получение пользовательских категорий
echo "\n3️⃣ Тест получения пользовательских категорий";
$result = makeRequest('/user/categories');
printResult('Пользовательские категории', $result);

// Тест 4: Проверка статистики категорий
echo "\n4️⃣ Тест статистики категорий";
$result = makeRequest('/user/categories/stats');
printResult('Статистика категорий', $result);

// Тест 5: Проверка проблемных категорий
echo "\n5️⃣ Тест проверки проблемных категорий";
$result = makeRequest('/user/categories/check');
printResult('Проверка проблемных категорий', $result);

// Тест 6: Получение товаров пользователя
echo "\n6️⃣ Тест получения товаров пользователя";
$result = makeRequest('/products');
printResult('Товары пользователя', $result);

echo "\n✅ ТЕСТИРОВАНИЕ ЗАВЕРШЕНО\n";
echo "Проверьте результаты выше для выявления проблем.\n"; 