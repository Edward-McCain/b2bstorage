<?php

// Тест WebRTC API
echo "🧪 ТЕСТ WEBRTC API\n";
echo "==================\n\n";

// URL WebRTC API
$apiUrl = 'https://45.92.173.142:8443/api/users/online';

echo "📡 URL: $apiUrl\n\n";

// Выполняем запрос к WebRTC API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем проверку SSL для тестирования
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

echo "🔄 Выполняем запрос к WebRTC API...\n";
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
    echo "❌ Ошибка: Не удалось подключиться к WebRTC серверу\n";
    echo "💡 Убедитесь, что WebRTC сервер доступен\n";
    exit;
}

echo "📄 Ответ сервера:\n";
echo "------------------\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "✅ Успешный ответ!\n";
        echo "📋 Структура ответа:\n";
        echo "- status: " . ($data['status'] ?? 'N/A') . "\n";
        echo "- count: " . ($data['count'] ?? 'N/A') . "\n";
        
        if (isset($data['users']) && is_array($data['users'])) {
            echo "- users count: " . count($data['users']) . "\n";
            echo "\n👥 Список пользователей онлайн:\n";
            echo "--------------------------------\n";
            
            foreach ($data['users'] as $index => $user) {
                echo ($index + 1) . ". ";
                echo "User ID: " . ($user['userId'] ?? 'N/A') . ", ";
                echo "Name: " . ($user['name'] ?? 'N/A') . ", ";
                echo "Token: " . (isset($user['token']) ? substr($user['token'], 0, 20) . '...' : 'N/A') . ", ";
                echo "Last Seen: " . ($user['lastSeen'] ?? 'N/A') . "\n";
                
                // Извлекаем user_id из токена
                if (isset($user['token'])) {
                    $tokenParts = explode('|', $user['token']);
                    $userId = $tokenParts[0] ?? 'N/A';
                    echo "   Extracted User ID: $userId\n";
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

echo "\n🔍 Дополнительная информация:\n";
echo "- Проверьте, что WebRTC сервер запущен на https://45.92.173.142:8443\n";
echo "- Убедитесь, что сервер доступен из вашей сети\n";
echo "- Проверьте SSL сертификат сервера\n";
?>
