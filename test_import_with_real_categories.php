<?php

// Тест импорта продуктов с реальными category_id из базы данных

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Тестирование импорта продуктов с реальными category_id...\n";

$importPayload = [
    'products' => [
        [
            'name' => 'Бетон М300 готовый',
            'price' => 4500,
            'quantity' => 0,
            'unit' => 'Кубический метр',
            'article' => 'BTN-300-001',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost', // Реальный category_id
            'subcategory' => null // Пока без подкатегории
        ],
        [
            'name' => 'Клей ПВА универсальный',
            'price' => 120,
            'quantity' => 0,
            'unit' => 'Килограмм',
            'article' => 'KLEI-PVA-002',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost', // Реальный category_id
            'subcategory' => null
        ],
        [
            'name' => 'Краска акриловая белая',
            'price' => 350,
            'quantity' => 0,
            'unit' => 'Литр',
            'article' => 'KRASKA-ACR-003',
            'warehouse_id' => 1,
            'category' => 'stroitelstvo-i-nedvijimost', // Реальный category_id
            'subcategory' => null
        ],
        [
            'name' => 'Удобрение азотное NPK',
            'price' => 85,
            'quantity' => 0,
            'unit' => 'Килограмм',
            'article' => 'UDOB-NPK-004',
            'warehouse_id' => 1,
            'category' => 'selskoe-khozyaystvo', // Реальный category_id
            'subcategory' => null
        ],
        [
            'name' => 'Провод медный ВВГнг 3x2.5',
            'price' => 45,
            'quantity' => 0,
            'unit' => 'Метр',
            'article' => 'PROV-VVG-005',
            'warehouse_id' => 1,
            'category' => 'elektrotekhnicheskoe-oborudovanie-i-materiali', // Реальный category_id
            'subcategory' => null
        ],
        [
            'name' => 'Косметика увлажняющий крем',
            'price' => 280,
            'quantity' => 0,
            'unit' => 'Штука',
            'article' => 'KOSM-CREM-006',
            'warehouse_id' => 1,
            'category' => 'krasota-i-lichnaya-gigiena', // Реальный category_id
            'subcategory' => null
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
    echo "✅ УСПЕХ: Импорт продуктов с категориями работает корректно!\n";
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