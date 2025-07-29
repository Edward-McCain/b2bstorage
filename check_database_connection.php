<?php

require_once 'b2b-project/backend/vendor/autoload.php';

// Загружаем переменные окружения
$dotenv = Dotenv\Dotenv::createImmutable('b2b-project/backend');
$dotenv->load();

// Инициализируем Laravel
$app = require_once 'b2b-project/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

echo "🔍 Проверка подключения к базе данных\n";
echo "=====================================\n\n";

// Проверяем переменную LOCAL_DB
$localDb = env('LOCAL_DB', true);
echo "LOCAL_DB: " . ($localDb ? "true (локальная)" : "false (серверная)") . "\n\n";

// Показываем текущие настройки
echo "Текущие настройки подключения:\n";
echo "Host: " . Config::get('database.connections.pgsql.host') . "\n";
echo "Port: " . Config::get('database.connections.pgsql.port') . "\n";
echo "Database: " . Config::get('database.connections.pgsql.database') . "\n";
echo "Username: " . Config::get('database.connections.pgsql.username') . "\n\n";

// Пытаемся подключиться
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Подключение успешно!\n";
    echo "Версия PostgreSQL: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    
    // Проверяем количество таблиц
    $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public'");
    echo "Количество таблиц: " . $tables[0]->count . "\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "\n";
} 