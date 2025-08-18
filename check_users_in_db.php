<?php

// Проверка пользователей в базе данных
echo "🔍 ПРОВЕРКА ПОЛЬЗОВАТЕЛЕЙ В БАЗЕ ДАННЫХ\n";
echo "========================================\n\n";

// Подключение к базе данных
try {
    $host = 'localhost';
    $dbname = 'b2bsklad';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Подключение к базе данных успешно\n\n";
    
    // Проверяем структуру таблицы users
    echo "📋 Структура таблицы users:\n";
    echo "----------------------------\n";
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }
    
    echo "\n";
    
    // Проверяем количество пользователей
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "👥 Общее количество пользователей: $totalUsers\n\n";
    
    // Проверяем активных пользователей
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM users WHERE deleted = 0 AND is_active = 1");
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    echo "✅ Активных пользователей: $activeUsers\n\n";
    
    // Получаем список пользователей для API
    $stmt = $pdo->query("
        SELECT id, first_name, user_name as user_id 
        FROM users 
        WHERE deleted = 0 AND is_active = 1 
        ORDER BY first_name 
        LIMIT 10
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 Первые 10 пользователей (как в API):\n";
    echo "----------------------------------------\n";
    
    if (count($users) > 0) {
        foreach ($users as $index => $user) {
            echo ($index + 1) . ". ID: {$user['id']}, Имя: {$user['first_name']}, User ID: {$user['user_id']}\n";
        }
    } else {
        echo "❌ Нет активных пользователей в базе данных\n";
    }
    
    echo "\n";
    
    // Проверяем наличие полей, используемых в API
    $requiredFields = ['id', 'first_name', 'user_name', 'deleted', 'is_active'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$field'");
        if ($stmt->rowCount() === 0) {
            $missingFields[] = $field;
        }
    }
    
    if (empty($missingFields)) {
        echo "✅ Все необходимые поля присутствуют в таблице\n";
    } else {
        echo "❌ Отсутствуют поля: " . implode(', ', $missingFields) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
    echo "\n💡 Проверьте:\n";
    echo "- Настройки подключения к базе данных\n";
    echo "- Существование базы данных 'b2bsklad'\n";
    echo "- Права доступа пользователя\n";
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}

echo "\n🔍 Рекомендации:\n";
echo "- Убедитесь, что в базе данных есть активные пользователи\n";
echo "- Проверьте, что поля deleted и is_active имеют правильные значения\n";
echo "- Убедитесь, что сервер Laravel запущен и может подключиться к БД\n";
?>
