<?php

// Тест для проверки исправлений в API сводки остатков
require_once 'b2b-project/backend/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// URL API
$baseUrl = 'http://localhost:8000/api';

// Тестовые данные для авторизации (замените на реальные)
$email = 'test@example.com';
$password = 'password';

echo "=== Тест API сводки остатков ===\n\n";

// 1. Авторизация
echo "1. Авторизация...\n";
$loginResponse = Http::post($baseUrl . '/auth/login', [
    'email' => $email,
    'password' => $password
]);

if ($loginResponse->successful()) {
    $token = $loginResponse->json('token');
    echo "✓ Авторизация успешна\n\n";
} else {
    echo "✗ Ошибка авторизации: " . $loginResponse->body() . "\n";
    exit(1);
}

// 2. Тест получения сводки
echo "2. Тест получения сводки остатков...\n";
$summaryResponse = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json'
])->post($baseUrl . '/balances/summary', []);

if ($summaryResponse->successful()) {
    $summary = $summaryResponse->json();
    echo "✓ Сводка получена успешно\n";
    echo "Данные сводки:\n";
    echo "- Всего товаров: " . ($summary['summary']['total_products'] ?? 'N/A') . "\n";
    echo "- Всего складов: " . ($summary['summary']['total_warehouses'] ?? 'N/A') . "\n";
    echo "- Общее количество: " . ($summary['summary']['total_quantity'] ?? 'N/A') . "\n";
    echo "- Общая стоимость: " . ($summary['summary']['total_value'] ?? 'N/A') . "\n";
    echo "- Товары с низким остатком: " . ($summary['summary']['low_stock_items'] ?? 'N/A') . "\n";
    echo "- Товары без остатка: " . ($summary['summary']['out_of_stock_items'] ?? 'N/A') . "\n";
    echo "- Валюта: " . ($summary['currency'] ?? 'N/A') . "\n\n";
} else {
    echo "✗ Ошибка получения сводки: " . $summaryResponse->body() . "\n";
}

// 3. Тест получения остатков
echo "3. Тест получения остатков...\n";
$balancesResponse = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json'
])->post($baseUrl . '/balances', []);

if ($balancesResponse->successful()) {
    $balances = $balancesResponse->json();
    echo "✓ Остатки получены успешно\n";
    echo "Количество остатков: " . ($balances['data'] ? count($balances['data']) : 0) . "\n";
    echo "Всего записей: " . ($balances['total'] ?? 0) . "\n\n";
} else {
    echo "✗ Ошибка получения остатков: " . $balancesResponse->body() . "\n";
}

// 4. Тест получения складов
echo "4. Тест получения складов...\n";
$warehousesResponse = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json'
])->get($baseUrl . '/warehouses');

if ($warehousesResponse->successful()) {
    $warehouses = $warehousesResponse->json();
    echo "✓ Склады получены успешно\n";
    echo "Количество складов: " . ($warehouses['data'] ? count($warehouses['data']) : 0) . "\n\n";
} else {
    echo "✗ Ошибка получения складов: " . $warehousesResponse->body() . "\n";
}

echo "=== Тест завершен ===\n"; 