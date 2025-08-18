<?php

// Скрипт для проверки существующих категорий в базе данных

$baseUrl = 'http://127.0.0.1:8000/api';
$token = '104|wsMyBT4ZTLUkbZJHMspCRUPyXgRdHtsOHQOgRtdY01939b73';

echo "Проверка существующих категорий...\n";

// Получаем системные категории
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/categories');
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

echo "Categories HTTP Code: $httpCode\n";
echo "Categories Response: " . $response . "\n\n";

// Получаем пользовательские категории
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/user/categories');
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

echo "User Categories HTTP Code: $httpCode\n";
echo "User Categories Response: " . $response . "\n\n"; 