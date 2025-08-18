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

echo "=== Скрипт обновления китайских переводов категорий ===\n";

try {
    // Подключение к PostgreSQL
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✅ Подключение к базе данных успешно\n";
    
    // Читаем JSON файл
    $jsonFile = 'b2b-project/frontend/cats.json';
    if (!file_exists($jsonFile)) {
        throw new Exception("Файл {$jsonFile} не найден");
    }
    
    $jsonContent = file_get_contents($jsonFile);
    $categories = json_decode($jsonContent, true);
    
    if (!$categories) {
        throw new Exception("Ошибка при парсинге JSON файла");
    }
    
    echo "✅ JSON файл загружен, найдено " . count($categories) . " категорий\n";
    
    // Статистика
    $stats = [
        'categories_updated' => 0,
        'categories_errors' => 0,
        'subcategories_updated' => 0,
        'subcategories_errors' => 0,
        'categories_not_found' => 0,
        'subcategories_not_found' => 0
    ];
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    echo "\n=== Обработка категорий и подкатегорий ===\n";
    
    foreach ($categories as $category) {
        $categoryId = $category['category_id'];
        
        // Обновление категории (если есть поле name_china)
        if (isset($category['name_china']) && !empty($category['name_china'])) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE categories SET name_china = :name_china WHERE category_id = :category_id"
                );
                $result = $stmt->execute([
                    'name_china' => $category['name_china'],
                    'category_id' => $categoryId
                ]);
                
                if ($stmt->rowCount() > 0) {
                    $stats['categories_updated']++;
                    echo "✅ Категория '{$categoryId}': обновлен китайский перевод\n";
                } else {
                    $stats['categories_not_found']++;
                    echo "⚠️  Категория '{$categoryId}': не найдена в базе данных\n";
                }
            } catch (Exception $e) {
                $stats['categories_errors']++;
                echo "❌ Ошибка при обновлении категории '{$categoryId}': " . $e->getMessage() . "\n";
            }
        }
        
        // Обработка подкатегорий
        if (isset($category['subcategories']) && is_array($category['subcategories'])) {
            foreach ($category['subcategories'] as $subcategory) {
                $subcategoryId = $subcategory['subcategory_id'];
                
                if (isset($subcategory['name_china']) && !empty($subcategory['name_china'])) {
                    try {
                        $stmt = $pdo->prepare(
                            "UPDATE subcategories SET name_china = :name_china WHERE subcategory_id = :subcategory_id"
                        );
                        $result = $stmt->execute([
                            'name_china' => $subcategory['name_china'],
                            'subcategory_id' => $subcategoryId
                        ]);
                        
                        if ($stmt->rowCount() > 0) {
                            $stats['subcategories_updated']++;
                            echo "✅ Подкатегория '{$subcategoryId}': обновлен китайский перевод\n";
                        } else {
                            $stats['subcategories_not_found']++;
                            echo "⚠️  Подкатегория '{$subcategoryId}': не найдена в базе данных\n";
                        }
                    } catch (Exception $e) {
                        $stats['subcategories_errors']++;
                        echo "❌ Ошибка при обновлении подкатегории '{$subcategoryId}': " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "⚠️  Подкатегория '{$subcategoryId}': пустой или отсутствующий китайский перевод\n";
                }
            }
        }
    }
    
    // Коммитим транзакцию
    $pdo->commit();
    echo "\n✅ Транзакция успешно завершена\n";
    
    // Выводим статистику
    echo "\n=== СТАТИСТИКА ОБНОВЛЕНИЯ ===\n";
    echo "📊 Категории:\n";
    echo "   - Обновлено: {$stats['categories_updated']}\n";
    echo "   - Не найдено в БД: {$stats['categories_not_found']}\n";
    echo "   - Ошибок: {$stats['categories_errors']}\n";
    
    echo "\n📊 Подкатегории:\n";
    echo "   - Обновлено: {$stats['subcategories_updated']}\n";
    echo "   - Не найдено в БД: {$stats['subcategories_not_found']}\n";
    echo "   - Ошибок: {$stats['subcategories_errors']}\n";
    
    echo "\n🎉 Скрипт завершен успешно!\n";
    
} catch (Exception $e) {
    // Откатываем транзакцию в случае ошибки
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollback();
        echo "\n❌ Транзакция отменена из-за ошибки\n";
    }
    
    echo "❌ КРИТИЧЕСКАЯ ОШИБКА: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
    exit(1);
}
?> 