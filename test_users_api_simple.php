<?php

// Простой тест API для получения списка пользователей
echo "🧪 ПРОСТОЙ ТЕСТ API СПИСКА ПОЛЬЗОВАТЕЛЕЙ\n";
echo "==========================================\n\n";

// URL API
$apiUrl = 'http://localhost:8000/api/users';

echo "📡 URL: $apiUrl\n\n";

// Выполняем запрос без токена
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "🔄 Выполняем запрос без токена...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "📊 HTTP код: $httpCode\n";

if ($error) {
    echo "❌ Ошибка cURL: $error\n";
    exit;
}

if ($httpCode === 0) {
    echo "❌ Ошибка: Не удалось подключиться к серверу\n";
    echo "💡 Убедитесь, что сервер запущен на http://localhost:8000\n";
    exit;
}

echo "📄 Ответ сервера:\n";
echo "------------------\n";

if ($httpCode === 401) {
    echo "🔒 Ожидаемая ошибка авторизации (401)\n";
    echo "✅ API работает корректно - требует авторизацию\n";
} elseif ($httpCode === 200) {
    echo "⚠️ Неожиданный успешный ответ без токена\n";
    $data = json_decode($response, true);
    if ($data) {
        echo "📋 Структура ответа:\n";
        print_r($data);
    }
} else {
    echo "❌ Неожиданный HTTP код: $httpCode\n";
    echo "📄 Ответ: $response\n";
}

echo "\n🔍 Следующие шаги:\n";
echo "1. Убедитесь, что сервер Laravel запущен\n";
echo "2. Получите валидный токен через /api/login\n";
echo "3. Используйте токен в заголовке Authorization: Bearer <token>\n";
?>
