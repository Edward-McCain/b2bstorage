<?php

// Скрипт для проверки данных в таблицах остатков

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Проверка данных в таблицах остатков...\n\n";

// 1. Проверяем остатки через API
echo "1. Получение остатков через API:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/balances');
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

echo "Balances API HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    $balancesData = json_decode($response, true);
    if (isset($balancesData['data']) && is_array($balancesData['data'])) {
        echo "Найдено остатков: " . count($balancesData['data']) . "\n";
        foreach ($balancesData['data'] as $balance) {
            echo "- Товар: {$balance['product']['name']}, Склад: {$balance['warehouse']['name']}, Количество: {$balance['quantity']}\n";
        }
    }
}
echo "\n";

// 2. Проверяем товары через API
echo "2. Получение товаров через API:\n";
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

echo "Products API HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    $productsData = json_decode($response, true);
    if (isset($productsData['data']['data']) && is_array($productsData['data']['data'])) {
        echo "Найдено товаров: " . count($productsData['data']['data']) . "\n";
        foreach ($productsData['data']['data'] as $product) {
            echo "- ID: {$product['id']}, Название: {$product['name']}, Категория: " . ($product['category'] ?? 'null') . "\n";
        }
    }
}
echo "\n";

// 3. Проверяем склады через API
echo "3. Получение складов через API:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/warehouses');
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

echo "Warehouses API HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    $warehousesData = json_decode($response, true);
    if (isset($warehousesData['data']) && is_array($warehousesData['data'])) {
        echo "Найдено складов: " . count($warehousesData['data']) . "\n";
        foreach ($warehousesData['data'] as $warehouse) {
            echo "- ID: {$warehouse['id']}, Название: {$warehouse['name']}, Пользователь: {$warehouse['user_id']}\n";
        }
    }
} 