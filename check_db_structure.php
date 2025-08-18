<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Настройки подключения к базе данных
$dbConfig = [
    'host' => '5.35.85.110',
    'port' => '5432',
    'dbname' => 'b2bstorage',
    'user' => 'b2buser',
    'password' => 'B2B_Storage_2024!'
];

echo "=== Проверка структуры базы данных ===\n";

try {
    // Подключение к PostgreSQL
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✅ Подключение к базе данных успешно\n\n";
    
    // Проверяем структуру таблицы categories
    echo "=== Таблица CATEGORIES ===\n";
    try {
        $stmt = $pdo->query("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'categories' 
            ORDER BY ordinal_position
        ");
        $columns = $stmt->fetchAll();
        
        if ($columns) {
            foreach ($columns as $column) {
                $nullable = $column['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column['column_default'] ? " DEFAULT {$column['column_default']}" : '';
                echo "  📋 {$column['column_name']} ({$column['data_type']}) {$nullable}{$default}\n";
            }
            
            // Проверяем наличие поля name_china
            $hasNameChina = false;
            foreach ($columns as $column) {
                if ($column['column_name'] === 'name_china') {
                    $hasNameChina = true;
                    break;
                }
            }
            
            if ($hasNameChina) {
                echo "✅ Поле 'name_china' найдено в таблице categories\n";
            } else {
                echo "❌ Поле 'name_china' НЕ найдено в таблице categories\n";
            }
        } else {
            echo "❌ Таблица 'categories' не найдена\n";
        }
        
        // Проверяем количество записей
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $count = $stmt->fetch();
        echo "📊 Количество записей в categories: {$count['total']}\n";
        
    } catch (Exception $e) {
        echo "❌ Ошибка при проверке таблицы categories: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Таблица SUBCATEGORIES ===\n";
    try {
        $stmt = $pdo->query("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'subcategories' 
            ORDER BY ordinal_position
        ");
        $columns = $stmt->fetchAll();
        
        if ($columns) {
            foreach ($columns as $column) {
                $nullable = $column['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column['column_default'] ? " DEFAULT {$column['column_default']}" : '';
                echo "  📋 {$column['column_name']} ({$column['data_type']}) {$nullable}{$default}\n";
            }
            
            // Проверяем наличие поля name_china
            $hasNameChina = false;
            foreach ($columns as $column) {
                if ($column['column_name'] === 'name_china') {
                    $hasNameChina = true;
                    break;
                }
            }
            
            if ($hasNameChina) {
                echo "✅ Поле 'name_china' найдено в таблице subcategories\n";
            } else {
                echo "❌ Поле 'name_china' НЕ найдено в таблице subcategories\n";
            }
        } else {
            echo "❌ Таблица 'subcategories' не найдена\n";
        }
        
        // Проверяем количество записей
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM subcategories");
        $count = $stmt->fetch();
        echo "📊 Количество записей в subcategories: {$count['total']}\n";
        
    } catch (Exception $e) {
        echo "❌ Ошибка при проверке таблицы subcategories: " . $e->getMessage() . "\n";
    }
    
    // Проверяем примеры данных
    echo "\n=== ПРИМЕРЫ ДАННЫХ ===\n";
    try {
        echo "🔍 Первые 3 категории:\n";
        $stmt = $pdo->query("
            SELECT category_id, name, name_ru, name_en, name_uz, 
                   CASE WHEN EXISTS(
                       SELECT 1 FROM information_schema.columns 
                       WHERE table_name = 'categories' AND column_name = 'name_china'
                   ) THEN name_china ELSE 'NO_FIELD' END as name_china
            FROM categories 
            LIMIT 3
        ");
        $categories = $stmt->fetchAll();
        
        foreach ($categories as $cat) {
            echo "  📁 {$cat['category_id']}: {$cat['name']} (china: {$cat['name_china']})\n";
        }
        
        echo "\n🔍 Первые 3 подкатегории:\n";
        $stmt = $pdo->query("
            SELECT subcategory_id, name, name_ru, name_en, name_uz,
                   CASE WHEN EXISTS(
                       SELECT 1 FROM information_schema.columns 
                       WHERE table_name = 'subcategories' AND column_name = 'name_china'
                   ) THEN name_china ELSE 'NO_FIELD' END as name_china
            FROM subcategories 
            LIMIT 3
        ");
        $subcategories = $stmt->fetchAll();
        
        foreach ($subcategories as $subcat) {
            echo "  📂 {$subcat['subcategory_id']}: {$subcat['name']} (china: {$subcat['name_china']})\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Ошибка при получении примеров данных: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Проверка завершена\n";
    
} catch (Exception $e) {
    echo "❌ КРИТИЧЕСКАЯ ОШИБКА: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
    exit(1);
}
?> 