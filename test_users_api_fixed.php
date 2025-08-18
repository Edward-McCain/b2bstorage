<?php

// Тест исправленного API пользователей
echo "🧪 ТЕСТ ИСПРАВЛЕННОГО API ПОЛЬЗОВАТЕЛЕЙ\n";
echo "==========================================\n\n";

// URL API
$apiUrl = 'http://localhost:8000/api/users';

// Получаем токен из параметра
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
    echo "http://localhost/test_users_api_fixed.php?token=YOUR_TOKEN_HERE\n";
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
                echo ($index + 1) . ". ";
                echo "ID: " . ($user['id'] ?? 'N/A') . ", ";
                echo "First Name: " . ($user['first_name'] ?? 'N/A') . ", ";
                echo "User ID: " . ($user['user_id'] ?? 'N/A') . "\n";
            }
            
            echo "\n🔍 Анализ данных для сопоставления с WebRTC:\n";
            echo "---------------------------------------------\n";
            
            // Проверяем, какие пользователи могут быть сопоставлены с WebRTC
            $webRTCUsers = [
                ['name' => 'Edward McCain', 'userId' => '4442c7fb-d338-44a6-9321-bb4bcb5b76ec'],
                ['name' => 'Ку ку', 'userId' => '52cde542-1ff1-4385-8532-f5852c105ce9']
            ];
            
            foreach ($data['users'] as $user) {
                $firstName = $user['first_name'] ?? '';
                $userId = $user['user_id'] ?? '';
                
                echo "Пользователь: $firstName (user_id: $userId)\n";
                
                foreach ($webRTCUsers as $webRTCUser) {
                    if ($webRTCUser['name'] === $firstName) {
                        echo "  ✅ СОВПАДЕНИЕ с WebRTC: " . $webRTCUser['name'] . " (userId: " . $webRTCUser['userId'] . ")\n";
                    }
                }
                echo "\n";
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

echo "\n🔍 Рекомендации:\n";
echo "- Проверьте, что сервер Laravel запущен\n";
echo "- Убедитесь, что база данных подключена\n";
echo "- Проверьте, что таблица users содержит данные\n";
echo "- Убедитесь, что middleware auth:sanctum работает корректно\n";
?>
