<?php

// Тест исправления импорта продуктов с warehouse_id в каждом продукте

$url = 'http://127.0.0.1:8000/api/products/import-with-receipt';

$payload = [
    'products' => [
        [
            'name' => 'Бетон М300 готовый',
            'price' => 4500,
            'quantity' => 0,
            'unit' => 'Кубический метр',
            'article' => 'BTN-300-001',
            'warehouse_id' => 17,
            'category' => 'Строительство и недвижимость'
        ],
        [
            'name' => 'Клей ПВА универсальный',
            'price' => 120,
            'quantity' => 0,
            'unit' => 'Килограмм',
            'article' => 'KLEI-PVA-002',
            'warehouse_id' => 17,
            'category' => 'Строительство и недвижимость'
        ]
    ]
];

echo "Тестирование импорта продуктов с warehouse_id в каждом продукте...\n";
echo "URL: $url\n";
echo "Payload: " . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
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
} else {
    echo "❌ ОШИБКА: Импорт продуктов не работает. Код ошибки: $httpCode\n";
} 