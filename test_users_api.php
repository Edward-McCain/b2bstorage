<?php

// Тест API для получения списка пользователей
echo "🧪 ТЕСТ API СПИСКА ПОЛЬЗОВАТЕЛЕЙ\n";
echo "================================\n\n";

// URL API
$apiUrl = 'http://localhost:8000/api/users';

// Получаем токен из localStorage (если есть)
$token = isset($_GET['token']) ? $_GET['token'] : '';

echo "📡 URL: $apiUrl\n";
echo "🔑 Token: " . ($token ? 'Установлен' : 'Не установлен') . "\n\n";

// Выполняем запрос
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "🔄 Выполняем запрос...\n";
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
    echo "🔒 Ошибка авторизации (401)\n";
    echo "💡 Необходимо передать валидный токен в параметре ?token=YOUR_TOKEN\n";
    echo "\n📝 Пример использования:\n";
    echo "http://localhost/test_users_api.php?token=YOUR_TOKEN_HERE\n";
} elseif ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "✅ Успешный ответ!\n";
        echo "📋 Структура ответа:\n";
        echo "- success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "- message: " . ($data['message'] ?? 'N/A') . "\n";
        
        if (isset($data['users']) && is_array($data['users'])) {
            echo "- users count: " . count($data['users']) . "\n";
            echo "\n👥 Список пользователей:\n";
            echo "------------------------\n";
            
            foreach ($data['users'] as $index => $user) {
                echo ($index + 1) . ". ID: {$user['id']}, Имя: {$user['first_name']}, User ID: {$user['user_id']}\n";
            }
        } else {
            echo "⚠️ Поле 'users' отсутствует или не является массивом\n";
        }
    } else {
        echo "❌ Ошибка парсинга JSON\n";
        echo "📄 Сырой ответ: $response\n";
    }
} else {
    echo "❌ Неожиданный HTTP код: $httpCode\n";
    echo "📄 Ответ: $response\n";
}

echo "\n🔍 Дополнительная информация:\n";
echo "- Проверьте, что сервер Laravel запущен\n";
echo "- Убедитесь, что база данных подключена\n";
echo "- Проверьте, что таблица users существует и содержит данные\n";
echo "- Убедитесь, что middleware auth:sanctum работает корректно\n";
?>
