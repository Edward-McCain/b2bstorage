<?php

// Тест новой логики остатков (товары из products_sklad)

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Тестирование новой логики остатков...\n";

// Получаем остатки через API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/balances');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Balances API HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    $balancesData = json_decode($response, true);
    if (isset($balancesData['data']) && is_array($balancesData['data'])) {
        echo "Найдено товаров: " . count($balancesData['data']) . "\n";
        foreach ($balancesData['data'] as $product) {
            echo "- ID: {$product['id']}, Название: {$product['name']}, Категория: " . ($product['category'] ?? 'null') . ", Общий остаток: " . ($product['total_quantity'] ?? 0) . "\n";
        }
    } else {
        echo "Структура ответа: " . json_encode($balancesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "Ошибка: " . $response . "\n";
} 