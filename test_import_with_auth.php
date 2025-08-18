<?php

// Тест исправления импорта продуктов с авторизацией

$baseUrl = 'http://127.0.0.1:8000/api';

// 1. Сначала авторизуемся
echo "1. Авторизация...\n";
$loginPayload = [
    'email' => 'test@example.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $httpCode\n";
echo "Login Response: $response\n\n";

if ($httpCode !== 200) {
    echo "❌ ОШИБКА: Не удалось авторизоваться. Создайте тестового пользователя.\n";
    exit(1);
}

$loginData = json_decode($response, true);
$token = $loginData['token'] ?? null;

if (!$token) {
    echo "❌ ОШИБКА: Токен не получен\n";
    exit(1);
}

echo "✅ Авторизация успешна. Токен получен.\n\n";

// 2. Тестируем импорт продуктов
echo "2. Тестирование импорта продуктов...\n";

$importPayload = [
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

echo "Import HTTP Code: $httpCode\n";
echo "Import Response: $response\n\n";

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