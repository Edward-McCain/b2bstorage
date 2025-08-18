<?php

// Тест остатков из таблицы product_balances

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Тестирование остатков из product_balances...\n";

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
        
        $totalQuantity = 0;
        $productsWithBalances = 0;
        
        foreach ($balancesData['data'] as $product) {
            $quantity = $product['total_quantity'] ?? 0;
            $totalQuantity += $quantity;
            
            if ($quantity > 0) {
                $productsWithBalances++;
                echo "- ID: {$product['id']}, Название: {$product['name']}\n";
                echo "  Остаток: {$quantity}\n";
                echo "  Категория: " . ($product['category'] ?? 'null') . "\n";
                
                // Показываем детали по складам
                if (isset($product['warehouse_balances']) && is_array($product['warehouse_balances'])) {
                    echo "  Остатки по складам:\n";
                    foreach ($product['warehouse_balances'] as $warehouseBalance) {
                        echo "    - Склад ID {$warehouseBalance['warehouse_id']}: {$warehouseBalance['quantity']}\n";
                    }
                }
                echo "\n";
            }
        }
        
        echo "Итого:\n";
        echo "- Товаров с остатками: $productsWithBalances\n";
        echo "- Общий остаток: $totalQuantity\n";
    } else {
        echo "Структура ответа: " . json_encode($balancesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "Ошибка: " . $response . "\n";
}

// Тестируем фильтрацию по складу
echo "\nТестирование фильтрации по складу...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/balances');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['warehouse_id' => 14]));
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

if ($httpCode === 200) {
    $balancesData = json_decode($response, true);
    if (isset($balancesData['data']) && is_array($balancesData['data'])) {
        echo "Товары на складе ID 14: " . count($balancesData['data']) . "\n";
    }
} 