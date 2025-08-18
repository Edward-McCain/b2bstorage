<?php

// Тест новой логики цен

$token = '106|DrG5Pnh0VJsOTkWt6FnNmWM4bIxNAQfDl7CZ4p08c5aedf5d';

echo "=== Тест новой логики цен ===\n\n";

// Получаем список товаров
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/balances');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
$data = json_decode($response, true);

if (isset($data['data'])) {
    echo "Товары и их цены:\n";
    foreach ($data['data'] as $product) {
        echo "- {$product['name']}:\n";
        echo "  - Цена в API: {$product['price']}\n";
        echo "  - Количество: {$product['total_quantity']}\n";
        echo "  - Стоимость: " . ($product['price'] * $product['total_quantity']) . "\n";
        echo "\n";
    }
} else {
    echo "Ошибка: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== Тест завершен ===\n"; 