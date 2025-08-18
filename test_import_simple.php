<?php

// Простой тест импорта продуктов без категорий

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Простой тест импорта продуктов без категорий...\n";

$importPayload = [
    'products' => [
        [
            'name' => 'Бетон М300 готовый',
            'price' => 4500,
            'quantity' => 0,
            'unit' => 'Кубический метр',
            'article' => 'BTN-300-001',
            'warehouse_id' => 1 // Используем существующий warehouse_id
        ],
        [
            'name' => 'Клей ПВА универсальный',
            'price' => 120,
            'quantity' => 0,
            'unit' => 'Килограмм',
            'article' => 'KLEI-PVA-002',
            'warehouse_id' => 1
        ],
        [
            'name' => 'Краска акриловая белая',
            'price' => 350,
            'quantity' => 0,
            'unit' => 'Литр',
            'article' => 'KRASKA-ACR-003',
            'warehouse_id' => 1
        ]
    ]
];

echo "Payload: " . json_encode($importPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/products/import-with-receipt');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($importPayload));
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

echo "HTTP Status Code: $httpCode\n";
echo "Response: " . $response . "\n\n";

if ($httpCode === 200) {
    echo "✅ УСПЕХ: Импорт продуктов работает корректно!\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['created_products_count'])) {
        echo "Создано продуктов: " . $responseData['created_products_count'] . "\n";
    }
} else {
    echo "❌ ОШИБКА: Импорт продуктов не работает. Код ошибки: $httpCode\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['error'])) {
        echo "Ошибка: " . $responseData['error'] . "\n";
    }
} 