<?php

// Простой скрипт для проверки сохраненных продуктов

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Проверка сохраненных продуктов...\n";

// Получаем список продуктов
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/products');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Products HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $productsData = json_decode($response, true);
    if (isset($productsData['data']['data']) && is_array($productsData['data']['data'])) {
        echo "Найденные продукты:\n";
        foreach ($productsData['data']['data'] as $product) {
            echo "- ID: {$product['id']}, Название: {$product['name']}, Категория: " . ($product['category'] ?? 'null') . ", Подкатегория: " . ($product['subcategory'] ?? 'null') . "\n";
        }
    }
} 