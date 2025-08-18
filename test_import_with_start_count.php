<?php

// Тест импорта продуктов с правильным start_count

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Тестирование импорта продуктов с start_count...\n";

$importPayload = [
    'products' => [
        [
            'name' => 'Бетон М400 готовый',
            'price' => 5000,
            'start_count' => 10, // Используем start_count вместо quantity
            'unit' => 'Кубический метр',
            'article' => 'BTN-400-001',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost'
        ],
        [
            'name' => 'Клей ПВА строительный',
            'price' => 150,
            'start_count' => 25, // Используем start_count вместо quantity
            'unit' => 'Килограмм',
            'article' => 'KLEI-PVA-003',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost'
        ],
        [
            'name' => 'Краска масляная черная',
            'price' => 400,
            'start_count' => 15, // Используем start_count вместо quantity
            'unit' => 'Литр',
            'article' => 'KRASKA-MAS-004',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost'
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
    echo "✅ УСПЕХ: Импорт продуктов с start_count работает корректно!\n";
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