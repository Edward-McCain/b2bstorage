<?php

// Тест импорта товаров
$url = 'http://127.0.0.1:8000/api/products/import-with-receipt';

$data = [
    'warehouse_id' => 1,
    'products' => [
        [
            'name' => 'Тестовый товар 1',
            'description' => 'Описание тестового товара 1',
            'category' => 'Тестовая категория',
            'subcategory' => 'Тестовая подкатегория',
            'start_count' => 10,
            'unit' => 'шт',
            'article' => 'TEST001',
            'code' => 'CODE001'
        ],
        [
            'name' => 'Тестовый товар 2',
            'description' => 'Описание тестового товара 2',
            'category' => 'Тестовая категория',
            'subcategory' => 'Тестовая подкатегория',
            'start_count' => 5,
            'unit' => 'шт',
            'article' => 'TEST002',
            'code' => 'CODE002'
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer YOUR_TOKEN_HERE' // Замените на реальный токен
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
if ($error) {
    echo "Curl Error: " . $error . "\n";
} 