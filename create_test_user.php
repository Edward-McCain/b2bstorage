<?php

// Скрипт для создания тестового пользователя

$baseUrl = 'http://127.0.0.1:8000/api';

echo "Создание тестового пользователя...\n";

$registerPayload = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password',
    'password_confirmation' => 'password',
    'company_name' => 'Test Company',
    'phone' => '+1234567890'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($registerPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Register HTTP Code: $httpCode\n";
echo "Register Response: $response\n\n";

if ($httpCode === 200 || $httpCode === 201) {
    echo "✅ Пользователь создан успешно!\n";
} else {
    $responseData = json_decode($response, true);
    if (isset($responseData['message']) && strpos($responseData['message'], 'already exists') !== false) {
        echo "ℹ️ Пользователь уже существует.\n";
    } else {
        echo "❌ ОШИБКА: Не удалось создать пользователя.\n";
    }
} 